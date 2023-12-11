# telebot
Telergam bot SDK package for laravel

```
composer create-project laravel/laravel example-app
```
```
cd example-app
```
add info db 
```
composer require reijo/telebot
```
add in app config provaider 

```
'providers' => ServiceProvider::defaultProviders()->merge([
    /*
     * Package Service Providers...
     */
    App\Providers\TelegramServiceProvider::class,
])->toArray(),
```
    
```
php artisan vendor:publish --tag=reijo-telebot
```
add info in file config telergam
```
php artisan migrate
```
```
php artisan t:set-webhook
```
