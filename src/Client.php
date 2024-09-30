<?php

namespace Tgb;

use Tgb\Api\Telegram;
use Tgb\Services\Appendix;

/**
 * Класс Client
 * 
 * Этот класс предоставляет функциональность для работы с Telegram Bot API.
 * Он включает методы для отправки сообщений, обработки обновлений и другие
 * полезные функции для взаимодействия с ботом.
 * 
 * @package Tgb
 */

class Client extends Telegram
{
    /**
     * Метод отправки сообщения текущему пользователю
     *
     * @param string|array $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int|array $layout Число делений или массив с ручным расположением.
     * @param int $type_keyboard Тип каливатуры 1 - keyboard 2 - inlineKeyboard
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     * 
     */
    public function sendSelf($message, $keyboard = null, $layout = 2, $type_keyboard = 0, $parse_mode = "HTML")
    {
        is_array($message) ? $message = $this->html($message) : $message;
        $keyboard ? $keygrid = $this->grid($keyboard, $layout) : $keyboard;
        $type_keyboard === 1 ? $type = "inlineKeyboard" : $type = "keyboard";
        return $this->sendMessage($this->getUserId(), $message, $keyboard ? $this->$type($keygrid) : $keyboard, $parse_mode);
    }

    /**
     * Метод отправки сообщения текущему пользователю использует inlineKeyboard
     *
     * @param string|array $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $layout Число делений или массив с ручным расположением.
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     * 
     */
    public function sendSelfInline($message, $keyboard = null, $layout = 2, $parse_mode = "HTML")
    {
        if ($keyboard) {
            foreach ($keyboard as &$button) {
                if (isset($button['web_app']) && is_string($button['web_app'])) {
                    $button['web_app'] = ['url' => $button['web_app']];
                }
            }
        }

        return $this->sendSelf($message, $keyboard, $layout, 1, $parse_mode);
    }

    /**
     * Метод удаления сообщений в чате
     *
     * @param string|array $message_id ID сообщения.
     * 
     */
    public function deleteSelfMessage($message_id)
    {
        return $this->deleteMessage($this->getUserId(), $message_id);
    }


    /**
     * Метод редактирования сообщения текущему пользователю
     *
     * @param string|array $message Текст сообщения.
     * @param string $message_id id сообщения
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $layout Число делений или массив с ручным расположением.
     * @param int $type_keyboard Тип каливатуры 1 - keyboard 2 - inlineKeyboard
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     * 
     */
    public function editSelf($message_id, $message, $keyboard = null, $layout = 2, $type_keyboard = 0, $parse_mode = "HTML")
    {
        is_array($message) ? $message = $this->html($message) : $message;
        $keyboard ? $keygrid = $this->grid($keyboard, $layout) : $keyboard;
        $type_keyboard === 1 ? $type = "inlineKeyboard" : $type = "keyboard";
        return $this->editMessage($this->getUserId(), $message_id, $message, $keyboard ? $this->$type($keygrid) : $keyboard, $parse_mode);
    }

    /**
     * Метод редактирования сообщения текущему пользователю использует inlineKeyboard
     *
     * @param string $message_id id сообщения
     * @param string|array $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $layout Число делений или массив с ручным расположением.
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     * 
     */

    public function editSelfInline($message_id, $message, $keyboard = null, $layout = 2, $parse_mode = "HTML")
    {
        return $this->editSelf($message_id, $message, $keyboard, $layout, 1, $parse_mode);
    }

    /**
     * Метод отправки сообщения другому пользователю
     *
     * @param int $id Идентификатор пользователя.
     * @param string $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $layout Число делений или массив с ручным расположением.
     * @param int $type_keyboard Тип каливатуры 0 - keyboard 1 - inlineKeyboard
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     *
     */
    public function sendOut($id, $message, $keyboard = null, $layout = 2, $type_keyboard = 0, $parse_mode = "HTML")
    {
        is_array($message) ? $message = $this->html($message) : $message;
        $keyboard ? $keygrid = $this->grid($keyboard, $layout) : $keyboard;
        $type_keyboard === 1 ? $type = "inlineKeyboard" : $type = "keyboard";
        return $this->sendMessage($id, $message, $keyboard ? $this->$type($keygrid) : $keyboard, $parse_mode);
    }


