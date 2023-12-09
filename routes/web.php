<?php

Route::post('/bot/main', [App\Http\Controllers\TelegramController::class, 'mybot']);

