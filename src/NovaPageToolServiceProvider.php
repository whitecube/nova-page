<?php

namespace Whitecube\NovaPage;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Whitecube\NovaPage\Http\Middleware\Authorize;

class NovaPageToolServiceProvider extends ServiceProvider
{

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
