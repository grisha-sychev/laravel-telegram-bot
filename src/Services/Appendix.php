<?php

namespace Tgb\Services;


/**
 * Класс Appendix предоставляет методы для работы с SDK.
 */
class Appendix
{
    /**
     * Извлекает первое слово из заданной строки.
     *
     * @param string $str Входная строка.
     * @return string Первое слово из входной строки или пустая строка, если совпадение не найдено.
     */
    public static function firstWord(string $str): string
    {
        preg_match("/^\/(\w+)/", $str, $matches);
        return isset($matches[1]) ? $matches[1] : "";
    }

    /**
     * Извлекает последнее слово из заданной строки, исключая определенную команду.
     *
     * @param string $str Входная строка.
     * @param string $command Команда, которую нужно исключить из входной строки.
     * @return string Последнее слово из входной строки или пустая строка, если входная строка совпадает с командой.
     */
    public static function lastWord(string $str, string $command): string
    {
        if ($str === $command) {
            return '';
        }
        preg_match('/(\S+)\s(.+)/', $str, $matches);
        return isset($matches[2]) ? $matches[2] : "";
    }

    /**
     * Добавляет новый элемент в коллекцию.
     *
     * @param mixed $item Элемент, который нужно добавить в коллекцию.
     * @return void
     */
    public static function issetKey($body, string $key)
    {
        return isset($body->$key) ? $body->$key : null;
    }

}
