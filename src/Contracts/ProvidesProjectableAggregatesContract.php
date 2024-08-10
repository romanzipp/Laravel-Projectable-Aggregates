<?php

namespace romanzipp\ProjectableAggregates\Contracts;

interface ProvidesProjectableAggregatesContract extends ProjectableAggregateContract
{
    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public static function created($callback);

    /**
     * @param callable $callback
     *
     * @return mixed
     */
    public static function deleting($callback);
}
