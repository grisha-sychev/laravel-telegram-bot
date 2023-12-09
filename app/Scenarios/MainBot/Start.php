<?php

namespace App\Scenarios\MainBot;

class Start
{
    public static function handler($bot)
    {
        $bot->command("start", function () {
            $this->sendSelf("Hello World");
        });
    }
}
