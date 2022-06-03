<?php

namespace Whitecube\NovaPage;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Template;
use Whitecube\NovaPage\Http\Middleware\Authorize;

class NovaPageToolServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }
        // Nova::router(['nova', Authorize::class], 'resources')
        //     ->group(__DIR__.'/../routes/api.php');
    }

    public function register()
    {
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::middleware(['nova', Authorize::class])
            ->group(
                __DIR__.'/../routes/api.php'
            );
    }
}
