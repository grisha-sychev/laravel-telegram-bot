<?php

namespace Reijo\Telebot\Api\Trait;

use Reijo\Telebot\Data\CallbackQuery;
use Reijo\Telebot\Data\MessageQuery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

/**
 * Trait Program
 *
 * Этот трейт (trait) предоставляет низкоуровенвые методы Telegram API.
 */
trait Program
{
    private $response;
    /**
     * Создает и возвращает инициализированный cURL-ресурс для выполнения запроса к API Telegram.
     *
     * @param string $method Метод API Telegram.
     * @param array $query Параметры запроса (необязательно).
     *
     */
    public function method($method, $query = [])
    {
        $this->response = Http::withoutVerifying()->get("https://api.telegram.org/bot" . $this->token . "/" . $method . ($query ? '?' . http_build_query($query) : ''));
        return $this->response;
    }

    public function file($fial_path)
    {
        return "https://api.telegram.org/file/bot" . $this->token . "/" . $fial_path;
    }

    public function body()
    {
        $this->response->body();
        return $this;
    }

    /**
     * Получает все данные запроса от Telegram и возвращает их в виде массива.
     *
     * Данные запроса от Telegram в виде массива.
     */
    public function request()
    {
        $all = Request::json()->all();

        if (isset($all['callback_query'])) {
            return new CallbackQuery($all);
        } else {
            return new MessageQuery($all);
        }
    }

}
