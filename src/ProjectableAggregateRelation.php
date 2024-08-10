<?php

namespace romanzipp\ProjectableAggregates;

use romanzipp\ProjectableAggregates\Contracts\ProjectableAggregateContract;

final readonly class ProjectableAggregateRelation
{
    public function __construct(
        public ProjectableAggregateContract $related,
        public string $relationName,
        public string $projectionAttribute,
        public int $projectionType
    ) {
    }
}
