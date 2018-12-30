<?php

namespace Whitecube\NovaPage;

use Illuminate\Support\ServiceProvider;
use Whitecube\NovaPage\Page\Manager;

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
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager([
                'sources' => [
                    'files' => [
                        'directory' => resource_path('lang/en/static')
                    ]
                ]
            ]);
        });
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