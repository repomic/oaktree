<?php

namespace Oaktree;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Oaktree\Commands\Build;
use Oaktree\Middleware\JitCompile;
use Oaktree\Rules\AnchorRule;
use Oaktree\Rules\BackgroundRule;
use Oaktree\Rules\BorderRule;
use Oaktree\Rules\ButtonRule;
use Oaktree\Rules\ContainerRule;
use Oaktree\Rules\EffectRule;
use Oaktree\Rules\FiltersRule;
use Oaktree\Rules\FlexboxGridRule;
use Oaktree\Rules\FormRule;
use Oaktree\Rules\InteractivityRule;
use Oaktree\Rules\LayoutRule;
use Oaktree\Rules\ListRule;
use Oaktree\Rules\MessageRule;
use Oaktree\Rules\SizingRule;
use Oaktree\Rules\SpacingRule;
use Oaktree\Rules\TableRule;
use Oaktree\Rules\TransformRule;
use Oaktree\Rules\TransitionsAnimationRule;
use Oaktree\Rules\TypographyRule;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Compiler::class, function ($app) {
            // Default core Rules
            $rules = [
                new LayoutRule,
                new FlexboxGridRule,
                new TransformRule,
                new TransitionsAnimationRule,
                new SizingRule,
                new SpacingRule,
                new BackgroundRule,
                new TypographyRule,
                new BorderRule,
                new ListRule,
                new FormRule,
                new EffectRule,
                new FiltersRule,
                new InteractivityRule,
                new ContainerRule,
                new AnchorRule,
                new TableRule,

                // components
                new ButtonRule,
                new MessageRule,
            ];

            // Allow extending via config or container bindings if needed
            return new Compiler(...$rules);
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
