<?php

namespace Whitecube\NovaPage;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Whitecube\NovaPage\Commands\CreateTemplate;
use Whitecube\NovaPage\Pages\Manager;
use Whitecube\NovaPage\Pages\Template;
use Whitecube\NovaPage\Pages\TemplatesRepository;

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
            return new Manager($app->make(TemplatesRepository::class));
        });

        $this->app->bind(Template::class, function($app) {
            return $app->make(Manager::class)->find();
        });

        $this->registerBladeDirectives();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    public function registerBladeDirectives()
    {
        Blade::directive('get', function ($key) {
            $key = trim(trim($key, '"'), "'");
            return resolve(Manager::class)->get($key);
        });
    }

    public function registerCommands()
    {
        $this->commands([
            CreateTemplate::class
        ]);
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

        $this->app->booted(function() {
            $this->app->make(Manager::class)->booted();
        });
    }

}