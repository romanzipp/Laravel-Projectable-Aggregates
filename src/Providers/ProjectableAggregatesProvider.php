<?php

namespace romanzipp\ProjectableAggregates\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use romanzipp\ProjectableAggregates\ProjectableAggregateRegistry;

class ProjectableAggregatesProvider extends ServiceProvider
{
    public function boot(): void
    {
    }

    public function register(): void
    {
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
