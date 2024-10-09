<?php

namespace App\Console;

use Tgb\Api\Telegram;
use Illuminate\Support\Facades\Artisan;

// Сначала записывает имя и токен в конфиг tgb, далее делает регистрацию бота
Artisan::command("tgb:new {name} {token} {domain=null}", function () {
  $name = $this->argument('name');
  $token = $this->argument('token');
  $domain = $this->argument('domain');

  // Записываем имя и токен в конфиг
  $configPath = config_path('tgb.php');
  $config = include $configPath;
  $config[$name] = $token;

  file_put_contents(
    $configPath,
    '<?php

   return ' . var_export($config, true) . ';'
  );

  // Перезагружаем конфиг, чтобы изменения вступили в силу
  Artisan::call('config:cache');

  // Регистрируем бота
  $client = new Telegram();
  $client->bot = $name;
  $domain ?? $client->domain = $domain;

  if ($client !== null) {
    $array = json_decode($client->setWebhook(), true);
    $description = $array['description'];

    $this->info(PHP_EOL . $description . PHP_EOL);

    if ($description === "Webhook is already set") {
      return;
    }
  }

  // Создаем папку и файл для бота
  $botNameCapitalized = ucfirst($name);
  $botDirectory = app_path("Bots");

  $startFilePath = "{$botDirectory}/{$botNameCapitalized}.php";

  // if (!file_exists($botDirectory)) {
  //   mkdir($botDirectory, 0755, true);
  // }

  if (!file_exists($startFilePath)) {
    $startFileContent = <<<PHP
<?php

namespace App\Bots;

use App\Bots\AbstractBot;

class {$botNameCapitalized} extends AbstractBot
{

    public function __construct()
    { 
         parent::__construct();
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

    file_put_contents($startFilePath, $startFileContent);
  }
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
      $this->info(PHP_EOL . $description . PHP_EOL);
    } else {
      $this->info(PHP_EOL . 'Error: There is no such bot!' . PHP_EOL);
    }
  } else {
    $this->info(PHP_EOL . 'Error: Bot name not found in config!' . PHP_EOL);
  }
});
