<?php

namespace Whitecube\NovaPage;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Template;

class NovaPageServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the Container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'novapage');
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager();
        });
        $this->app->bind(Template::class, function($app) {
            return $app->make(Manager::class)->find();
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Route::mixin(new NovaPageRouteMacros());
        $this->publishes([
            __DIR__ . '/config.php' => config_path('novapage.php')
        ]);
    }

}