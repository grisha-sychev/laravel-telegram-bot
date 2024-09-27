<?php

namespace Reijo\Telebot\Base;

use Reijo\Telebot\Tgb;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class Bot extends Tgb
{
    // public function check($updated = true)
    // {
    //     $user = $this->getUserData();

    //     is_numeric($this->argument()) ? $invited = $this->argument() : $invited = 0;

    //     if (!$user) {
    //         $user = new User();
    //         $user->tg_id = $this->getUserId();
    //         $user->login = $this->getUsername();
    //         $user->name = $this->getFirstName();
    //         $user->invited = $invited;
    //         $user->avatar = "/photo/user/" . $this->download($this->getUserAvatarUrl());
    //         $user->save();
    //     } else {
    //         if ($updated) {
    //             $user->login = $this->getUsername();
    //             $user->name = $this->getFirstName();
    //             Storage::disk('photo')->delete(basename($user->avatar));
    //             $user->avatar = "/photo/user/" . $this->download($this->getUserAvatarUrl());
    //             $user->save();
    //         }
    //     }

    //     return true;
    // }


    // public function download($imageUrl)
    // {
    //     $client = new Client();
    //     $response = $client->get($imageUrl);

    //     $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);

    //     $newName = Str::random(20) . '.' . $extension;

    //     Storage::disk('photo')->put($newName, $response->getBody());

    //     return $newName;
    // }

    public function getUserData()
    {
        return User::where('tg_id', $this->getUserId())->first();
    }

}