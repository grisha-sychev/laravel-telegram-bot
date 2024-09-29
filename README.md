# Laravel Telergam Bot SDK

![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white) 
![Redis](https://img.shields.io/badge/redis-%23DD0031.svg?style=for-the-badge&logo=redis&logoColor=white)

![Packagist Version](https://img.shields.io/packagist/v/tgb/tgb)
![GitHub License](https://img.shields.io/github/license/grisha-sychev/laravel-telegram-bot)
![GitHub Repo stars](https://img.shields.io/github/stars/grisha-sychev/laravel-telegram-bot)

Laravel Telergam Bot SDK представляет собой готовый набор инструментов для Laravel, который значительно упрощает процесс создания ботов для Telegram.

- [Простые методы](/)
- [Callback-методы](/)
- [Хранилище сообщений](/)
- [Рекомендации по созданию бота](/)

## Установка и настройка

> [!IMPORTANT]\
> Прежде чем приступить к установке, убедитесь, что ваше приложение подключено к базе данных.


```bash
composer require tgb/tgb
```

```bash
php artisan vendor:publish --tag=tgb-assets
```

```bash
php artisan migrate
```

После этого необходимо зарегистрировать webhook с именем вашего бота (по умолчанию default) на домен, с которого происходит туннелирование. Если запуск происходит с хостинга, где уже есть домен, то указывать его не нужно. Для этого используется команда:

```bash
php artisan tgb:new default {token} {?domian}
```

## Использование бота

- Теперь, когда все настройки выполнены, вы можете запустить команду «/start» в вашем боте, и он ответит «Hello Word».

- Вся логика основного бота реализована в файле «app/Http/Bots/Default/Start.php», который легко найти и отредактировать.

- Если вы решите создать нового бота с другим именем, будет автоматически создан новый раздел.





