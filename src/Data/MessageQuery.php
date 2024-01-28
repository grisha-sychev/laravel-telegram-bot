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
        $this->data = $data ?? null;
        $this->updateId = $data['update_id'] ?? null;
        $this->messageId = $data['message']['message_id'] ?? null;
        $this->fromId = $data['message']['from']['id'] ?? null;
        $this->fromIsBot = $data['message']['from']['is_bot'] ?? null;
        $this->fromFirstName = $data['message']['from']['first_name'] ?? null;
        $this->fromUsername = $data['message']['from']['username'] ?? null;
        $this->chatId = $data['message']['chat']['id'] ?? null;
        $this->chatFirstName = $data['message']['chat']['first_name'] ?? null;
        $this->chatUsername = $data['message']['chat']['username'] ?? null;
        $this->chatType = $data['message']['chat']['type'] ?? null;
        $this->messageDate = $data['message']['date'] ?? null;
        $this->messageText = $data['message']['text'] ?? null;
        $this->video = $data['message']['video'] ?? null;
        $this->audio = $data['message']['audio'] ?? null;
        $this->photo = $data['message']['photo'] ?? null;
        $this->sticker = $data['message']['sticker'] ?? null;
        $this->contact = $data['message']['contact'] ?? null;
        $this->location = $data['message']['location'] ?? null;
        $this->voice = $data['message']['voice'] ?? null;
        $this->document = $data['message']['document'] ?? null;
        $this->date = $data['message']['date'] ?? null;
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
