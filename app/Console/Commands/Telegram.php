<?php

namespace App\Console\Commands;

use Reijo\Telebot\Api\Telegram;
use Illuminate\Support\Facades\Artisan;


Artisan::command("t:set {namebot=main}", function ($namebot) {
  $client = new Telegram($this->argument('namebot'));
  if ($client !== null) {
    $array = json_decode($client->setWebhook(), true);
    $description = $array['description'];
    echo "
  $description
       ";
  } else {
    echo "Error: There is no such bot!";
  }
});

Artisan::command("t:remove {namebot=mybot}", function ($namebot) {
  $client = new Telegram($this->argument('namebot'));
  if ($client !== null) {
    $array = json_decode($client->removeWebhook(), true);
    $description = $array['description'];
    echo "
  $description
       ";
  } else {
    echo "Error: There is no such bot!";
  }

});