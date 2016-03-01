<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Packback\Prices\Clients\ValoreBooksPriceClient as Client;
use App\Contracts\Search;
use App\Services\ValoreSearch;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    //protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Search::class, function ($app) {
            $config = config('services.valore');

            return new ValoreSearch(
                new Client($config)
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    // public function provides()
    // {
    //     return [Search::class];
    // }
}
