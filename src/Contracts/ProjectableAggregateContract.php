<?php

namespace romanzipp\ProjectableAggregates\Contracts;

interface ProjectableAggregateContract
{
    /**
     * @return mixed
     */
    public function newQuery();

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $options
     *
     * @return mixed
     */
    public function update(array $attributes = [], array $options = []);
}
