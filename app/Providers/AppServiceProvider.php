<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //URL::forceScheme('https');
        Paginator::useBootstrapFive();

        View::composer('*', function ($view) {
            try {
                $settings   = Setting::first();
                $view->with('settings', $settings);
            } catch (\Exception $e) {
                // Database not available during migrations or setup 
            }
        });
    }
}
