<?php

namespace App\Http\Bots\Default;

use Reijo\Telebot\Base\Bot;

class Start extends Bot
{
    
    public function handler()
    {
        $this->command("start", function () {
            $this->sendSelf("Hello World");
        });
    }
}
