<?php

namespace Reijo\Telebot\Helpers;

use App\Models\Message;

/**
 * Класс цикла задач
 */
class Messages
{
    /**
     * Экземпляр бота
     *
     * @var mixed
     */
    private $bot;

    /**
     * Конструктор класса
     *
     * @param \Reijo\Telebot\ApiMod $bot Экземпляр бота
     */
    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    /**
     * Метод для получения значения последнего сообщения и выполнения callback
     *
     * @param string $value Значение сообщения
     * @param Closure $callback Функция обратного вызова
     * 
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function getMessage($value, $callback): mixed
    {
        $messageText = $this->bot->getMessageText();
        $cb = $this->bot->getCallbackData();

        if (!empty($messageText) && $messageText !== '' && !$cb && !is_object($cb)) {
            $message = Message::where('id_user', $this->bot->getUserId())->first();

            if ($value === $message->text) {
                return $callback($value, $message);
            }
        }

        return null;
    }

    /**
     * Метод для установки значения сообщения
     *
     * @param mixed $value Значение сообщения
     * @return void
     */
    public function setMessage($value): void
    {
        $step = Message::where('id_user', $this->bot->getUserId())->first();

        if (!$step) {
            $step = new Message;
            $step->id_user = $this->bot->getUserId();
            $step->text = $value;
            $step->save();
        } else {
            $step->text = $value;
            $step->save();
        }
    }
}
