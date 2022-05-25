<?php

namespace App\Providers;

use App\Models\CatalogAd;
use App\Observers\CatalogAdObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        \DB::listen(function ($query) {
//            var_dump([$query->sql, $query->time / 1000 . ' ms']);
//            var_dump([$query->bindings]);
//        });
        CatalogAd::observe(CatalogAdObserver::class);
        URL::forceScheme('https');
    }
}
