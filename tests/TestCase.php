<?php

namespace romanzipp\ProjectableAggregates\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use romanzipp\ProjectableAggregates\Providers\ProjectableAggregatesProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ProjectableAggregatesProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();

        config(['projectable-aggregates.use_transactions' => false]);

        $this->setupDatabase($this->app);
    }

    protected function setupDatabase(Application $app): void
    {
        $app['db']->connection()->getSchemaBuilder()->dropAllTables();

        // Basic: HasMany <-> BelongsTo

        $app['db']->connection()->getSchemaBuilder()->create('basic_consumers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('projection_providers_count')->default(0);
        });

        $app['db']->connection()->getSchemaBuilder()->create('basic_providers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consumer_id')->unsigned();
        });

        // Basic-Morph: HasMany <-> BelongsTo

        $app['db']->connection()->getSchemaBuilder()->create('basic_morph_consumers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('projection_providers_count')->default(0);
        });

        $app['db']->connection()->getSchemaBuilder()->create('basic_morph_providers', function (Blueprint $table) {
            $table->id();
            $table->morphs('consumer');
        });

        // Pivot: HasManyThrough <-> HasOneThrough

        $app['db']->connection()->getSchemaBuilder()->create('pivot_consumers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('projection_providers_count')->default(0);
        });

        $app['db']->connection()->getSchemaBuilder()->create('pivot_providers', function (Blueprint $table) {
            $table->id();
        });

        $app['db']->connection()->getSchemaBuilder()->create('pivot_provider_consumer_pivot', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consumer_id')->unsigned();
            $table->bigInteger('provider_id')->unsigned();
        });
    }
}
