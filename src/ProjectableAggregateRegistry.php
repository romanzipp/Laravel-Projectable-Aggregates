<?php

namespace romanzipp\ProjectableAggregates;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate;
use romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\Contracts\ProjectableAggregateContract;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;

class ProjectableAggregateRegistry
{
    /**
     * @var array<class-string>
     */
    public array $registeredProviderClasses = [];

    /**
     * @var array<class-string>
     */
    public array $registeredConsumerClasses = [];

    /*
     *--------------------------------------------------------------------------
     * Consumers
     *--------------------------------------------------------------------------
     */

    public function registerConsumers(array $consumerClasses): void
    {
        foreach ($consumerClasses as $consumerClass) {
            $this->registerSingleConsumer($consumerClass);
        }
    }

    public function registerSingleConsumer(string $consumerClass): void
    {
        $reflectionClass = new \ReflectionClass($consumerClass);
        $reflectionInstance = $reflectionClass->newInstanceWithoutConstructor();

        if ( ! $reflectionInstance instanceof ConsumesProjectableAggregatesContract) {
            throw new \RuntimeException("Class {$consumerClass} must implement {StoresProjectableAggregatesContract}");
        }

        if (in_array($consumerClass, $this->registeredProviderClasses)) {
            return;
        }

        $this->registeredConsumerClasses[] = $consumerClass;
    }

