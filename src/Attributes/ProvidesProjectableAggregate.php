<?php

namespace romanzipp\ProjectableAggregates\Attributes;

use romanzipp\ProjectableAggregates\ProjectionAggregateType;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ProvidesProjectableAggregate
{
    public function __construct(
        public string $projectionAttribute,
        public int $type = ProjectionAggregateType::TYPE_COUNT,
        public ?string $targetAttribute = null,
    ) {
    }
}
