<?php

namespace Reijo\Telebot\Api\Trait;

trait MethodQuery
{
    /**
     * Устанавливает вебхук (Webhook) для бота.
     *
     */
    public function setWebhook()
    {
        return $this->method('setWebhook', [
            "url" => $this->getBot()["url"],
        ]);
    }

    /**
     * Удаляет вебхук (Webhook) для бота.
     *
     */
    public function removeWebhook()
    {
        return $this->method('setWebhook');
    }

    /**
     * Получает информацию о боте.
     *
     */
    public function getMe()
    {
        return $this->method('getMe');
    }

    /**
     * Получает профиль пользователя в Telegram.
     *
     * @param int $user_id Идентификатор пользователя.
     * @param int|null $offset Смещение в результате (необязательно).
     * @param int $limit Количество фотографий для получения (по умолчанию 100).
     *
     */
    public function getUserProfilePhotos($user_id, $offset = null, $limit = 100)
    {
        return $this->method('getUserProfilePhotos', [
            "user_id" => $user_id,
            "offset" => $offset,
            "limit" => $limit
        ])->body();
    }

    /**
     * Получает информацию о файле в Telegram.
     *
     * @param string $file_id Идентификатор файла.
     *
     */
    public function getFile($file_id)
    {
        return $this->method('getFile', [
            "file_id" => $file_id,
        ])->body();

    }


    /**
     * Отправляет текстовое сообщение в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $text Текст сообщения.
     * @param array|null $reply_markup Массив с настройками клавиатуры (необязательно).
     * @param string|null $parse_mode Режим HTML (необязательно).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param bool $disable_web_page_preview Отключить предпросмотр веб-страницы (по умолчанию false).
     *
     */
    public function sendMessage($chat_id, $text, $reply_markup = null, $parse_mode = null, $reply_to_message_id = null, $disable_notification = false, $disable_web_page_preview = false)
    {
        return $this->method('sendMessage', [
            "chat_id" => $chat_id,
            "text" => $text,
            "reply_markup" => $reply_markup,
            "parse_mode" => $parse_mode,
            "reply_to_message_id" => $reply_to_message_id,
            "disable_notification" => $disable_notification,
            "disable_web_page_preview" => $disable_web_page_preview
        ]);
    }

    /**
     * Редактирует сообщение из чата.
     *
     * @param int $chat_id Идентификатор чата.
     * @param int $message_id Идентификатор сообщения.
     *
     */

    public function editMessage($chat_id, $message_id, $text, $reply_markup = null, $parse_mode = null, $reply_to_message_id = null, $disable_notification = false, $disable_web_page_preview = false)
    {
        return $this->method('editMessageText', [
            "chat_id" => $chat_id,
            "message_id" => $message_id,
            "text" => $text,
            "reply_markup" => $reply_markup,
            "parse_mode" => $parse_mode,
            "reply_to_message_id" => $reply_to_message_id,
            "disable_notification" => $disable_notification,
            "disable_web_page_preview" => $disable_web_page_preview
        ]);
    }



    

    /**
     * Отправляет видео в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $video Путь к видеофайлу или URL видео.
     * @param string|null $caption Описание видео (необязательно).
     * @param int|null $duration Продолжительность видео в секундах (необязательно).
     * @param int|null $width Ширина видео в пикселях (необязательно).
     * @param int|null $height Высота видео в пикселях (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendVideo($chat_id, $video, $caption = null, $duration = null, $width = null, $height = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendVideo', [
            "chat_id" => $chat_id,
            'video' => $video,
            'duration' => $duration,
            'width' => $width,
            'height' => $height,
            'caption' => $caption,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id
        ]);
    }

    /**
     * Отправляет фотографию в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $photo Путь к фотографии или URL изображения.
     * @param string|null $caption Описание фотографии (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendPhoto($chat_id, $photo, $caption = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendPhoto', [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id
        ]);
    }

    /**
     * Пересылает сообщение из одного чата в другой.
     *
     * @param int $chat_id Идентификатор чата, в который будет переслано сообщение.
     * @param int $from_chat_id Идентификатор чата, из которого будет переслано сообщение.
     * @param int $message_id Идентификатор сообщения, которое будет переслано.
     * @param bool $disable_notification Отключить уведомления о пересылке (по умолчанию false).
     *
     */
    public function forwardMessage($chat_id, $from_chat_id, $message_id, $disable_notification = false)
    {
        return $this->method('forwardMessage', [
            "chat_id" => $chat_id,
            "from_chat_id" => $from_chat_id,
            "message_id" => $message_id,
            "disable_notification" => $disable_notification
        ]);
    }

