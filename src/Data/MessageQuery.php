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
        $this->data = $data;

        $this->updateId = $data['update_id'] && $data['update_id'];

        if (isset($data['message'])) {
            $message = $data['message'];

            $this->messageId = $message['message_id'];
            $this->fromId = $message['from']['id'];
            $this->fromIsBot = $message['from']['is_bot'];
            $this->fromFirstName = $message['from']['first_name'];
            $this->fromUsername = $message['from']['username'];
            $this->chatId = $message['chat']['id'];
            $this->chatFirstName = $message['chat']['first_name'];
            $this->chatUsername = $message['chat']['username'];
            $this->chatType = $message['chat']['type'];
            $this->messageDate = $message['date'];
            $this->messageText = $message['text'];
            $this->video = $message['video'] ?? null;
            $this->audio = $message['audio'] ?? null;
            $this->photo = $message['photo'] ?? null;
            $this->sticker = $message['sticker'] ?? null;
            $this->contact = $message['contact'] ?? null;
            $this->location = $message['location'] ?? null;
            $this->voice = $message['voice'] ?? null;
            $this->document = $message['document'] ?? null;
            $this->date = $message['date'] ?? null;
        }
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
