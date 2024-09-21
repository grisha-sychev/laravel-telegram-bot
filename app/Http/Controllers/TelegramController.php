<?php

namespace App\Http\Controllers;

use App\Bot\Major;
use App\Http\Bots\Major\Start;
use App\Http\Controllers\Controller;


class TelegramController extends Controller
{
    // Логика основного бота
    public function mybot()
    {
        Start::handler(new Major);
    }

}