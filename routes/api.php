<?php

use Illuminate\Support\Facades\Route;
use Whitecube\NovaPage\Http\Controllers\ActionController;
use Whitecube\NovaPage\Http\Controllers\CardController;
use Whitecube\NovaPage\Http\Controllers\FilterController;
use Whitecube\NovaPage\Http\Controllers\LensController;
use Whitecube\NovaPage\Http\Controllers\Page\IndexController as PageResourceIndexController;
use Whitecube\NovaPage\Http\Controllers\Page\CountController as PageResourceCountController;
use Whitecube\NovaPage\Http\Controllers\Page\UpdateController as PageResourceUpdateController;
use Whitecube\NovaPage\Http\Controllers\Page\FieldDestroyController as PageFieldDestroyController;
use Whitecube\NovaPage\Http\Controllers\Option\IndexController as OptionResourceIndexController;
use Whitecube\NovaPage\Http\Controllers\Option\CountController as OptionResourceCountController;
use Whitecube\NovaPage\Http\Controllers\Option\UpdateController as OptionResourceUpdateController;
use Whitecube\NovaPage\Http\Controllers\Option\FieldDestroyController as OptionFieldDestroyController;

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
Route::get('/nova-api/nova-option/actions', ActionController::class . '@index');

// Filters...
Route::get('/nova-api/nova-page/filters', FilterController::class . '@index');
Route::get('/nova-api/nova-option/filters', FilterController::class . '@index');

// Lenses...
Route::get('/nova-api/nova-page/lenses', LensController::class . '@index');
Route::get('/nova-api/nova-option/lenses', LensController::class . '@index');

// Cards / Metrics...
Route::get('/nova-api/nova-page/cards', CardController::class . '@index');
Route::get('/nova-api/nova-option/cards', CardController::class . '@index');

// Resource Management...
Route::get('/nova-api/nova-page', PageResourceIndexController::class . '@handle');
Route::get('/nova-api/nova-page/count', PageResourceCountController::class . '@show');
Route::put('/nova-api/nova-page/{resourceId}', PageResourceUpdateController::class . '@handle');
Route::delete('/nova-api/nova-page/{resourceId}/field/{field}', PageFieldDestroyController::class . '@handle');

Route::get('/nova-api/nova-option', OptionResourceIndexController::class . '@handle');
Route::get('/nova-api/nova-option/count', OptionResourceCountController::class . '@show');
Route::put('/nova-api/nova-option/{resourceId}', OptionResourceUpdateController::class . '@handle');
Route::delete('/nova-api/nova-option/{resourceId}/field/{field}', OptionFieldDestroyController::class . '@handle');
