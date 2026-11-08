<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Luinuxscl\LnxSam\SamAccess;
use Luinuxscl\LnxSam\SamRegistry;

/*
 * The runtime resolves contributors through the container, exactly as it does
 * in a host application. The tests only need that container: no database, no
 * HTTP kernel and no model gateway are involved in a declaration.
 */
$container = new Container;
Container::setInstance($container);

// Mirrors LnxSamServiceProvider: the runtime resolves one registry and one
// access gate per request, so a tool and the host see the same declarations.
$container->singleton(SamRegistry::class);
$container->singleton(SamAccess::class);

if (! function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        $container = Container::getInstance();

        return $abstract === null ? $container : $container->make($abstract);
    }
}

require __DIR__.'/helpers.php';
