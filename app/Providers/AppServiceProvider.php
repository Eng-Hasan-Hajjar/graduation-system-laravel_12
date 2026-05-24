<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Bootstrap pagination
        Paginator::useBootstrapFive();

        // @active('route.name') directive for sidebar
        Blade::directive('active', function ($route) {
            return "<?php echo request()->routeIs({$route}) ? 'active' : ''; ?>";
        });

        // Set default locale from config
        if (!session()->has('locale')) {
            app()->setLocale(config('app.locale', 'ar'));
        }
    }
}