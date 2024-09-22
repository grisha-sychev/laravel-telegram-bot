<?php

namespace Reijo\Telebot\Api\Trait;

/**
 * Trait Helpers
 */

trait Helpers
{
    private function firstword($str): string
    {
        preg_match("/^\/(\w+)/", $str, $matches);
        return isset($matches[0]) ? $matches[0] : "";
    }

    /**
     * Внутренний метод, проверяет, существует ли указанный ключ в массиве.
     *
     * @param mixed $body Массив, в котором проверяется наличие ключа.
     * @param string $key Ключ, который проверяется на наличие.
     * @return mixed Значение ключа, если он существует, в противном случае возвращает null.
     */
    private function isset($body, $key)
    {
        return isset($body->$key) ? $body->$key : null;
    }
}