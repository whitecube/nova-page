<?php

use Illuminate\Support\Facades\Route;
use Whitecube\NovaPage\Http\Controllers\CardController;
use Whitecube\NovaPage\Http\Controllers\ResourceIndexController;
use Whitecube\NovaPage\Http\Controllers\ResourceCountController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Actions...
Route::get('/nova-api/nova-page/actions', function() {
    return collect();
});

// Filters...
Route::get('/nova-api/nova-page/filters', function() {
    return collect();
});

// Lenses...
Route::get('/nova-api/nova-page/lenses', function() {
    return collect();
});

// Cards / Metrics...
Route::get('/nova-api/nova-page/cards', CardController::class . '@index');

// Resource Management...
Route::get('/nova-api/nova-page', ResourceIndexController::class . '@handle');
Route::get('/nova-api/nova-page/count', ResourceCountController::class . '@show');
