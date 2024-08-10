<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

/**
 * @property int $id
 * @property int $projection_providers_count
 */
class BasicConsumer extends Model implements ConsumesProjectableAggregatesContract
{
    public $table = 'basic_consumers';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\romanzipp\ProjectableAggregates\Tests\Support\BasicProvider, $this>
     */
    #[ConsumesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function providers(): HasMany
    {
        return $this->hasMany(BasicProvider::class, 'consumer_id');
    }
}
