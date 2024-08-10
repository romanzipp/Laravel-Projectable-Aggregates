<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

/**
 * @property int $id
 */
class PivotProvider extends Model implements ProvidesProjectableAggregatesContract
{
    public $table = 'pivot_providers';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<\romanzipp\ProjectableAggregates\Tests\Support\PivotConsumer, \romanzipp\ProjectableAggregates\Tests\Support\PivotProviderConsumerPivot, $this>
     */
    #[ProvidesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function consumer(): HasOneThrough
    {
        return $this->hasOneThrough(
            PivotConsumer::class,
            PivotProviderConsumerPivot::class,
            'consumer_id',
            'id',
            'id',
            'consumer_id'
        );
    }
}