    /**
     * Отправляет аудиофайл в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $audio Путь к аудиофайлу или URL аудио.
     * @param int|null $duration Продолжительность аудио в секундах (необязательно).
     * @param string|null $performer Исполнитель аудио (необязательно).
     * @param string|null $track Название трека (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendAudio($chat_id, $audio, $duration = null, $performer = null, $track = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendAudio', [
            "chat_id" => $chat_id,
            "audio" => $audio,
            "duration" => $duration,
            "performer" => $performer,
            "track" => $track,
            "disable_notification" => $disable_notification,
            "reply_to_message_id" => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет документ в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $document Путь к документу или URL документа.
     * @param string|null $caption Описание документа (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendDocument($chat_id, $document, $caption = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendDocument', [
            "chat_id" => $chat_id,
            "document" => $document,
            "caption" => $caption,
            "disable_notification" => $disable_notification,
            "reply_to_message_id" => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет стикер в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $sticker Путь к стикеру или URL стикера.
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendSticker($chat_id, $sticker, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendSticker', [
            "chat_id" => $chat_id,
            "sticker" => $sticker,
            "disable_notification" => $disable_notification,
            "reply_to_message_id" => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет голосовое сообщение в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $voice Путь к аудиофайлу голосового сообщения или URL аудио.
     * @param int|null $duration Продолжительность голосового сообщения в секундах (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendVoice($chat_id, $voice, $duration = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendVoice', [
            "chat_id" => $chat_id,
            "voice" => $voice,
            "duration" => $duration,
            "disable_notification" => $disable_notification,
            "reply_to_message_id" => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет информацию о местоположении в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param float $latitude Широта местоположения.
     * @param float $longitude Долгота местоположения.
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     */
    public function sendLocation($chat_id, $latitude, $longitude, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendLocation', [
            "chat_id" => $chat_id,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "disable_notification" => $disable_notification,
            "reply_to_message_id" => $reply_to_message_id,
        ]);
    }

    /**
     * Отвечает на callback query.
     *
     * @param string $callbackQueryId Уникальный идентификатор callback query.
     * @param string $text Текст ответа, который будет виден пользователю (по умолчанию: пустая строка).
     * @param bool $showAlert Если установлен в true, то у пользователя появится всплывающее уведомление с текстом ответа, даже если он не открыл чат с ботом (по умолчанию: false).
     * @param string $url URL, который будет открыт пользователем, если он нажмет на уведомление (по умолчанию: пустая строка).
     * @param int $cacheTime Время в секундах, в течение которого ответ на callback query считается актуальным и может быть использован повторно (по умолчанию: 0).
     */
    public function answerCallbackQuery($callbackQueryId, $text = '', $showAlert = false, $url = '', $cacheTime = 0)
    {
        $this->method("answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
            'url' => $url,
            'cache_time' => $cacheTime,
        ]);
    }


    /**
     * Отправляет информацию о действии в чате.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $action Тип действия (например, "typing" для "печатает").
     *
     */
    public function sendChatAction($chat_id, $action)
    {
        return $this->method('sendChatAction', [
            "chat_id" => $chat_id,
            "action" => $action
        ]);
    }

    /**
     * Удаляет сообщение из чата.
     *
     * @param int $chat_id Идентификатор чата.
     * @param int $message_id Идентификатор сообщения.
     *
     */
    public function deleteMessage($chat_id, $message_id)
    {
        return $this->method('deleteMessage', ["chat_id" => $chat_id, "message_id" => $message_id]);
    }
    
    /**
     * Отправляет счет на оплату в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $title Название продукта.
     * @param string $description Описание продукта.
     * @param string $payload Полезная нагрузка, которая будет передана в callback.
     * @param string $provider_token Токен провайдера платежей.
     * @param string $start_parameter Параметр для глубоких ссылок.
     * @param string $currency Валюта (например, "USD").
     * @param array $prices Массив цен (каждый элемент - это массив с ключами 'label' и 'amount').
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param string|null $photo_url URL фотографии продукта (необязательно).
     * @param int|null $photo_size Размер фотографии продукта (необязательно).
     * @param int|null $photo_width Ширина фотографии продукта (необязательно).
     * @param int|null $photo_height Высота фотографии продукта (необязательно).
     * @param bool $need_name Требуется ли имя пользователя (по умолчанию false).
     * @param bool $need_phone_number Требуется ли номер телефона пользователя (по умолчанию false).
     * @param bool $need_email Требуется ли email пользователя (по умолчанию false).
     * @param bool $need_shipping_address Требуется ли адрес доставки (по умолчанию false).
     * @param bool $send_phone_number_to_provider Отправить ли номер телефона провайдеру (по умолчанию false).
     * @param bool $send_email_to_provider Отправить ли email провайдеру (по умолчанию false).
     * @param bool $is_flexible Гибкие цены (по умолчанию false).
     *
     */
    public function sendInvoice($chat_id, $title, $description, $payload, $provider_token, $start_parameter, $currency, $prices, $reply_to_message_id = null, $disable_notification = false, $photo_url = null, $photo_size = null, $photo_width = null, $photo_height = null, $need_name = false, $need_phone_number = false, $need_email = false, $need_shipping_address = false, $send_phone_number_to_provider = false, $send_email_to_provider = false, $is_flexible = false)
    {
        return $this->method('sendInvoice', [
            "chat_id" => $chat_id,
            "title" => $title,
            "description" => $description,
            "payload" => $payload,
            "provider_token" => $provider_token,
            "start_parameter" => $start_parameter,
            "currency" => $currency,
            "prices" => $prices,
            "reply_to_message_id" => $reply_to_message_id,
            "disable_notification" => $disable_notification,
            "photo_url" => $photo_url,
            "photo_size" => $photo_size,
            "photo_width" => $photo_width,
            "photo_height" => $photo_height,
            "need_name" => $need_name,
            "need_phone_number" => $need_phone_number,
            "need_email" => $need_email,
            "need_shipping_address" => $need_shipping_address,
            "send_phone_number_to_provider" => $send_phone_number_to_provider,
            "send_email_to_provider" => $send_email_to_provider,
            "is_flexible" => $is_flexible,
        ]);
    }

}