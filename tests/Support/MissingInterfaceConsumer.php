<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

class MissingInterfaceConsumer extends Model
{
    public $table = 'basic_consumers';

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\romanzipp\ProjectableAggregates\Tests\Support\MissingInterfaceProvider, $this>
     */
    #[ConsumesProjectableAggregate(
        projectionAttribute: 'projection_providers_count',
        type: ProjectionAggregateType::TYPE_COUNT,
    )]
    public function provider(): HasMany
    {
        return $this->hasMany(MissingInterfaceProvider::class);
    }
}
