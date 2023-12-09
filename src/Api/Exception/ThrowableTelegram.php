<?php

namespace Reijo\Telebot\Api\Exception;

use Reijo\Telebot\ClientPlus;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Facades\Cache;
use Throwable;
use ParseError;

class ThrowableTelegram extends Handler
{
    public function report(Throwable $exception)
    {
        $cacheKey = 'error_notification_sent';
        $cacheDuration = now()->addMinutes(1); // Пример: уведомление будет отправлено не чаще одного раза в 30 минут

        $tool = new ClientPlus;
        
        if (!Cache::has($cacheKey)) {
            // Обрабатываем синтаксические ошибки
            if ($exception instanceof ParseError) {
                // Обработка синтаксических ошибок
                $tool->sendSelf($exception->getMessage());
            } else {
                // Обработка других ошибок
                $tool->sendSelf($exception->getMessage());
            }

            if ($this->shouldReport($exception)) {
                $cacheKey = 'error_notification_sent';
                $cacheDuration = now()->addMinutes(30); // Пример: уведомление будет отправлено не чаще одного раза в 30 минут

                // Проверяем, было ли уведомление уже отправлено в течение кэшированного времени
                $tool = new ClientPlus;
                $tool->sendSelf($exception->getMessage());
            }
            // Устанавливаем флаг уведомления в кэше
            Cache::put($cacheKey, true, $cacheDuration);
        }

        parent::report($exception);
    }
}
