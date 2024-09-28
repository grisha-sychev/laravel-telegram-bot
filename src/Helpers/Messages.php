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
     * @param \Reijo\Telebot\Base\Bot $bot Экземпляр бота
     */
    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    /**
     * Метод для получения значения последнего сообщения и выполнения callback
     *
     * @param string|array $pattern Шаблон сообщения или массив шаблонов
     * @param Closure $callback Функция обратного вызова
     * 
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function message($pattern, $callback): mixed
    {
        $messageText = $this->bot->getMessageText();


        $cb = $this->bot->getCallbackData();

        // Проверяем, что это не callback и сообщение не пусто
        if (!empty($cb) && empty($messageText)) {
            return null;
        }

        $patterns = is_array($pattern) ? $pattern : [$pattern];
        $message = Message::where('id_user', $this->bot->getUserId())->first();

        foreach ($patterns as $singlePattern) {
            // Проверяем, является ли паттерн регулярным выражением
            $isRegex = preg_match('/^\/.*\/[a-z]*$/i', $singlePattern);

            // Если это не регулярное выражение, преобразуем паттерн с параметрами в регулярное выражение
            if (!$isRegex) {
                $singlePattern = str_replace(['{', '}'], ['(?P<', '>[^}]+)'], $singlePattern);
                $singlePattern = "/^" . str_replace('/', '\/', $singlePattern) . "$/";
            }

            if (preg_match($singlePattern, $message->text, $matches)) {
                // Извлекаем только значения параметров из совпавших данных и передаем их в функцию-обработчик
                $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Вызываем функцию-обработчик с параметрами
                $callback(...$parameters);
                exit; // Завершаем выполнение скрипта после выполнения callback
            }
        }

        return null;
    }

    public function getMessage()
    {
        return Message::where('id_user', $this->bot->getUserId())->first();
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
