<?php

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Cache\Repository;
use Illuminate\Cache\ArrayStore;
use Illuminate\Database\Capsule\Manager as Capsule;
use Faker\Factory as FakerFactory;

require __DIR__ . '/../vendor/autoload.php';

// Set up service container
$container = new Container();
Facade::setFacadeApplication($container);

// Set up cache binding for facade + helper
$cache = new Repository(new ArrayStore);
$container->instance('cache', $cache);

// Optional: alias facade (if needed)
class_alias(\Illuminate\Support\Facades\Cache::class, 'Cache');
// Optional: alias Schema facade (if needed)
class_alias(\Illuminate\Support\Facades\Schema::class, 'Schema');

// Setup Eloquent (optional for model tests)
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Bind 'db' and 'db.schema' to the container for migrations
$container->instance('db', $capsule->getDatabaseManager());
$container->bind('db.schema', function ($app) use ($capsule) {
    return $capsule->schema();
});


$container->singleton(Faker\Generator::class, function () {
    return FakerFactory::create();
});

$migrationPath = __DIR__ . '/../database/migrations/create_email_tables.php';
$migrationClass = new (require $migrationPath)();
$migrationClass->up();

function dropTables()
{
    global $migrationClass;
    $migrationClass->down();
}
