<?php

namespace Oaktree;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Oaktree\Commands\Build;
use Oaktree\Middleware\JitCompile;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Compiler::class, function ($app) {
            return new Compiler(...(new RuleLoader)->getRules());
        });

        $this->mergeConfigFrom(
            __DIR__ . '/../config/oaktree.php',
            'oaktree'
        );
    }

    public function boot(Router $router): void
    {
        $this->commands(Build::class);
        $router->pushMiddlewareToGroup('web', JitCompile::class);
    }
}
