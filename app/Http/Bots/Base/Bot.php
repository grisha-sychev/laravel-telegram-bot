<?php

namespace App\Http\Bots\Base;

use Tgb\Tgb\Client;
use App\Models\User;

class Bot extends Client
{
    public function register($updated = true)
    {
        $user = $this->getUserData();

        if (!$user) {
            $user = new User();
            $user->tg_id = $this->getUserId();
            $user->login = $this->getUsername();
            $user->name = $this->getFirstName();
            $user->save();
        } else {
            if ($updated) {
                $user->login = $this->getUsername();
                $user->name = $this->getFirstName();
                $user->save();
            }
        }

        return true;
    }

    public function getUserData()
    {
        return User::where('tg_id', $this->getUserId())->first();
    }

}