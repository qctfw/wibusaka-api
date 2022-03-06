<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::get('/', function () {
                $v = config('app.version');

                return response()->json([
                    'current_version' => $v,
                    'current_version_url' => config('app.url') . '/v' . explode('.', $v)[0],
                    'api_documentations' => config('app.api_docs_url'),
                    'home' => config('app.home_url'),
                    'discord' => config('app.discord_url'),
                ]);
            });

            Route::middleware('api')->group(function () {
                Route::prefix('v1')
                ->namespace($this->namespace)
                ->as('v1.')
                ->group(base_path('routes/api.v1.php'));
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