    /**
     * Метод отправки сообщения другому пользователю использует inlineKeyboard
     *
     * @param int $id Идентификатор пользователя.
     * @param string $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $layout Число делений или массив с ручным расположением.
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     *
     */
    public function sendOutInline($id, $message, $keyboard = null, $layout = 2, $parse_mode = "HTML")
    {
        return $this->sendOut($id, $message, $keyboard, $layout, 1, $parse_mode);
    }

    /**
     * Отправляет счет самому себе.
     *
     * @param string $title Название счета.
     * @param string $description Описание счета.
     * @param string $payload Полезная нагрузка счета.
     * @param string $provider_token Токен провайдера.
     * @param string $start_parameter Параметр запуска.
     * @param string $currency Валюта счета.
     * @param array $prices Массив цен.
     * @param int|null $reply_to_message_id ID сообщения, на которое нужно ответить (необязательно).
     * @param bool $disable_notification Отключить уведомления (по умолчанию false).
     * @param string|null $photo_url URL фотографии (необязательно).
     * @param int|null $photo_size Размер фотографии (необязательно).
     * @param int|null $photo_width Ширина фотографии (необязательно).
     * @param int|null $photo_height Высота фотографии (необязательно).
     * @param bool $need_name Требуется ли имя (по умолчанию false).
     * @param bool $need_phone_number Требуется ли номер телефона (по умолчанию false).
     * @param bool $need_email Требуется ли email (по умолчанию false).
     * @param bool $need_shipping_address Требуется ли адрес доставки (по умолчанию false).
     * @param bool $send_phone_number_to_provider Отправить ли номер телефона провайдеру (по умолчанию false).
     * @param bool $send_email_to_provider Отправить ли email провайдеру (по умолчанию false).
     * @param bool $is_flexible Гибкий ли счет (по умолчанию false).
     *
     * @return mixed Результат отправки счета.
     */
    public function sendInvoiceSelf($title, $description, $payload, $provider_token, $start_parameter, $currency, $prices, $reply_to_message_id = null, $disable_notification = false, $photo_url = null, $photo_size = null, $photo_width = null, $photo_height = null, $need_name = false, $need_phone_number = false, $need_email = false, $need_shipping_address = false, $send_phone_number_to_provider = false, $send_email_to_provider = false, $is_flexible = false)
    {
        return $this->sendInvoice($this->getUserId(), $title, $description, $payload, $provider_token, $start_parameter, $currency, $prices, $reply_to_message_id, $disable_notification, $photo_url, $photo_size, $photo_width, $photo_height, $need_name, $need_phone_number, $need_email, $need_shipping_address, $send_phone_number_to_provider, $send_email_to_provider, $is_flexible);
    }

