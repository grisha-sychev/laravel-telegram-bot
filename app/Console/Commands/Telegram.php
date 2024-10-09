<?php

namespace App\Console;

use Tgb\Api\Telegram;
use Illuminate\Support\Facades\Artisan;

Artisan::command("tgb:new {name} {token} {hostname=null}", function () {
  $name = $this->argument('name');
  $token = $this->argument('token');
  $hostname = $this->argument('hostname');

  $configPath = config_path('tgb.php');
  $config = include $configPath;
  $config = is_array($config) ? $config : [];
  $config[$name] = $token;

  file_put_contents(
    $configPath,
    '<?php return ' . var_export($config, true) . ';'
  );

  Artisan::call('config:cache');

  $client = new Telegram();
  $client->bot = $name;
  $client->hostname = $hostname ? $hostname : $client->hostname;

  if ($client !== null) {
    $array = $client->setWebhook();
    $description = $array['description'];

    $this->info(PHP_EOL . $description . PHP_EOL);

    if ($description === "Webhook is already set") {
      return;
    }
  }

  $botNameCapitalized = ucfirst($name);
  $botDirectory = app_path("Bots");

  $startFilePath = "{$botDirectory}/{$botNameCapitalized}.php";

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

  $configPath = config_path('tgb.php');
  $config = include $configPath;

  if (isset($config[$name])) {
    unset($config[$name]);
    file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');

    $client = new Telegram();
    $client->bot = $name;

    if ($client !== null) {
      $array = $client->removeWebhook();
      $description = $array['description'];
      $this->info(PHP_EOL . $description . PHP_EOL);
    } else {
      $this->info(PHP_EOL . 'Error: There is no such bot!' . PHP_EOL);
    }
  } else {
    $this->info(PHP_EOL . 'Error: Bot name not found in config!' . PHP_EOL);
  }
});
