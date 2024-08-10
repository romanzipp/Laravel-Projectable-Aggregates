<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

/**
 * @property int $id
 * @property int $projection_providers_count
 */
class PivotConsumer extends Model implements ConsumesProjectableAggregatesContract
{
    public $table = 'pivot_consumers';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\romanzipp\ProjectableAggregates\Tests\Support\PivotProvider, \romanzipp\ProjectableAggregates\Tests\Support\PivotProviderConsumerPivot, $this>
     */
    #[ConsumesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function providers(): HasManyThrough
    {
        return $this->hasManyThrough(
            PivotProvider::class,
            PivotProviderConsumerPivot::class,
            'provider_id',
            'id',
            'id',
            'provider_id'
        );
    }
}
