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
    public function boot()
    {
        $filePath = base_path('routes/telegram.php');
        file_put_contents($filePath, "");

        $this->routes(function () {
            Route::middleware('web')->withoutMiddleware(['web', 'App\Http\Middleware\VerifyCsrfToken'])
                ->group(base_path('routes/telegram.php'));
        });
    }
}