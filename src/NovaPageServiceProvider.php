<?php

namespace Whitecube\NovaPage;

use Illuminate\Support\ServiceProvider;
use Whitecube\NovaPage\Page\Manager;
use Whitecube\NovaPage\Page\Container;

class NovaPageServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'novapage');
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager();
        });
        $this->app->bind(Container::class, function($app) {
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
        $this->publishes([
            __DIR__ . '/config.php' => config_path('novapage.php')
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Manager::class];
    }

}