<?php

namespace romanzipp\ProjectableAggregates\Contracts;

interface ProvidesProjectableAggregatesContract extends ProjectableAggregateContract
{
    public static function created($callback);

    public static function deleting($callback);
}
