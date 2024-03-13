# Telebot Laravel - Api Telegram Bot
Telergam Bot SDK пакет для Laravel

## 📕 Документация
- [Базовая логика](https://github.com/grisha-sychev/telebot/blob/main/basic-logic.md)
- [Система шагов](https://github.com/grisha-sychev/telebot/blob/main/basic-logic.md)
- [Все про ApiMod](https://github.com/grisha-sychev/telebot/blob/main/basic-logic.md)
- [Про систему папок](https://github.com/grisha-sychev/telebot/blob/main/basic-logic.md)

## 📋 Установка
Прежде чем установить, подключите приложение к `базе данных`
```
composer require reijo/telebot
```

Добавьте в конфигурацию `app.php` провайдеры
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    /*
     * Package Service Providers...
     */
    Reijo\Telebot\Providers\TelegramServiceProvider::class,
    Reijo\Telebot\Providers\TelegramBootstrapServiceProvider::class,
])->toArray(),
```
Выполните выгрузку провайдеров
```
php artisan vendor:publish --tag=reijo-telebot
```
В конфигурации `telegram.php` добавьте `token` и адрес сайта, `/bot/main` являеться дефолтным адресом основного бота, рекомендуем основного бота оставить по этому адресу

```php
return [
    'bots' => [
        "main" => [
            "token" => "",
            "url" => "https://domen.com/bot/main",
        ]
    ],
];
```
Теперь можете выполнить миграцию
```
php artisan migrate
```
Зарегестрируйте webhook
```
php artisan t:set-webhook
```

- Теперь вы можете в вашем боте вызвать команду `start` и получить ответ `Hello Word`
- Вся логика основного бота находиться в разделе `app/Scenarios/MainBot/Start.php`
- Внутрення логика бота находиться в разделе `app/Bots/Main.php`


