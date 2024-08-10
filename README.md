# Laravel Projectable Aggregates

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/Laravel-Projectable-Aggregates.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-projectable-aggregates)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/Laravel-Projectable-Aggregates.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-projectable-aggregates)
[![License](https://img.shields.io/packagist/l/romanzipp/Laravel-Projectable-Aggregates.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-projectable-aggregates)
[![GitHub Build Status](https://img.shields.io/github/actions/workflow/status/romanzipp/Laravel-Projectable-Aggregates/tests.yml?branch=master&style=flat-square)](https://github.com/romanzipp/Laravel-Projectable-Aggregates/actions)

## What

Laravel Projectable Aggregates is a package that allows you to **easily storage aggregate values like counts, sums, averages**, etc. in your models eliminating the need to **calculate these values on the fly** (with `withCount`, `withStum`, `withAvg`, etc.).

## Installation

```bash
composer require romanzipp/laravel-projectable-aggregates
```

## Terminology

#### 🟢 Consumers 

Consumers hold the projectable aggregate database field. This is the model which otherwise would calculate the relationship fields via `withCount`, `withStum`, `withAvg`, etc.

#### 🔵 Providers

Providing models provide (duh) the aggregate values for the consumer. Think of the provider to exist many times for one consumer.

![](art/diagram.png)

## Usage

Let's continue with the example of a `Car` model with `Door` models.

### 1. Add a Projection Field to DB

```php
new class() extends Migration
{
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('projection_doors_count')->default(0);
        });
    }
}
```

### 2. Update your Models

#### 🟢 Car (Consumer)

```php
use romanzipp\ProjectableAggregates\Attributes\ConsumesProjectableAggregate;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

class Car extends Model
{
    #[ConsumesProjectableAggregate(
        projectionAttribute: 'project_doors_count',   // <- Name of the projection field in the database
        projectionType: ProjectionAggregateType::TYPE_COUNT
    )]
    public function doors(): HasMany
    {
        return $this->hasMany(Door::class);
    }
}
```

#### 🔵 Door (Provider)

```php
use romanzipp\ProjectableAggregates\Attributes\ProvidesProjectableAggregate;
use romanzipp\ProjectableAggregates\ProjectionAggregateType;

class Door extends Model
{
    #[ProvidesProjectableAggregate(
        projectionAttribute: 'project_doors_count',   // <- Name of the FOREIGN projection field in the database
        projectionType: ProjectionAggregateType::TYPE_COUNT
    )]
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
```

## Testing

This repository contains a [Lando](https://lando.dev) configuration file that can be used to run the tests on your local machine.

```bash
lando start
```

```
lando phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
