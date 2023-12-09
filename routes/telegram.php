<?php
use Illuminate\Support\Facades\Route;
Route::post('/bot/main', [App\Http\Controllers\TelegramController::class, 'mybot']);

