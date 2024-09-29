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

    /**
     * Внутренний метод, проверяет существование фотографии в сообщении и возвращает ее данные по указанному размеру.
     *
     * @param string $size Размер фотографии ('S', 'M' или 'L').
     * @param string $key Ключ, по которому проверяется наличие фотографии.
     * @return mixed Значение ключа, если фотография существует, в противном случае возвращает null.
     */
    public static function issetPhoto($get, $size, $key)
    {
        $set = $get[$size][$key];
        $res = isset($set) ? $set : null;

        switch ($size) {
            case 0:
                return $res;
            case 1:
                return $res;
            case 2:
                return $res;
        }
    }
}
