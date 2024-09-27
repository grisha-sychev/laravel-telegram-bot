<?php

namespace App\Console\Commands;

use Reijo\Telebot\Api\Telegram;
use Illuminate\Support\Facades\Artisan;

// Сначала записывает имя и токен в конфиг tgb, далее делает регистрацию бота
Artisan::command("tgb:new --name={name} --token={token}", function () {
  $name = $this->argument('name');
  $token = $this->argument('token');

  // Записываем имя и токен в конфиг
  $configPath = config_path('tgb.php');
  $config = include $configPath;
  $config['bots'][$name] = $token;
  file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');

  // Регистрируем бота
  $client = new Telegram();
  $client->bot = $name;

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

Artisan::command("t:remove {namebot=major}", function () {
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
