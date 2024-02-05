<?php

namespace Reijo\Telebot\Api\Trait;

use Reijo\Telebot\Data\MessageQuery;
use Reijo\Telebot\Data\CallbackQuery;

/**
 * Trait User
 *
 * Этот трейт (trait) предоставляет методы для работы с пользователями в боте Telegram.
 */
trait User
{
    private $user;

    /**
     * Получает данные о пользователе из запроса.
     *
     * @return CallbackQuery|MessageQuery
     */
    public function user()
    {
        return $this->request();
    }

    /**
     * Получает ID пользователя.
     *
    //  * @return int
     */
    public function getUserId()
    {
        return $this->getUser()->getFromId();
    }

    /**
     * Проверяет, является ли пользователь ботом.
     *
     * @return bool
     */
    public function isUserBot()
    {
        return $this->getUser()->getFromIsBot();
    }

    /**
     * Получает логин пользователя.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->getUser()->getFromUsername();
    }

    /**
     * Получает имя пользователя.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getUser()->getFromFirstName();
    }

    /**
     * Получает тип чата пользователя.
     *
     * @return string|null
     */
    public function getChatType()
    {
        return $this->getUser()->getChatType();
    }

    /**
     * Получает file_id аватара пользователя.
     * TODO: Надо создать обьект photos и переделать это недоразумение
     * @return string|null
     */
    public function getUserAvatarFileId()
    {
        $userProfilePhotos = json_decode($this->getUserProfilePhotos($this->getUserId()), true);

        // Проверяем наличие фото
        if (isset($userProfilePhotos['result']['photos']) && !empty($userProfilePhotos['result']['photos'])) {
            // Получаем file_id первой фотографии
            return $userProfilePhotos['result']['photos'][0][0]['file_id'];
        } else {
            // Возвращаем null или другое значение, если фото отсутствует
            return null;
        }
    }



    /**
     * Получает id сообщения.
     *
     * @return string|null
     */
    public function getMessageId()
    {
        return $this->getUser()->getMessageId();
    }

    /**
     * Получает текст сообщения.
     *
     * @return string|null
     */
    public function getMessageText()
    {
        return $this->getUser()->getMessageText();
    }

    /**
     * Получает дату сообщения.
     *
     * @return int|null
     */
    public function getMessageDate()
    {
        return $this->getUser()->getMessageDate();
    }

    /**
     * Получает объект медиа (фото, видео, аудио и т.д.) из сообщения.
     *
     * @return object|null
     */
    public function getMedia()
    {
        $user = $this->getUser();

        if (
            method_exists($user, 'getPhoto') &&
            method_exists($user, 'getVideo') &&
            method_exists($user, 'getAudio') &&
            method_exists($user, 'getSticker') &&
            method_exists($user, 'getContact') &&
            method_exists($user, 'getLocation') &&
            method_exists($user, 'getVoice') &&
            method_exists($user, 'getDocument')
        ) {
            return (object) [
                'photo' => $user->getPhoto(),
                'video' => $user->getVideo(),
                'audio' => $user->getAudio(),
                'sticker' => $user->getSticker(),
                'contact' => $user->getContact(),
                'location' => $user->getLocation(),
                'voice' => $user->getVoice(),
                'document' => $user->getDocument(),
            ];
        }

        return null;
    }

    /**
     * Получает данные о чате.
     *
     * @return object|null
     */
    public function getChatData()
    {
        return (object) [
            'chat_id' => $this->getUser()->getChatId(),
            'first_name' => $this->getUser()->getChatFirstName(),
            'username' => $this->getUser()->getChatUsername(),
        ];
    }

    /**
     * Получает данные о колбеке (если доступно).
     *
     * @return object|null
     */
    public function getCallbackData()
    {

        $user = $this->getUser();

        if ($user && method_exists($user, 'getCallbackQueryId') && method_exists($user, 'getCallbackData')) {
            return (object) [
                'callback_query_id' => $user->getCallbackQueryId(),
                'callback_data' => $user->getCallbackData(),
            ];
        }
        
        return null;
    }

    /**
     * Получает все данные о пользователе.
     *
     * @return object
     */
    public function getAllUserData()
    {
        return (object) array_merge(
            (array) $this->getChatData(),
            (array) $this->getMedia(),
            (array) $this->getCallbackData(),
            ['user_id' => $this->getUserId()],
            ['is_bot' => $this->isUserBot()],
            ['username' => $this->getUsername()],
            ['chat_type' => $this->getChatType()],
            ['message_text' => $this->getMessageText()],
            ['message_date' => $this->getMessageDate()]
        );
    }

    /**
     * Получает объект пользователя.
     *
     * @return CallbackQuery|MessageQuery
     */
    protected function getUser()
    {
        if ($this->user === null) {
            $this->setUser($this->request());
        }

        return $this->user;
    }

    /**
     * Устанавливает объект пользователя.
     *
     * @param CallbackQuery|MessageQuery $user
     */
    protected function setUser($user)
    {
        $this->user = $user;
    }
}

