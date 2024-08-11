<?php

namespace romanzipp\ProjectableAggregates\Events;

use Illuminate\Foundation\Events\Dispatchable;
use romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;

final class UpdateProjectableAggregatesEvent
{
    use Dispatchable;

    public function __construct(
        public ProvidesProjectableAggregatesContract $provider,
        public ConsumesProjectableAggregatesContract $consumer,
        public string $relationName,
        public string $projectionAttribute,
        public int $projectionType,
        public ?string $targetAttribute = null,
    ) {
    }
}
