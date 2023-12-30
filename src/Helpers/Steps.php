<?php

namespace Reijo\Telebot\Helpers;

use App\Models\Step;
use App\Models\User;

/**
 * Класс цикла задач
 */
class Steps
{
    /**
     * Имя цикла
     *
     * @var string
     */
    private $name;

    /**
     * Экземпляр бота
     *
     * @var mixed
     */
    private $bot;
    /**
     * Экземпляр бота
     *
     * @var mixed
     */
    private $miss = false;

    /**
     * Конструктор класса
     *
     * @param string $name Имя цикла
     * @param \Reijo\Telebot\ApiMod $bot Экземпляр бота
     */
    public function __construct($name = '', $bot)
    {
        $this->name = $name;
        $this->bot = $bot;
    }

    /**
     * Метод для работы с шагами цикла
     *
     * @param int $body Текст шага (необязательный)
     * @return int|null Числовое значение текущего шага (если $body не передан), иначе устанавливает новый шаг
     */
    public function step($body = 0)
    {
        if ($body === 0) {
            return $this->getStep();
        } else {
            $this->setStep($this->name, $body);
        }
    }

    /**
     * Для начала работы с циклом
     */
    public function start()
    {
        $this->step(1);
    }

    /**
     * Метод для выполнения действий в цикле
     *
     * @param int|array $count Количество шагов, на котором выполнится действие
     * @param callable $callback Функция, выполняемая на каждом шаге
     * @param bool $miss Флаг, указывающий на необходимость пропуска цикла
     * @return $this
     */
    public function round($count, $callback, $miss = false, $skipstep = false)
    {
        if (is_array($count)) {
            $count = $count[0];
            $skipstep = $count[1];
        }

        if ($this->name === $this->getCurrentStep()) {

            $data = new \stdClass;
            $this->miss === true && $miss = true;
            $callback = $callback->bindTo($this, $this);
            $api = $this->bot;
            // TODO: на любую информацию вместо getMessageText
            $data->message = $api->getMessageText() ?: null;
            // $data->callback = $api->getCallbackData() ?: null;

            if ($this->getStep() === null) {
                $this->step(1);
                if ($this->getStep() === $count) {
                    if ($callback($data) !== false) {
                        $skipstep ?: $this->step($this->getStep() + 1);
                        $miss ?: die;
                    }
                }
            } else {
                if ($this->getStep() === $count) {
                    if ($callback($data) !== false) {
                        $skipstep ?: $this->step($this->getStep() + 1);
                        $miss ?: die;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Метод для получения значения шага
     *
     * @param string $key Ключ шага
     * @return int|null Возвращает значение шага или null, если шаг не существует
     */
    public function getStep(): int|null
    {
        $key = $this->bot->getUserId() . '_' . $this->name;
        $step = Step::where('key_id', $key)->first();
        return $step ? $step->body : null;
    }

    public function stage($int)
    {
        $this->step($int);
        $this->refresh();
        die;
    }

    public function miss()
    {
        $this->miss = true;
        $this->refresh();
        return $this;
    }


    public function getCurrentStep()
    {
        return User::getStep($this->bot->getUserId());
    }

    public function setCurrentStep($step)
    {
        $user = User::getUser($this->bot->getUserId());
        $user->step = $step;
        $user->save();
    }

    public function refresh()
    {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        return $this;
    }
    /**
     * Метод для установки значения шага
     *
     * @param string $key Ключ шага
     * @param mixed $value Значение шага
     * @return void
     */
    public function setStep($key, $value): void
    {
        $key = $this->bot->getUserId() . '_' . $key;
        $step = Step::where('key_id', $key)->first();

        if (!$step) {
            $step = new Step;
            $step->key_id = $key;
            $step->body = $value;
            $step->save();
        } else {
            $step->body = $value;
            $step->save();
        }
    }
}
