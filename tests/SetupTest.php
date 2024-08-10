<?php

namespace romanzipp\ProjectableAggregates\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;
use romanzipp\ProjectableAggregates\Tests\Support\PivotConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProvider;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProviderConsumerPivot;

class SetupTest extends TestCase
{
    use RefreshDatabase;

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

        self::assertSame($provider->id, $consumer->providers()->first()->id);
        self::assertSame($consumer->id, $provider->consumer()->first()->id);
    }
}
