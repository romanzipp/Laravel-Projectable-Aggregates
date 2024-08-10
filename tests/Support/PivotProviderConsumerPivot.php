<?php

namespace romanzipp\ProjectableAggregates\Tests\Support;

use Illuminate\Database\Eloquent\Model;
use romanzipp\ProjectableAggregates\Contracts\ProvidesProjectableAggregatesContract;

/**
 * @property int $id
 * @property int $consumer_id
 * @property int $provider_id
 */
class PivotProviderConsumerPivot extends Model implements ProvidesProjectableAggregatesContract
{
    public $table = 'pivot_provider_consumer_pivot';

    public $timestamps = false;

    protected $guarded = [];
}
