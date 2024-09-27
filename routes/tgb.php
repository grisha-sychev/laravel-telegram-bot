<?php
use Illuminate\Support\Facades\Route;


Route::post('/bot/{token}', function ($token) {

    $bots = config('tgb');

    $botName = array_search($token, $bots);

    if ($botName === false) {
        return response()->json(['error' => 'Bot not found'], 404);
    }

    $botClass = 'App\\Http\\Bots\\' . ucfirst($botName) . '\\Start';

    if (!class_exists($botClass)) {
        return response()->json(['error' => 'Bot class not found'], 404);
    }

    return (new $botClass())->handler();
});

