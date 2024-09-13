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
     * Метод для получения значения последнего сообщения
     *
     * @return string|null Возвращает сообщение или null, если шаг не существует
     */
    public function getMessage(): string|null
    {
        $message = Message::where('id_user', $this->bot->getUserId())->first();
        return $message ? $message->text : null;
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
