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
        
        if (!file_exists($filePath)) {
            file_put_contents(
                $filePath,
                "<?php\n

use Illuminate\Support\Facades\Route;\n

Route::post('/bot/main', [App\Http\Controllers\TelegramController::class, 'mybot']);\n"
            );
        }

        $this->routes(function () {
            Route::middleware('web')->withoutMiddleware(['web', 'App\Http\Middleware\VerifyCsrfToken'])
                ->group(base_path('routes/telegram.php'));
        });
    }
}
