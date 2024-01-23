<?php

namespace Reijo\Telebot;

use Reijo\Telebot\Api\Telegram;

/**
 * Api Mod Telegram - имеет все стандартные методы плюс моды для удобства
 */

class ApiMod extends Telegram
{
    public function __construct($bot = 'main')
    {
        parent::__construct($bot);
    }

    /**
     * Метод отправки сообщения текущему пользователю
     *
     * @param string|array $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $grid Деление сообщений на столбцы.
     * @param int $type_keyboard Тип каливатуры 1 - keyboard 2 - inlineKeyboard
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     * 
     */
    public function sendSelf($message, $keyboard = null, $grid = 2, $type_keyboard = 0, $parse_mode = "HTML")
    {
        is_array($message) ? $message = $this->html($message) : $message;
        $keyboard ? $keygrid = $this->grid($keyboard, $grid) : $keyboard;
        $type_keyboard === 1 ? $type = "inlineKeyboard" : $type = "keyboard";
        return $this->sendMessage($this->getUserId(), $message, $keyboard ? $this->$type($keygrid) : $keyboard, $parse_mode);
    }


    /**
     * Метод отправки сообщения текущему пользователю использует inlineKeyboard
     *
     * @param string|array $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $grid Деление сообщений на столбцы.
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     * 
     */
    public function sendSelfInline($message, $keyboard = null, $grid = 2, $parse_mode = "HTML")
    {
        return $this->sendSelf($message, $keyboard, $grid, 1, $parse_mode);
    }

    /**
     * Метод отправки сообщения другому пользователю
     *
     * @param int $id Идентификатор пользователя.
     * @param string $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $grid Деление сообщений на столбцы.
     * @param int $type_keyboard Тип каливатуры 0 - keyboard 1 - inlineKeyboard
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     *
     */
    public function sendOut($id, $message, $keyboard = null, $grid = 2, $type_keyboard = 0, $parse_mode = "HTML")
    {
        $keyboard ? $keygrid = $this->grid($keyboard, $grid) : $keyboard;
        $type_keyboard === 1 ? $type = "inlineKeyboard" : $type = "keyboard";
        return $this->sendMessage($id, $message, $keyboard ? $this->$type($keygrid) : $keyboard, $parse_mode);
    }


    /**
     * Метод отправки сообщения другому пользователю использует inlineKeyboard
     *
     * @param int $id Идентификатор пользователя.
     * @param string $message Текст сообщения.
     * @param array|null $keyboard Клавиатура для сообщения (необязательно).
     * @param int $grid Деление сообщений на столбцы.
     * @param string|null $parse_mode Включение HTML мода, по умолчанию включен (необязательно).
     *
     */
    public function sendOutInline($id, $message, $keyboard = null, $grid = 2, $parse_mode = "HTML")
    {
        return $this->sendOut($id, $message, $keyboard, $grid, 1, $parse_mode);
    }

    /**
     * Метод для создания подгрупп для клавиатуры
     *
     * @param array $array
     * @param int $number число делений, по умалчанию 2
     * 
     * @return array Возвращает новый массив
     */
    public function grid($array, $number = 2)
    {
        if ($number <= 0) {
            return [];
        }

        $result = [];

        $currentSubarray = [];

        foreach ($array as $element) {
            $currentSubarray[] = $element;
            if (count($currentSubarray) == $number) {
                $result[] = $currentSubarray;
                $currentSubarray = [];
            }
        }
        if (!empty($currentSubarray)) {
            $result[] = $currentSubarray;
        }

        return $result;
    }

    /**
     * Метод для рендеринга HTML сообщений
     *
     * @param array $data строки (необязательно).
     * 
     * @return string Возвращает строку
     */
    public function html($data = [])
    {
        return implode("\n", $data);
    }

}
