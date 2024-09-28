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

    public $bot;
    public $command;
    public $callback;
    public $message;

    /**
     * Конструктор класса Client.
     *
     * @param string|null $bot Имя бота (необязательный параметр).
     */
    public function __construct(string $bot = null)
    {
        if ($bot) {
            $this->bot = $bot;
        }
    }

    /**
     * Определяет бота
     */
    private function getToken(): string
    {
        return config('tgb.' . $this->bot);
    }

    /**
     * Получает ссылку на аватраку пользователя
     */
    public function getUserAvatarUrl()
    {
        $userAvatarFileId = $this->getUserAvatarFileId();

        // Проверяем наличие file_id
        if ($userAvatarFileId) {
            $photo = json_decode($this->getFile($userAvatarFileId), true);

            // Проверяем наличие информации о файле
            if (isset($photo['result']['file_path'])) {
                return $this->file($photo['result']['file_path']);
            }
        }

        // Возвращаем null или другое значение, если информация о файле отсутствует
        return null;
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
     * Определяет команду для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения или callback.
     *
     * @param string|array $command Команда, начинающаяся с символа "/" (например, "/start") или массив команд.
     * @param Closure $callback Функция-обработчик для выполнения, если команда или callback совпадают.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function command($command, $callback)
    {
        // Приводим команду к массиву, если это строка
        $commands = is_array($command) ? $command : [$command];

        // Преобразуем команды, добавляя "/" к каждой, если необходимо
        $commands = array_map(function ($cmd) {
            return "/" . ltrim($cmd, '/');
        }, $commands);

        // Привязываем callback к текущему объекту
        $callback = $callback->bindTo($this, $this);

        // Получаем текст сообщения и данные callback
        $messageText = $this->getMessageText();
        $cb = $this->getCallbackData();

        // Проверка для текста сообщения
        foreach ($commands as $cmd) {
            if ($cmd === $this->firstword($messageText)) {
                $arguments = $this->getArguments($messageText);
                $callback($arguments);
            }
        }

        // Проверка для callback данных
        if ($cb && is_object($cb)) {
            foreach ($commands as $cmd) {
                if ($cmd === "/" . $cb->callback_data) { // сравниваем с callback_data
                    $arguments = $this->getArguments($cb->callback_data);
                    $callback($arguments);
                }
            }
        }

        return null;
    }

    /**
     * Извлекает аргументы из текста команды.
     *
     * @param string $text Текст команды.
     *
     * @return array Массив аргументов.
     */
    private function getArguments($text)
    {
        $parts = explode(' ', $text);
        array_shift($parts); // Удаляем первую часть, так как это команда
        return $parts;
    }

    /**
     * Определяет callback для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения.
     *
     * @param string|array $pattern  Это строка или массив строк, по которым будет искать какой callback выполнить, например hello{id} или greet{name}.
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает с паттерном.
     * @param bool $deleteInlineButtons Удалять ли кнопки прошлого сообщения
     * 
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function callback($pattern, $callback, $deleteInlineButtons = false)
    {
        $cb = $this->getCallbackData();

        // Добавляем проверку на существование и тип переменной $cb
        if ($cb && is_object($cb)) {
            // Приводим паттерн к массиву, если это строка
            $patterns = is_array($pattern) ? $pattern : [$pattern];

            // Пробегаемся по каждому паттерну
            foreach ($patterns as $singlePattern) {
                // Преобразуем паттерн с параметрами в регулярное выражение
                $singlePattern = str_replace(['{', '}'], ['(?P<', '>[^}]+)'], $singlePattern);
                $singlePattern = "/^" . str_replace('/', '\/', $singlePattern) . "$/";

                if (preg_match($singlePattern, $cb->callback_data, $matches)) {
                    // Извлекаем только значения параметров из совпавших данных и передаем их в функцию-обработчик
                    $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    // Вызываем answerCallbackQuery только если паттерн совпадает
                    $this->answerCallbackQuery($cb->callback_query_id);

                    if ($deleteInlineButtons) {
                        $this->editMessage($this->getUserId(), $this->getMessageId(), $this->getMessageText());
                    }

                    $callback(...$parameters);
                    exit; // Завершаем выполнение скрипта после выполнения callback
                }
            }
        }

        return null;
    }

    /**
     * Определяет сообщение для бота и выполняет соответствующий обработчик, если сообщение совпадает с паттерном.
     *
     * @param string|array $pattern Это строка или массив строк/регулярных выражений, по которым будет искать совпадение с сообщением.
     * @param Closure $callback Функция-обработчик для выполнения, если сообщение совпадает с паттерном.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function message($pattern, $callback)
    {
        $messageText = $this->getMessageText();

        // Приводим паттерн к массиву, если это строка
        $patterns = is_array($pattern) ? $pattern : [$pattern];

        // Пробегаемся по каждому паттерну
        foreach ($patterns as $singlePattern) {
            // Проверяем, является ли паттерн регулярным выражением
            $isRegex = preg_match('/^\/.*\/[a-z]*$/i', $singlePattern);

            // Если это не регулярное выражение, преобразуем паттерн с параметрами в регулярное выражение
            if (!$isRegex) {
                $singlePattern = str_replace(['{', '}'], ['(?P<', '>[^}]+)'], $singlePattern);
                $singlePattern = "/^" . str_replace('/', '\/', $singlePattern) . "$/";
            }

            if (preg_match($singlePattern, $messageText, $matches)) {
                // Извлекаем только значения параметров из совпавших данных и передаем их в функцию-обработчик
                $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Вызываем функцию-обработчик с параметрами
                $callback(...$parameters);
                exit; // Завершаем выполнение скрипта после выполнения callback
            }
        }

        return null;
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
