<?php

namespace Reijo\Telebot\Api\Data;

class CallbackQuery
{
    private $data;
    private $updateId;
    private $callbackQueryId;
    private $fromId;
    private $fromIsBot;
    private $fromFirstName;
    private $fromUsername;
    private $messageId;
    private $messageFromId;
    private $messageIsBot;
    private $messageFirstName;
    private $messageUsername;
    private $chatId;
    private $chatFirstName;
    private $chatUsername;
    private $chatType;
    private $messageDate;
    private $messageText;
    private $inlineKeyboard;
    private $chatInstance;
    private $callbackData;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->updateId = $data['update_id'];
        $this->callbackQueryId = $data['callback_query']['id'];
        $this->fromId = $data['callback_query']['from']['id'];
        $this->fromIsBot = $data['callback_query']['from']['is_bot'];
        $this->fromFirstName = $data['callback_query']['from']['first_name'];
        $this->fromUsername = $data['callback_query']['from']['username'];
        $this->messageId = $data['callback_query']['message']['message_id'];
        $this->messageFromId = $data['callback_query']['message']['from']['id'];
        $this->messageIsBot = $data['callback_query']['message']['from']['is_bot'];
        $this->messageFirstName = $data['callback_query']['message']['from']['first_name'];
        $this->messageUsername = $data['callback_query']['message']['from']['username'];
        $this->chatId = $data['callback_query']['message']['chat']['id'];
        $this->chatFirstName = $data['callback_query']['message']['chat']['first_name'];
        $this->chatUsername = $data['callback_query']['message']['chat']['username'];
        $this->chatType = $data['callback_query']['message']['chat']['type'];
        $this->messageDate = $data['callback_query']['message']['date'];
        $this->messageText = $data['callback_query']['message']['text'];
        $this->inlineKeyboard = $data['callback_query']['message']['reply_markup']['inline_keyboard'];
        $this->chatInstance = $data['callback_query']['chat_instance'];
        $this->callbackData = $data['callback_query']['data'];
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

    public function getCallbackQueryId()
    {
        return $this->callbackQueryId;
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

    public function getMessageId()
    {
        return $this->messageId;
    }

    public function getMessageFromId()
    {
        return $this->messageFromId;
    }

    public function getMessageIsBot()
    {
        return $this->messageIsBot;
    }

    public function getMessageFirstName()
    {
        return $this->messageFirstName;
    }

    public function getMessageUsername()
    {
        return $this->messageUsername;
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

    public function getInlineKeyboard()
    {
        return $this->inlineKeyboard;
    }

    public function getChatInstance()
    {
        return $this->chatInstance;
    }

    public function getCallbackData()
    {
        return $this->callbackData;
    }
}
