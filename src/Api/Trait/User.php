<?php

namespace Reijo\Telebot\Api\Trait;

use Reijo\Telebot\Api\Data\MessageQuery;
use Reijo\Telebot\Api\Data\CallbackQuery;

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
        $avatar = json_decode($this->getUserProfilePhotos($this->getUserId()), true);
        return $avatar['result']['photos'][0][1]['file_id'];
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
        return (object) [
            'photo' => $this->getUser()->getPhoto(),
            'video' => $this->getUser()->getVideo(),
            'audio' => $this->getUser()->getAudio(),
            'sticker' => $this->getUser()->getSticker(),
            'contact' => $this->getUser()->getContact(),
            'location' => $this->getUser()->getLocation(),
            'voice' => $this->getUser()->getVoice(),
            'document' => $this->getUser()->getDocument(),
        ];
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
        return (object) [
            'callback_query_id' => $this->getUser()->getCallbackQueryId(),
            'callback_data' => $this->getUser()->getCallbackData(),
        ];
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

