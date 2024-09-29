<?php

namespace Tgb\Tgb\Api\Trait;

/**
 * Trait Photo
 */

trait Photo
{
    /**
     * Внутренний метод, проверяет существование фотографии в сообщении и возвращает ее данные по указанному размеру.
     *
     * @param string $size Размер фотографии ('S', 'M' или 'L').
     * @param string $key Ключ, по которому проверяется наличие фотографии.
     * @return mixed Значение ключа, если фотография существует, в противном случае возвращает null.
     */
    private function issetPhoto($size, $key)
    {
        $set = $this->getPhoto()[$size][$key];
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

    /**
     * @return array Массив с данными о фотографии.
     */
    public function getPhoto()
    {
        return $this->isset($this->getUserProfilePhotos($this->getUserId()), 0);
    }


    /**
     * @param string $size Размер фотографии ('S', 'M' или 'L' - то есть default).
     * @return 
     */
    public function getPhotoId($size = 'M')
    {
        switch ($size) {
            case 'S':
                return $this->issetPhoto(0, 'file_id');
            case 'M':
                return $this->issetPhoto(1, 'file_id');
            case 'L':
                return $this->issetPhoto(2, 'file_id');
        }
    }
}