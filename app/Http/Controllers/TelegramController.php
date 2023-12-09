<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Scenarios\MainBot\Start;
use App\Bots\Main;

class TelegramController extends Controller
{
    // Логика основного бота
    public function mybot()
    {
        Start::handler(Main::class);
    }

}
