<?php

namespace Tgb\Tgb\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * @package App\Providers
 */
class TelegramServiceProvider extends ServiceProvider
{
    public function map()
    {
        Route::withoutMiddleware(['web', 'App\Http\Middleware\VerifyCsrfToken'])->group(base_path('routes/tgb.php'));
    }
}
