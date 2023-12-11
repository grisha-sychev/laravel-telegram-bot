<?php

namespace Reijo\Telebot\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * @package App\Providers
 */
class TelegramBootstrapServiceProvider extends ServiceProvider
{
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
        ], "reijo-telebot");
    }
}
