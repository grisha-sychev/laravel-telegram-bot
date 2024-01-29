<?php

namespace Reijo\Telebot\Data;

class MessageQuery
{
    private $data;
    private $updateId;
    private $messageId;
    private $fromId;
    private $fromIsBot;
    private $fromFirstName;
    private $fromUsername;
    private $chatId;
    private $chatFirstName;
    private $chatUsername;
    private $chatType;
    private $messageDate;
    private $messageText;
    private $video;
    private $audio;
    private $photo;
    private $sticker;
    private $contact;
    private $location;
    private $voice;
    private $document;
    private $date;

    public function __construct(array $data)
    {
        $this->data = $data ?? '';
        $this->updateId = $data['update_id'] ?? '';
        $this->messageId = $data['message']['message_id'] ?? '';
        $this->fromId = $data['message']['from']['id'] ?? '';
        $this->fromIsBot = $data['message']['from']['is_bot'] ?? '';
        $this->fromFirstName = $data['message']['from']['first_name'] ?? '';
        $this->fromUsername = $data['message']['from']['username'] ?? '';
        $this->chatId = $data['message']['chat']['id'] ?? '';
        $this->chatFirstName = $data['message']['chat']['first_name'] ?? '';
        $this->chatUsername = $data['message']['chat']['username'] ?? '';
        $this->chatType = $data['message']['chat']['type'] ?? '';
        $this->messageDate = $data['message']['date'] ?? '';
        $this->messageText = $data['message']['text'] ?? '';
        $this->video = $data['message']['video'] ?? '';
        $this->audio = $data['message']['audio'] ?? '';
        $this->photo = $data['message']['photo'] ?? '';
        $this->sticker = $data['message']['sticker'] ?? '';
        $this->contact = $data['message']['contact'] ?? '';
        $this->location = $data['message']['location'] ?? '';
        $this->voice = $data['message']['voice'] ?? '';
        $this->document = $data['message']['document'] ?? '';
        $this->date = $data['message']['date'] ?? '';
    }
    public function getAll()
    {
        $this->data;
        return $this;
    }
    public function getUpdateId()
    {
        return $this->updateId;
    }

    public function getMessageId()
    {
        return $this->messageId;
    }

    public function getFromId()
    {
        return $this->fromId;
    }

    public function getFromIsBot()
    {
        return $this->fromIsBot;
    }

    public function getFromFirstName()
    {
        return $this->fromFirstName;
    }

    public function getFromUsername()
    {
        return $this->fromUsername;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function getChatFirstName()
    {
        return $this->chatFirstName;
    }

    public function getChatUsername()
    {
        return $this->chatUsername;
    }

    public function getChatType()
    {
        return $this->chatType;
    }

    public function getMessageDate()
    {
        return $this->messageDate;
    }

    public function getMessageText()
    {
        return $this->messageText;
    }

    public function getVideo()
    {
        return $this->video;
    }

    public function getAudio()
    {
        return $this->audio;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getSticker()
    {
        return $this->sticker;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getVoice()
    {
        return $this->voice;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function getDate()
    {
        return $this->date;
    }
}
