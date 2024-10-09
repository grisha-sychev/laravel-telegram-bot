<?php

namespace Tgb\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * @package App\Providers
 */
class TelegramServiceProvider extends ServiceProvider
{
    public function map()
    {
        $routePath = base_path('routes/tgb.php');
        
        if (file_exists($routePath)) {
            Route::withoutMiddleware(['web', 'App\Http\Middleware\VerifyCsrfToken'])->group($routePath);
        }
    }
}
