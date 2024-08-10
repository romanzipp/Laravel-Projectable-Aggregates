<?php

namespace romanzipp\ProjectableAggregates\Tests;

use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;
use romanzipp\ProjectableAggregates\Tests\Support\MissingInterfaceConsumer;
use romanzipp\ProjectableAggregates\Tests\Support\MissingInterfaceProvider;

class ImplementationTest extends TestCase
{
    public function testMissingConsumerInterface(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Class romanzipp\ProjectableAggregates\Tests\Support\MissingInterfaceConsumer must implement {StoresProjectableAggregatesContract}');

        $registry = app(ProjectableAggregateRegistry::class);
        $registry->registerConsumers([MissingInterfaceConsumer::class]);
    }

    public function testMissingProviderInterface(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Class romanzipp\ProjectableAggregates\Tests\Support\MissingInterfaceProvider must implement {ProvidesProjectableAttributesContract}');

        $registry = app(ProjectableAggregateRegistry::class);
        $registry->registerProvider([MissingInterfaceProvider::class]);
    }
}
