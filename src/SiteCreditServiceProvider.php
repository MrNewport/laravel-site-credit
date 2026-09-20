<?php

namespace MrNewport\SiteCredit;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class SiteCreditServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/site-credit.php', 'site-credit');
        $this->app->singleton(Credit::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'site-credit');
        Blade::component('site-credit', SiteCredit::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/site-credit.php' => config_path('site-credit.php'),
            ], 'site-credit-config');
            $this->publishes([
                __DIR__.'/../resources/css/site-credit.css' => public_path('vendor/site-credit/site-credit.css'),
            ], 'site-credit-assets');
        }
    }
}
