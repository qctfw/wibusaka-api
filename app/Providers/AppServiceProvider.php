<?php

namespace App\Providers;

use App\Services\Contracts\EntryServiceInterface;
use App\Services\Contracts\ResourceServiceInterface;
use App\Services\EntryService;
use App\Services\ResourceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        EntryServiceInterface::class => EntryService::class,
        ResourceServiceInterface::class => ResourceService::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
