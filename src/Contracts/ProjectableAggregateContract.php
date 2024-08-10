<?php

namespace romanzipp\ProjectableAggregates\Contracts;

interface ProjectableAggregateContract
{
    public function newQuery();

    public function update(array $attributes = [], array $options = []);
}
