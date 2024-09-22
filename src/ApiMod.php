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
     * Метод для создания подгрупп для клавиатуры с возможностью ручного управления расположением кнопок
     *
     * @param array $array
     * @param int|array $layout Число делений или массив с ручным расположением.
     * 
     * @return array Возвращает новый массив
     */
    public function grid($array, $layout = 2)
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
    public function html($data = [])
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
