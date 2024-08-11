<?php

namespace romanzipp\ProjectableAggregates\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use romanzipp\ProjectableAggregates\Events\UpdateProjectableAggregatesEvent;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;
use romanzipp\ProjectableAggregates\Tests\Support\BasicConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\BasicProvider;
use romanzipp\ProjectableAggregates\Tests\Support\MorphBasicConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\MorphBasicProvider;
use romanzipp\ProjectableAggregates\Tests\Support\PivotConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProvider;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProviderConsumerPivot;

class ProjectFromModelEventsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        if ('cli' === PHP_SAPI && ! defined('STDOUT')) {
            Event::listen(['eloquent.*'], function (string $eventName, array $data) {
                dump('[' . $this->name() . '] ' . $eventName);
            });

            Event::listen(['eloquent.created*', 'eloquent.deleting*'], function (string $eventName, array $data) {
                dump('[' . $this->name() . '] ' . $eventName);
            });
        }
    }

    /**
     * Provider::belongsTo() <-> Consumer::hasMany().
     *
     * @return void
     */
    public function testBasicModels(): void
    {
        $events = Event::fake([UpdateProjectableAggregatesEvent::class]);

        $registry = app(ProjectableAggregateRegistry::class);
        $registry->registerConsumers([BasicConsumer::class]);
        $registry->registerProviders([BasicProvider::class]);

        $consumer = BasicConsumer::query()->create();
        $consumer->refresh();

        self::assertSame(0, $consumer->projection_providers_count);

        // Create a provider model

        $providerOne = BasicProvider::query()->create([
            'consumer_id' => $consumer->id,
        ]);

        $consumer->refresh();

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(1, $consumer->projection_providers_count);

        // Create another provider model

        BasicProvider::query()->create([
            'consumer_id' => $consumer->id,
        ]);

        $consumer->refresh();

        self::assertSame(2, $consumer->providers()->count());
        self::assertSame(2, $consumer->projection_providers_count);

        // Delete the first provider model

        $providerOne->delete();

        $consumer->refresh();

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(1, $consumer->projection_providers_count);

        $events->assertDispatchedTimes(UpdateProjectableAggregatesEvent::class, 3);
    }

    /**
     * Provider::morphTo() <-> Consumer::morphMany().
     *
     * @return void
     */
    public function testBasicMorphModels()
    {
        $events = Event::fake([UpdateProjectableAggregatesEvent::class]);

        $registry = app(ProjectableAggregateRegistry::class);
        $registry->registerConsumers([MorphBasicConsumer::class]);
        $registry->registerProviders([MorphBasicProvider::class]);

        $consumer = MorphBasicConsumer::query()->create();
        $consumer->refresh();

        self::assertSame(0, $consumer->projection_providers_count);

        // Create a provider model

        $providerOne = MorphBasicProvider::query()->create([
            'consumer_id' => $consumer->id,
            'consumer_type' => get_class($consumer),
        ]);

        $consumer->refresh();

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(1, $consumer->projection_providers_count);
    }

    /**
     * Provider::hasOneThrough() <-> Consumer::hasManyThrough().
     *
     * @return void
     */
    public function testPivotModels(): void
    {
        $events = Event::fake([UpdateProjectableAggregatesEvent::class]);

        $registry = app(ProjectableAggregateRegistry::class);
        $registry->registerConsumers([PivotConsumer::class]);
        $registry->registerProviders([PivotProvider::class]);

        $consumer = PivotConsumer::query()->create();

        $provider = PivotProvider::query()->create();

        PivotProviderConsumerPivot::query()->create([
            'consumer_id' => $consumer->id,
            'provider_id' => $provider->id,
        ]);

        $consumer->refresh();

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(0, $consumer->projection_providers_count); // doesn't exist yet
    }
}
