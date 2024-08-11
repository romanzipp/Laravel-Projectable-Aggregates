<?php

namespace romanzipp\ProjectableAggregates;

use romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;

final readonly class ProjectableAggregateRelation
{
    public function __construct(
        public string $relationName,
        public string $projectionAttribute,
        public int $projectionType,
        public ?ConsumesProjectableAggregatesContract $consumer = null,
        public ?ProvidesProjectableAggregatesContract $provider = null,
        public ?string $targetAttribute = null,
    ) {
    }
}
