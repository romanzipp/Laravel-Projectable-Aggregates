<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ConsumesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

/**
 * @property int $id
 * @property int $projection_providers_count
 */
class MorphBasicConsumer extends Model implements ConsumesProjectableAggregatesContract
{
    public $table = 'basic_morph_consumers';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\romanzipp\ProjectableAggregates\Tests\Support\MorphBasicProvider, $this>
     */
    #[ConsumesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function providers(): MorphMany
    {
        return $this->morphMany(MorphBasicProvider::class, 'consumer');
    }
}
