<?php

namespace romanzipp\ProjectableAggregates\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use romanzipp\ProjectableAggregates\Command\CalculateBulkAggregatesCommand;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;

class ProjectableAggregatesProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            dirname(__DIR__) . '/../config/projectable-aggregates.php' => config_path('projectable-aggregates.php'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CalculateBulkAggregatesCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/../config/projectable-aggregates.php',
            'projectable-aggregates'
        );

        $this->app->singleton(ProjectableAggregateRegistry::class, function (Application $app) {
            return new ProjectableAggregateRegistry();
        });
    }

    /**
     * @return array<class-string>
     */
    public function provides(): array
    {
        return [
            ProjectableAggregateRegistry::class,
        ];
    }
}
