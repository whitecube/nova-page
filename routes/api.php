<?php

use Illuminate\Support\Facades\Route;
use Whitecube\NovaPage\Http\Controllers\ActionController;
use Whitecube\NovaPage\Http\Controllers\CardController;
use Whitecube\NovaPage\Http\Controllers\FilterController;
use Whitecube\NovaPage\Http\Controllers\LensController;
use Whitecube\NovaPage\Http\Controllers\ResourceIndexController;
use Whitecube\NovaPage\Http\Controllers\ResourceCountController;
use Whitecube\NovaPage\Http\Controllers\ResourceUpdateController;

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
Route::get('/nova-api/nova-page/actions', ActionController::class . '@index');

// Filters...
Route::get('/nova-api/nova-page/filters', FilterController::class . '@index');

// Lenses...
Route::get('/nova-api/nova-page/lenses', LensController::class . '@index');

// Cards / Metrics...
Route::get('/nova-api/nova-page/cards', CardController::class . '@index');

// Resource Management...
Route::get('/nova-api/nova-page', ResourceIndexController::class . '@handle');
Route::get('/nova-api/nova-page/count', ResourceCountController::class . '@show');
Route::put('/nova-api/nova-page/{resourceId}', ResourceUpdateController::class . '@handle');
