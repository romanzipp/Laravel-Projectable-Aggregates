<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

class MissingInterfaceProvider extends Model
{
    public $table = 'basic_providers';

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\romanzipp\ProjectableAggregates\Tests\Support\MissingInterfaceConsumer, $this>
     */
    #[ProvidesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function consumers(): BelongsTo
    {
        return $this->belongsTo(MissingInterfaceConsumer::class);
    }
}
