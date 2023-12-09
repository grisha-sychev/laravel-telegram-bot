<?php

namespace App\Scenarios\MainBot;

class Start
{
    public static function handler($bot)
    {
        $bot->command("start", function () use ($bot) {
            $bot->sendSelf("Hello World");
        });
    }
}
