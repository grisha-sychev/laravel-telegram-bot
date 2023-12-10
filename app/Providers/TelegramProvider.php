<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * @package App\Providers
 */
class TelegramProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../app' => app_path(),
            __DIR__ . '/../../config' => config_path(),
            __DIR__ . '/../../database' => database_path(),
            __DIR__ . '/../../routes' => base_path('routes'),
        ]);
        
        $this->routes(function () {
            Route::middleware('web')->withoutMiddleware(['web', 'App\Http\Middleware\VerifyCsrfToken'])
                ->group(base_path('routes/telegram.php'));
        });
    }
}
