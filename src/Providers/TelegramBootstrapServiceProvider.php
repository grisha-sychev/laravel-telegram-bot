<?php

namespace Tgb\Tgb\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

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
        $pathsToPublish = [
            __DIR__ . '/../../app' => app_path(),
            __DIR__ . '/../../config' => config_path(),
            __DIR__ . '/../../database' => database_path(),
            __DIR__ . '/../../routes' => base_path('routes'),
        ];

        $pathsToPublish = array_filter($pathsToPublish, function ($source) {
            return file_exists($source);
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($pathsToPublish)) {
            $this->publishes($pathsToPublish, "laravel-assets");
        }
    }
}

