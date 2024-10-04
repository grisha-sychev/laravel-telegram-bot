<?php

namespace App\Console;

use Tgb\Api\Telegram;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

// Сначала записывает имя и токен в конфиг tgb, далее делает регистрацию бота
Artisan::command("tgb:new {name} {token} {domain=null}", function () {
  $name = $this->argument('name');
  $token = $this->argument('token');
  $domain = $this->argument('domain');

  // Записываем имя и токен в конфиг
  $configPath = config_path('tgb.php');
  $config = include $configPath;

  if (!is_array($config)) {
    $config = [];
  }

  $config[$name] = $token;

  File::put(
    $configPath,
    '<?php

return ' . var_export($config, true) . ';'
  );

  // Перезагружаем конфиг, чтобы изменения вступили в силу
  Artisan::call('config:cache');

  // Регистрируем бота
  $client = new Telegram();
  $client->bot = $name;
  $client->domain = $domain ? $domain : null;

  if ($client !== null) {
    $array = json_decode($client->setWebhook(), true);
    $description = $array['description'];
    echo "
  $description
       ";
  }

  $botNameCapitalized = ucfirst($name);

  // Создаем папку и файл для бота
  $botDirectory = app_path("Http/Bots/" . $botNameCapitalized);

  // Создаем папку, если она не существует
  if (!File::exists($botDirectory)) {
    File::makeDirectory($botDirectory, 0755, true);
  }

  $startFilePath = $botDirectory . "/Start.php";

  // Создаем файл, если он не существует
  if (!File::exists($startFilePath)) {
    $startFileContent = <<<PHP
<?php

namespace App\Http\Bots\\{$botNameCapitalized};

use App\Http\Bots\Base\Bot;

class Start extends Bot
{

    public function __construct()
    { 
        \$this->register(false);
    }

    public function handler()
    {
        \$this->command("start", function () {
            \$this->register();
            \$this->sendSelf("Hello World");
        });
    }
}
PHP;

    File::put($startFilePath, $startFileContent);
  }
});


Artisan::command("tgb:del {name}", function () {
  $name = $this->argument('name');

  // Удаляем имя и токен из конфига
  $configPath = config_path('tgb.php');
  $config = include $configPath;

  if (isset($config[$name])) {
    unset($config[$name]);
    File::put($configPath, '<?php return ' . var_export($config, true) . ';');

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
      echo "
Error: There is no such bot!
      ";
    }
  } else {
    echo "
Error: Bot name not found in config!
    ";
  }
});
