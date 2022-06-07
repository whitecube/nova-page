<?php

namespace Whitecube\NovaPage;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
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
    }

    public function register()
    {
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::middleware(['nova', Authorize::class])
            ->group(__DIR__.'/../routes/api.php');
    }
}
