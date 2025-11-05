<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\Transport\KkuApiTransport;
use App\Services\KKUApiService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
protected $policies = [
    \App\Models\Indicator::class => \App\Policies\IndicatorPolicy::class,
];
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('global_setting', Setting::first());
        });

        // Register custom KKU mail transport so existing Mailables work
        Mail::extend('kku', function (array $config = []) {
            return new KkuApiTransport(app(KKUApiService::class));
        });
    }
    
}