    /**
     * Отправляет счет самому себе.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $title Название счета.
     * @param string $description Описание счета.
     * @param string $payload Полезная нагрузка счета.
     * @param string $provider_token Токен провайдера.
     * @param string $start_parameter Параметр запуска.
     * @param string $currency Валюта счета.
     * @param array $prices Массив цен.
     * @param int|null $reply_to_message_id ID сообщения, на которое нужно ответить (необязательно).
     * @param bool $disable_notification Отключить уведомления (по умолчанию false).
     * @param string|null $photo_url URL фотографии (необязательно).
     * @param int|null $photo_size Размер фотографии (необязательно).
     * @param int|null $photo_width Ширина фотографии (необязательно).
     * @param int|null $photo_height Высота фотографии (необязательно).
     * @param bool $need_name Требуется ли имя (по умолчанию false).
     * @param bool $need_phone_number Требуется ли номер телефона (по умолчанию false).
     * @param bool $need_email Требуется ли email (по умолчанию false).
     * @param bool $need_shipping_address Требуется ли адрес доставки (по умолчанию false).
     * @param bool $send_phone_number_to_provider Отправить ли номер телефона провайдеру (по умолчанию false).
     * @param bool $send_email_to_provider Отправить ли email провайдеру (по умолчанию false).
     * @param bool $is_flexible Гибкий ли счет (по умолчанию false).
     *
     * @return mixed Результат отправки счета.
     */
    public function sendInvoiceOut($chat_id, $title, $description, $payload, $provider_token, $start_parameter, $currency, $prices, $reply_to_message_id = null, $disable_notification = false, $photo_url = null, $photo_size = null, $photo_width = null, $photo_height = null, $need_name = false, $need_phone_number = false, $need_email = false, $need_shipping_address = false, $send_phone_number_to_provider = false, $send_email_to_provider = false, $is_flexible = false)
    {
        return $this->sendInvoice($chat_id, $title, $description, $payload, $provider_token, $start_parameter, $currency, $prices, $reply_to_message_id, $disable_notification, $photo_url, $photo_size, $photo_width, $photo_height, $need_name, $need_phone_number, $need_email, $need_shipping_address, $send_phone_number_to_provider, $send_email_to_provider, $is_flexible);
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
            if (strpos($messageText, $cmd) === 0) {
                $arguments = Appendix::getArguments($messageText);
                $callback($arguments); // Завершаем выполнение после нахождения совпадения
                return;
            }
        }

        // Проверка для callback данных
        if ($cb && is_object($cb)) {
            foreach ($commands as $cmd) {
                if (strpos($cb->callback_data, $cmd) === 0) { // сравниваем с callback_data
                    $arguments = Appendix::getArguments($cb->callback_data);
                    $callback($arguments); // Завершаем выполнение после нахождения совпадения
                    return;
                }
            }
        }

        return null;
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
     * Находит совпадение для заданного шаблона в предоставленном тексте.
     *
     * @param string $pattern Регулярное выражение для поиска.
     * @param string $text Текст, в котором будет производиться поиск.
     * @return array|null Возвращает массив совпадений, если они найдены, или null, если совпадений не найдено.
     */
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
     * Метод для создания подгрупп для клавиатуры с возможностью ручного управления расположением кнопок
     *
     * @param array $array
     * @param int|array $layout Число делений или массив с ручным расположением.
     * 
     * @return array Возвращает новый массив
     */
    private function grid($array, $layout = 2)
    {
        if (is_array($layout)) {
            $result = [];
            $index = 0;
            foreach ($layout as $count) {
                $result[] = array_slice($array, $index, $count);
                $index += $count;
            }
            return $result;
        } elseif (is_int($layout) && $layout > 0) {
            $result = [];
            $currentSubarray = [];
            foreach ($array as $element) {
                $currentSubarray[] = $element;
                if (count($currentSubarray) == $layout) {
                    $result[] = $currentSubarray;
                    $currentSubarray = [];
                }
            }
            if (!empty($currentSubarray)) {
                $result[] = $currentSubarray;
            }
            return $result;
        } else {
            return [];
        }
    }

    /**
     * Метод для рендеринга HTML сообщений
     *
     * @param array $data строки (необязательно).
     * 
     * @return string Возвращает строку
     */
    private function html($data = [])
    {
        return implode("\n", $data);
    }

    /**
     * Метод для блокировки медиа
     *
     * @param callback $callback
     * 
     */
    public function ignoreMedia($callback)
    {
        if ($this->getMessageText()) {
            if (
                method_exists($this, 'getMedia') &&
                in_array(true, array_map(function ($value) {
                    return !is_null($value);
                }, (array) $this->getMedia()), true)
            ) {
                $callback();
                exit;
            }
        }
    }
}
