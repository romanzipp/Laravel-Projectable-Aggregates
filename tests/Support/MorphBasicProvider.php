<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

/**
 * @property int $id
 * @property int $consumer_id
 * @property string $consumer_type
 */
class MorphBasicProvider extends Model implements ProvidesProjectableAggregatesContract
{
    public $table = 'basic_morph_providers';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\romanzipp\ProjectableAggregates\Tests\Support\MorphBasicConsumer, $this>
     */
    #[ProvidesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function consumer(): MorphTo
    {
        /** @phpstan-ignore-next-line */
        return $this->morphTo();
    }
}
