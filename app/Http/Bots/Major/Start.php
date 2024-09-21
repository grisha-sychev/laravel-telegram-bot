<?php

namespace App\Http\Bots\Major;

class Start
{
    public static function handler($bot)
    {
        $bot->command("start", function () use ($bot) {
            $bot->sendSelf("Hello World");
        });
    }
}
