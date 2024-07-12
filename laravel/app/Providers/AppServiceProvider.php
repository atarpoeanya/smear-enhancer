<?php

namespace App\Providers;

use App\Services\HouseKeeping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(HouseKeeping::class, function ($app) {
            return new HouseKeeping;
        });

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Cache::has('directories_checked')) {
            $houseKeeping = $this->app->make(HouseKeeping::class);
            $houseKeeping->checkFolder();

            // Set the cache flag to indicate the directories have been checked.
            Cache::forever('directories_checked', true);
        }
    }
}