    /**
     * @throws \ReflectionException
     *
     * @return \Generator<\romanzipp\ProjectableAggregates\ProjectableAggregateRelation>
     */
    public function discoverConsumingRelations(ProjectableAggregateContract $consumer): \Generator
    {
        $reflectionClass = new \ReflectionClass($consumer);
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes(ConsumesProjectableAggregate::class) as $reflectionAttribute) {
                /** @var \romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate $projectionAttribute */
                $projectionAttribute = $reflectionAttribute->newInstance();

                if ( ! $consumer instanceof ConsumesProjectableAggregatesContract) {
                    throw new \RuntimeException("Class {$reflectionClass->getName()} must implement {ConsumesProjectableAggregatesContract}");
                }

                yield new ProjectableAggregateRelation(
                    related: $consumer,
                    relationName: $reflectionMethod->getName(),
                    projectionAttribute: $projectionAttribute->projectionAttribute,
                    projectionType: $projectionAttribute->type,
                );
            }
        }
    }

    /*
     *--------------------------------------------------------------------------
     * Related
     *--------------------------------------------------------------------------
     */

    public function registerProvider(array $providerClasses): void
    {
        foreach ($providerClasses as $providerClass) {
            $this->registerSingleProvider($providerClass);
        }
    }

    private function registerSingleProvider(string $providerClass): void
    {
        $reflectionClass = new \ReflectionClass($providerClass);
        $reflectionInstance = $reflectionClass->newInstanceWithoutConstructor();

        if ( ! $reflectionInstance instanceof ProvidesProjectableAggregatesContract) {
            throw new \RuntimeException("Class {$providerClass} must implement {ProvidesProjectableAttributesContract}");
        }

        if (in_array($providerClass, $this->registeredProviderClasses)) {
            return;
        }

        $reflectionInstance::created(fn (ProvidesProjectableAggregatesContract $provider) => $this->onRelatedModelEvent($provider, 1));
        $reflectionInstance::deleting(fn (ProvidesProjectableAggregatesContract $provider) => $this->onRelatedModelEvent($provider, -1));

        $this->registeredProviderClasses[] = $providerClass;
    }

    /**
     * @throws \ReflectionException
     *
     * @return \Generator<\romanzipp\ProjectableAggregates\ProjectableAggregateRelation>
     */
    public function discoverProvidingRelations(ProjectableAggregateContract $provider): \Generator
    {
        $reflectionClass = new \ReflectionClass($provider);
        $reflectionMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($reflectionMethods as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes(ProvidesProjectableAggregate::class) as $reflectionAttribute) {
                /** @var \romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate $projectionAttribute */
                $projectionAttribute = $reflectionAttribute->newInstance();

                /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
                $relation = $reflectionMethod->invoke($provider);

                // Check if the consumer (model that receives the projection attribute updates) implements the interface

                $consumer = $relation->getRelated();

                if ( ! $consumer instanceof ConsumesProjectableAggregatesContract) {
                    throw new \RuntimeException("Class {$reflectionClass->getName()} must implement {StoresProjectableAggregatesContract}");
                }

                yield new ProjectableAggregateRelation(
                    related: $provider,
                    relationName: $reflectionMethod->getName(),
                    projectionAttribute: $projectionAttribute->projectionAttribute,
                    projectionType: $projectionAttribute->type,
                );
            }
        }
    }

    /*
     *--------------------------------------------------------------------------
     * Bulk Aggregation (Consumers -> Related)
     *--------------------------------------------------------------------------
     */

    public function bulkAggregate(): void
    {
        foreach ($this->registeredConsumerClasses as $consumerClass) {
            /** @var class-string<\romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract> $consumerClass */
            $this->aggregateConsumer(
                new $consumerClass()
            );
        }
    }

    private function aggregateConsumer(ConsumesProjectableAggregatesContract $consumer): void
    {
        $consumer->newQuery()->each(function (ConsumesProjectableAggregatesContract $consumer) {
            $updateDatabaseCallback = function () use ($consumer) {
                foreach ($this->discoverConsumingRelations($consumer) as $projectableRelation) {
                    // dd($projectableRelation);
                    $this->updateAggregateAttributes($projectableRelation);
                }
            };

            if (config('projectable-aggregates.use_transactions')) {
                DB::transaction($updateDatabaseCallback);
            } else {
                $updateDatabaseCallback();
            }
        });
    }

    private function updateAggregateAttributes(ProjectableAggregateRelation $projectableRelation): void
    {
        /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
        $relation = $projectableRelation->related->{$projectableRelation->relationName}();

        // Fetch the aggregated value to be stored in the projection attribute

        $aggregateValue = self::getAggregatedValue($projectableRelation, $relation);

        $projectableRelation->related->update([
            $projectableRelation->projectionAttribute => $aggregateValue,
        ]);
    }

    /*
     *--------------------------------------------------------------------------
     * Model Events (Consumers -> Related)
     *--------------------------------------------------------------------------
     */

    /**
     * Executed once a created/deleted events has been fired from the related model.
     *
     * @param \romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract $provider
     * @param int $countOffset
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    private function onRelatedModelEvent(ProvidesProjectableAggregatesContract $provider, int $countOffset): void
    {
        // Search for all relation methods which have a ProjectsAggregateTo attribute attached

        foreach ($this->discoverProvidingRelations($provider) as $projectableRelation) {
            $this->adjustAggregateAttributes($projectableRelation, $countOffset);
        }
    }

    private function adjustAggregateAttributes(ProjectableAggregateRelation $projectableRelation, int $countOffset): void
    {
        /** @var \Illuminate\Database\Eloquent\Relations\Relation $relation */
        $relation = $projectableRelation->related->{$projectableRelation->relationName}();

        $updateDatabaseCallback = function () use ($relation, $projectableRelation, $countOffset) {
            /** @phpstan-ignore-next-line */
            $relation
                ->newQuery()
                ->each(function ($consumer) use ($projectableRelation, $countOffset) {
                    $consumer->increment($projectableRelation->projectionAttribute, $countOffset);

                    // dump('updating ' . $consumer::class . ' (' . $consumer->id . ') attribute (relation ' . $projectableRelation->relationName . ') ' . $projectableRelation->projectionAttribute . ' to ' . $aggregateValue);
                });
        };

        if (config('projectable-aggregates.use_transactions')) {
            DB::transaction($updateDatabaseCallback);
        } else {
            $updateDatabaseCallback();
        }
    }

    /*
     *--------------------------------------------------------------------------
     * General
     *--------------------------------------------------------------------------
     */

    private static function getAggregatedValue(ProjectableAggregateRelation $projectableRelation, Relation $relation): int
    {
        return match ($projectableRelation->projectionType) {
            ProjectionAggregateType::TYPE_COUNT => $relation->count(),
            ProjectionAggregateType::TYPE_SUM => $relation->sum(),
            default => throw new \RuntimeException("Unknown projection type {$projectableRelation->projectionType}"),
        };
    }
}
