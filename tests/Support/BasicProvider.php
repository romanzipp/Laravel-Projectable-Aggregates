<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

/**
 * @property int $id
 * @property int $projection_providers_count
 */
class BasicProvider extends Model implements ProvidesProjectableAggregatesContract
{
    public $table = 'basic_providers';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\romanzipp\ProjectableAggregates\Tests\Support\BasicConsumer, $this>
     */
    #[ProvidesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function consumer(): BelongsTo
    {
        return $this->belongsTo(BasicConsumer::class);
    }
}
