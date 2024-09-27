<?php

namespace Reijo\Telebot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * @package App\Providers
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function register()
    {
        $this->routes(function () {
            Route::middleware('web')->withoutMiddleware(['web', 'App\Http\Middleware\VerifyCsrfToken'])
                ->group(base_path('routes/tgb.php'));
        });
    }
}
