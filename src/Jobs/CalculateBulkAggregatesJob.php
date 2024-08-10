<?php

namespace romanzipp\ProjectableAggregates\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;

final class CalculateBulkAggregatesJob
{
    use SerializesModels;
    use Queueable;

    /**
     * @param array<class-string> $consumerClasses
     */
    public function __construct(
        public array $consumerClasses = []
    ) {
    }

    public function handle(): void
    {
        $registry = app(ProjectableAggregateRegistry::class);
        $registry->bulkAggregate();
    }
}
