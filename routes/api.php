<?php

use Illuminate\Support\Facades\Route;
use Whitecube\NovaPage\Http\Controllers\CardController;

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

Route::get('/nova-api/nova-page/cards', CardController::class . '@index');
Route::get('/nova-api/nova-page/filters', function() {
    return response()->json([]);
});
Route::get('/nova-api/nova-page/lenses', function() {
    return response()->json([]);
});
Route::get('/nova-api/nova-page/actions', function() {
    return response()->json([]);
});