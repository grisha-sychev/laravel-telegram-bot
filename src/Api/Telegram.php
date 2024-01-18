<?php

namespace Reijo\Telebot\Api;

use Reijo\Telebot\Api\Trait\Helpers;
use Reijo\Telebot\Api\Trait\MethodQuery;
use Reijo\Telebot\Api\Trait\Photo;
use Reijo\Telebot\Api\Trait\User;
use Reijo\Telebot\Api\Trait\Program;

/**
 * Класс Api Client для управления Telegram ботом.
 */
class Telegram
{
    use User, Photo, Program, Helpers, MethodQuery;

    public $token;
    public $bot;
    public $command;
    public $callback;
    public $message;


    /**
     * Определяет бота
     */
    private function getBot(): array
    {
        return config('telegram.bots.' . $this->bot);
    }

    /**
     * Конструктор класса Client.
     */
    public function __construct(string $bot = 'main')
    {
        $this->bot = $bot;
        $this->token = $this->getBot()["token"];
    }

    /**
     * Получает ссылку на аватраку пользователя
     */
    public function getUserAvatarUrl()
    {
        $photo = json_decode($this->getFile($this->getUserAvatarFileId()), true);
        return $this->file($photo["result"]["file_path"]);
    }

    /**
     * Отправляет сообщение с инлайн-клавиатурой.
     *
     * @param array $keyboard Массив с настройками клавиатуры.
     *
     * @return string JSON-представление инлайн-клавиатуры.
     */
    public function inlineKeyboard($keyboard)
    {
        return json_encode(['inline_keyboard' => $keyboard]);
    }

    /**
     * Отправляет сообщение с обычной клавиатурой.
     *
     * @param array $keyboard Массив с настройками клавиатуры.
     * @param bool $one_time_keyboard Параметр одноразовой клавиатуры (по умолчанию true).
     * @param bool $resize_keyboard Параметр изменения размера клавиатуры (по умолчанию true).
     *
     * @return string JSON-представление обычной клавиатуры.
     */
    public function keyboard($keyboard, $one_time_keyboard = true, $resize_keyboard = true)
    {
        return json_encode([
            'keyboard' => $keyboard,
            'one_time_keyboard' => $one_time_keyboard,
            'resize_keyboard' => $resize_keyboard
        ]);
    }

    /**
     * Определяет команду для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения.
     *
     * @param string $command Команда, начинающаяся с символа "/" (например, "/start").
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function command($command, $callback)
    {
        $this->command = "/" . $command;

        $callback = $callback->bindTo($this, $this);

        if ($this->command === $this->firstword($this->getMessageText())) {
            return $callback();
        }
    }

    /**
     * Определяет callback для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения.
     *
     * @param string $command Команда, начинающаяся с символа "/" (например, "/start").
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function callback($data, $callback)
    {
        $this->callback = $this->getCallbackData()->callback_data;
        $this->answerCallbackQuery($this->getCallbackData()->callback_query_id);
        $callback = $callback->bindTo($this, $this);

        if ($this->callback === $data) {
            return $callback();
        }
    }

    /**
     * Определяет сообщение для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения.
     *
     * @param string $message Любое сообщение кроме команды.
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function message($message, $callback)
    {
        $callback = $callback->bindTo($this, $this);
        $this->message = $message;
        if (mb_substr($message, 0, 1) !== "/") {
            if ($message === $this->getMessageText()) {
                return $callback();
            }
        }
    }

    /**
     * Определяет сообщение от пользователя и выполняет ошибку.
     *
     * @param mixed $message Любое сообщение кроме команды.
     * @param array|null $array Данные
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function error($message, $array, $callback)
    {
        $callback = $callback->bindTo($this);

        if ($array === null) {
            if ($message === $this->getMessageText()) {
                $callback();
            }
        } else {
            if ($this->findMatch($message, $array)) {
                $callback();
            }
        }

    }

    private function findMatch($data, $array)
    {
        foreach ($array as $value) {
            if (stripos($data, $value) !== false) {
                return false; // Найдено совпадение
            }
        }
        return true; // Совпадений не найдено
    }


    /**
     * Определяет действие для бота и выполняет соответствующий обработчик, если текст сообщения не начинается с "/".
     *
     * @param Closure $callback Функция-обработчик для выполнения, если текст сообщения не является командой.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function anyMessage($callback)
    {
        if (mb_substr($this->getMessageText(), 0, 1) !== "/") {
            return $callback();
        }
    }

    /**
     * Аругмент любой команды.
     * 
     * @return int|string|null Результат выполнения функции-обработчика.
     */
    public function argument()
    {
        if ($this->command === $this->firstword($this->getMessageText())) {
            return $this->lastword($this->getMessageText());
        }
    }

}
