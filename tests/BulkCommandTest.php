<?php

namespace romanzipp\ProjectableAggregates\Tests;

use Illuminate\Support\Facades\Bus;
use romanzipp\ProjectableAggregates\Jobs\CalculateBulkAggregatesJob;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;
use romanzipp\ProjectableAggregates\Tests\Support\PivotConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProvider;
use romanzipp\ProjectableAggregates\Tests\Support\PivotProviderConsumerPivot;

class BulkCommandTest extends TestCase
{
    public function testCommand()
    {
        $registry = app(ProjectableAggregateRegistry::class);
        $registry->registerConsumers([PivotConsumer::class]);
        $registry->registerProviders([PivotProvider::class]);

        PivotProviderConsumerPivot::query()->create([
            'consumer_id' => ($consumer = PivotConsumer::query()->create()->refresh())->id,
            'provider_id' => ($provider = PivotProvider::query()->create())->id,
        ]);

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(0, $consumer->projection_providers_count);

        // Command -----------------------------------------------------
        $this->artisan('aggregates:bulk-aggregate')->assertSuccessful();
        // -------------------------------------------------------------

        $consumer->refresh();

        self::assertSame(1, $consumer->providers()->count());
        self::assertSame(1, $consumer->projection_providers_count);
    }

    public function testQueued()
    {
        $bus = Bus::fake();

        $this->artisan('aggregates:bulk-aggregate --queued');

        $bus->assertDispatched(
            CalculateBulkAggregatesJob::class
        );
    }

    public function testQueuedOnOtherQueue()
    {
        $bus = Bus::fake();

        $this->artisan('aggregates:bulk-aggregate --queued --queue=foobar');

        $bus->assertDispatched(
            CalculateBulkAggregatesJob::class,
            fn (CalculateBulkAggregatesJob $job) => 'foobar' === $job->queue
        );
    }
}
