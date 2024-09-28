<?php

namespace App\Console;

use Reijo\Telebot\Api\Telegram;
use Illuminate\Support\Facades\Artisan;

// Сначала записывает имя и токен в конфиг tgb, далее делает регистрацию бота
Artisan::command("tgb:new {name} {token}", function () {
  $name = $this->argument('name');
  $token = $this->argument('token');

  // Записываем имя и токен в конфиг
  $configPath = config_path('tgb.php');
  $config = include $configPath;
  $config[$name] = $token;

  file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');

  // Перезагружаем конфиг, чтобы изменения вступили в силу
  Artisan::call('config:cache');

  // Регистрируем бота
  $client = new Telegram();
  $client->bot = $name;

  if ($client !== null) {
    $array = json_decode($client->setWebhook(), true);
    $description = $array['description'];
    echo "
  $description
       ";
    if ($description === "Webhook is already set") {
      return;
    }
  }

  // Создаем папку и файл для бота
  $botNameCapitalized = ucfirst($name);
  $botDirectory = app_path("Http/Bots/{$botNameCapitalized}");
  $startFilePath = "{$botDirectory}/Start.php";

  if (!file_exists($botDirectory)) {
    mkdir($botDirectory, 0755, true);
  }

  $startFileContent = <<<PHP
<?php

namespace App\Http\Bots\\{$botNameCapitalized};

use Reijo\Telebot\Base\Bot;

class Start extends Bot
{
    public function handler()
    {
        \$this->command("start", function () {
            \$this->sendSelf("Hello World");
        });
    }
}
PHP;

  file_put_contents($startFilePath, $startFileContent);
});


Artisan::command("tgb:del {name}", function () {
  $name = $this->argument('name');

  // Удаляем имя и токен из конфига
  $configPath = config_path('tgb.php');
  $config = include $configPath;

  if (isset($config[$name])) {
    unset($config[$name]);
    file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');

    // Удаляем вебхук бота
    $client = new Telegram();
    $client->bot = $name;

    if ($client !== null) {
      $array = json_decode($client->removeWebhook(), true);
      $description = $array['description'];
      echo "
  $description
       ";
    } else {
      echo "Error: There is no such bot!";
    }
  } else {
    echo "Error: Bot name not found in config!";
  }
});