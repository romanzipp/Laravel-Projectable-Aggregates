<?php

namespace romanzipp\ProjectableAggregates\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;
use romanzipp\ProjectableAggregates\Tests\Support\BasicConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\BasicProvider;

class ProjectFromModelEventsTest extends TestCase
{
    use RefreshDatabase;

    public function testBasicModels(): void
    {
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
    }
}
