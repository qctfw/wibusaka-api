<?php

use App\Http\Controllers\AnimeResourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/', function () {
    return response()->json(['version' => '1']);
});

Route::middleware('cache.headers:public;max_age=129600;etag')->group(function () {
    Route::group(['prefix' => 'resources'], function () {
        Route::get('anime/{source}', [AnimeResourceController::class, 'main']);
    });
});
