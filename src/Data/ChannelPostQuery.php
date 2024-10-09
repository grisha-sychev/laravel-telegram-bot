<?php

namespace Tgb\Data;

class ChannelPostQuery
{
    private $update_id;
    private $channel_post;

    public function __construct($data)
    {
        $this->update_id = $data['update_id'];
        $this->channel_post = $data['channel_post'];
    }

    public function getUpdateId()
    {
        return $this->update_id;
    }

    public function getChannelPost()
    {
        return $this->channel_post;
    }

    public function getMessageId()
    {
        return $this->channel_post['message_id'];
    }

    public function getSenderChat()
    {
        return $this->channel_post['sender_chat'];
    }

    public function getChat()
    {
        return $this->channel_post['chat'];
    }

    public function getDate()
    {
        return $this->channel_post['date'];
    }

    public function getText()
    {
        return $this->channel_post['text'];
    }
}
