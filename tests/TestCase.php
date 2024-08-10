<?php

namespace romanzipp\ProjectableAggregates\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setupDatabase($this->app);
    }

    protected function setupDatabase(Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->dropAllTables();

        $app['db']->connection()->getSchemaBuilder()->create('basic_consumers', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('projection_providers_count')->default(0);
        });

        $app['db']->connection()->getSchemaBuilder()->create('basic_providers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('consumer_id')->unsigned();
        });
    }
}
