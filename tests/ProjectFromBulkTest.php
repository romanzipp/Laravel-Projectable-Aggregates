<?php

namespace romanzipp\ProjectableAggregates\Tests;

use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;
use romanzipp\ProjectableAggregates\Tests\Support\PivotConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProvider;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProviderConsumerPivot;

class ProjectFromBulkTest extends TestCase
{
    public function testPivotModels(): void
    {
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
        self::assertSame(0, $consumer->projection_providers_count);

        $registry->bulkAggregate();

        $consumer->refresh();

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(1, $consumer->projection_providers_count);
    }
}
