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
    $routes = [];
    foreach (Route::getRoutes()->getRoutesByName() as $route_name => $route) {
        if (str($route_name)->contains('v1')) {
            $routes[] = url($route->uri());
        }
    }

    return response()->json([
        'version' => 1,
        'routes' => $routes,
    ]);
})->name('index');

Route::middleware('cache.headers:public;max_age=129600;etag')->group(function () {
    Route::group(['prefix' => 'resources', 'as' => 'resources.'], function () {
        Route::get('anime/{source}', [AnimeResourceController::class, 'main'])->name('anime');
    });
});
