<?php

namespace romanzipp\ProjectableAggregates\Command;

use Illuminate\Console\Command;
use romanzipp\ProjectableAggregates\Jobs\CalculateBulkAggregatesJob;

final class CalculateBulkAggregatesCommand extends Command
{
    protected $signature = 'aggregates:bulk-aggregate
                           {--queued : Push a job to the queue}
                           {--queue= : The queue to push the job to}
                           {--class=}';

    public function handle(): int
    {
        $classes = [];

        $job = new CalculateBulkAggregatesJob($classes);

        if ($this->option('queued')) {
            $this->info('Pushing aggregation to queue...');

            $pendingDispatch = dispatch($job);

            if ($this->hasOption('queue')) {
                $pendingDispatch->onQueue(
                    $this->option('queue')
                );
            }

            return 0;
        }

        $job->handle();

        return 0;
    }
}
