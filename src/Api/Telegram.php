<?php

namespace Tgb\Api;

use Tgb\Services\Appendix;

use Tgb\Data\CallbackQuery;
use Tgb\Data\MessageQuery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Tgb\Data\PreCheckoutQuery;

/**
 * Класс Api Client для управления Telegram ботом.
 */
class Telegram
{
    /**
     * @var \Illuminate\Http\Client\Response|null $response HTTP-ответ от API Telegram.
     */
    private ?\Illuminate\Http\Client\Response $response = null;

    /**
     * @var CallbackQuery|MessageQuery|PreCheckoutQuery|null $user Информация о пользователе, которая может быть либо CallbackQuery, либо MessageQuery, или null, если не установлена.
     */
    private CallbackQuery|MessageQuery|PreCheckoutQuery|null $user = null;

    /**
     * @var string|null $bot Идентификатор бота.
     */
    public ?string $bot = null;

    /**
     * @var string|null $command Команда для выполнения.
     */
    public ?string $command = null;

    /**
     * @var \Closure|null $callback Функция обратного вызова для выполнения.
     */
    public ?\Closure $callback = null;

    /**
     * @var string|null $message Содержимое сообщения.
     */
    public ?string $message = null;

    /**
     * @var string|null $domain Домен, связанный с ботом.
     */
    public ?string $domain = null;

    /**
     * Конструктор класса Client.
     *
     * @param string|null $bot Имя бота (необязательный параметр).
     */
    public function __construct()
    {
        $this->bot = $bot ?? $this->bot;
        $this->domain = $domain ?? $this->domain;
    }

    /**
     * Создает и возвращает инициализированный cURL-ресурс для выполнения запроса к API Telegram.
     *
     * @param string $method Метод API Telegram.
     * @param array $query Параметры запроса (необязательно).
     *
     */
    public function method($method, $query = [])
    {
        $url = "https://api.telegram.org/bot" . $this->getToken() . "/" . $method . ($query ? '?' . http_build_query($query) : '');

        $this->response = Http::withoutVerifying()->get($url);
        return $this->response;
    }

    /**
     * Возвращает URL для доступа к файлу на сервере Telegram.
     *
     * @param string $fial_path Путь к файлу на сервере Telegram.
     * @return string URL для доступа к файлу.
     */
    public function file($fial_path)
    {
        return "https://api.telegram.org/file/bot" . $this->getToken() . "/" . $fial_path;
    }

    /**
     * Возвращает тело ответа и текущий объект.
     *
     * @return $this Текущий объект для цепочки вызовов.
     */
    public function body()
    {
        $this->response->body();
        return $this;
    }

    /**
     * Получает все данные запроса от Telegram и возвращает их в виде массива.
     *
     * Данные запроса от Telegram в виде массива.
     */
    public function request()
    {
        $all = Request::json()->all();

        switch (true) {
            case isset($all['callback_query']):
                return new CallbackQuery($all);
            case isset($all['pre_checkout_query']):
                return new PreCheckoutQuery($all);
            default:
                return new MessageQuery($all);
        }
    }

    /**
     * Определяет бота по индификатору
     */
    private function getToken(): string
    {
        return config('tgb.' . $this->bot);
    }

    /**
     * Отправляет сообщение с инлайн-клавиатурой.
     *
     * @param array $keyboard Массив с настройками клавиатуры.
     *
     * @return string JSON-представление инлайн-клавиатуры.
     */
    public function inlineKeyboard($keyboard)
    {
        return json_encode(['inline_keyboard' => $keyboard]);
    }

    /**
     * Отправляет сообщение с обычной клавиатурой.
     *
     * @param array $keyboard Массив с настройками клавиатуры.
     * @param bool $one_time_keyboard Параметр одноразовой клавиатуры (по умолчанию true).
     * @param bool $resize_keyboard Параметр изменения размера клавиатуры (по умолчанию true).
     *
     * @return string JSON-представление обычной клавиатуры.
     */
    public function keyboard($keyboard, $one_time_keyboard = true, $resize_keyboard = true)
    {
        return json_encode([
            'keyboard' => $keyboard,
            'one_time_keyboard' => $one_time_keyboard,
            'resize_keyboard' => $resize_keyboard
        ]);
    }

    /**
     * Аругмент любой команды.
     * 
     * @return int|string|null Результат выполнения функции-обработчика.
     */
    public function argument()
    {
        if ($this->command === Appendix::firstword($this->getMessageText())) {
            return Appendix::lastword($this->getMessageText(), $this->command);
        }
    }

    /**
     * Устанавливает вебхук (Webhook) для бота.
     *
     */
    public function setWebhook()
    {
        $host = $this->domain ?? $_SERVER['SERVER_NAME'];

        return $this->method('setWebhook', [
            "url" => 'https://' . $host . '/bot/' . $this->getToken(),
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
     * Получает данные о пользователе из запроса.
     *
     * @return CallbackQuery|MessageQuery|PreCheckoutQuery
     */
    public function user()
    {
        return $this->request();
    }


    /**
     * Получает идентификатор пользователя.
     *
     * @return int Идентификатор пользователя.
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
     * Получает ссылку на аватраку пользователя
     */
    public function getUserAvatarUrl()
    {
        $userAvatarFileId = $this->getUserAvatarFileId();

        // Проверяем наличие file_id
        if ($userAvatarFileId) {
            $photo = json_decode($this->getFile($userAvatarFileId), true);

            // Проверяем наличие информации о файле
            if (isset($photo['result']['file_path'])) {
                return $this->file($photo['result']['file_path']);
            }
        }

        // Возвращаем null или другое значение, если информация о файле отсутствует
        return null;
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
        $user = $this->getUser();
        if (method_exists($user, 'getMessageText')) {
            return $user->getMessageText();
        }
        return null;
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
     * Получает данные о предварительном запросе на оплату.
     *
     * @return PreCheckoutQuery|null
     */
    public function getPreCheckoutData()
    {
        $request = $this->request();
        if ($request && method_exists($request, 'getPreCheckoutQuery')) {
            return $request->getPreCheckoutQuery();
        }
        return null;
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
     * @param string $size Размер фотографии ('S', 'M' или 'L' - то есть default).
     * @return 
     */
    public function getPhotoId($size = 'M')
    {
        switch ($size) {
            case 'S':
                return Appendix::issetPhoto($this->getPhoto(), 0, 'file_id');
            case 'M':
                return Appendix::issetPhoto($this->getPhoto(), 1, 'file_id');
            case 'L':
                return Appendix::issetPhoto($this->getPhoto(), 2, 'file_id');
        }
    }

    /**
     * @return array Массив с данными о фотографии.
     */
    public function getPhoto()
    {
        return Appendix::issetKey($this->getUserProfilePhotos($this->getUserId()), 0);
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
     * @return CallbackQuery|MessageQuery|PreCheckoutQuery
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
     * @param CallbackQuery|MessageQuery|PreCheckoutQuery $user
     */
    protected function setUser($user)
    {
        $this->user = $user;
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
     * Отвечает на pre-checkout query.
     *
     * @param string $pre_checkout_query_id Уникальный идентификатор pre-checkout query.
     * @param bool $ok Укажите True, если все в порядке (товары доступны и т.д.) и бот готов продолжить выполнение заказа. Укажите False, если есть какие-либо проблемы.
     * @param string|null $error_message Сообщение об ошибке в читаемом виде, объясняющее причину невозможности продолжить оформление заказа (обязательно, если ok равно False).
     *
     * @return bool True в случае успеха.
     */
    public function answerPreCheckoutQuery($pre_checkout_query_id, $ok, $error_message = null)
    {
        $params = [
            'pre_checkout_query_id' => $pre_checkout_query_id,
            'ok' => $ok,
        ];

        if (!$ok && $error_message) {
            $params['error_message'] = $error_message;
        }

        return $this->method('answerPreCheckoutQuery', $params)->successful();
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
        return $this->method('deleteMessage', [
            "chat_id" => $chat_id,
            "message_id" => $message_id
        ]);
    }

    /**
     * Отправляет счет на оплату в чат.
     *
     * @param int|string $chat_id Уникальный идентификатор целевого чата или имя пользователя целевого канала (в формате @channelusername).
     * @param string $title Название продукта, 1-32 символов.
     * @param string $description Описание продукта, 1-255 символов.
     * @param string $payload Полезная нагрузка, определенная ботом, 1-128 байт. Это не будет отображаться пользователю, используйте это для ваших внутренних процессов.
     * @param string $provider_token Токен провайдера платежей, полученный через @BotFather. Передайте пустую строку для платежей в Telegram Stars.
     * @param string $currency Трехбуквенный код валюты ISO 4217. Передайте "XTR" для платежей в Telegram Stars.
     * @param array $prices Разбивка цен, JSON-сериализованный список компонентов (например, цена продукта, налог, скидка, стоимость доставки, налог на доставку, бонус и т.д.). Должен содержать ровно один элемент для платежей в Telegram Stars.
     * @param int|null $max_tip_amount Максимально допустимая сумма чаевых в наименьших единицах валюты (целое число, не float/double). По умолчанию 0. Не поддерживается для платежей в Telegram Stars.
     * @param array|null $suggested_tip_amounts JSON-сериализованный массив предложенных сумм чаевых в наименьших единицах валюты (целое число, не float/double). Максимум 4 предложенные суммы чаевых.
     * @param string|null $start_parameter Уникальный параметр глубоких ссылок.
     * @param string|null $provider_data JSON-сериализованные данные о счете, которые будут переданы провайдеру платежей.
     * @param string|null $photo_url URL фотографии продукта для счета.
     * @param int|null $photo_size Размер фотографии в байтах.
     * @param int|null $photo_width Ширина фотографии.
     * @param int|null $photo_height Высота фотографии.
     * @param bool $need_name Требуется ли полное имя пользователя для завершения заказа.
     * @param bool $need_phone_number Требуется ли номер телефона пользователя для завершения заказа.
     * @param bool $need_email Требуется ли email пользователя для завершения заказа.
     * @param bool $need_shipping_address Требуется ли адрес доставки.
     * @param bool $send_phone_number_to_provider Отправить ли номер телефона пользователя провайдеру.
     * @param bool $send_email_to_provider Отправить ли email пользователя провайдеру.
     * @param bool $is_flexible Гибкие цены.
     * @param bool $disable_notification Отправить сообщение без звука.
     * @param bool $protect_content Защитить содержимое отправленного сообщения от пересылки и сохранения.
     * @param string|null $message_effect_id Уникальный идентификатор эффекта сообщения, который будет добавлен к сообщению; только для личных чатов.
     * @param array|null $reply_parameters Описание сообщения, на которое нужно ответить.
     * @param array|null $reply_markup JSON-сериализованный объект для встроенной клавиатуры. Если пусто, будет показана одна кнопка "Оплатить общую сумму". Если не пусто, первая кнопка должна быть кнопкой оплаты.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function sendInvoice(
        $chat_id,
        $title,
        $description,
        $payload,
        $provider_token,
        $currency,
        $prices,
        $max_tip_amount = null,
        $suggested_tip_amounts = null,
        $start_parameter = null,
        $provider_data = null,
        $photo_url = null,
        $photo_size = null,
        $photo_width = null,
        $photo_height = null,
        $need_name = false,
        $need_phone_number = false,
        $need_email = false,
        $need_shipping_address = false,
        $send_phone_number_to_provider = false,
        $send_email_to_provider = false,
        $is_flexible = false,
        $disable_notification = false,
        $protect_content = false,
        $message_effect_id = null,
        $reply_parameters = null,
        $reply_markup = null
    ) {
        return $this->method('sendInvoice', [
            "chat_id" => $chat_id,
            "title" => $title,
            "description" => $description,
            "payload" => $payload,
            "provider_token" => $provider_token,
            "currency" => $currency,
            "prices" => $prices,
            "max_tip_amount" => $max_tip_amount,
            "suggested_tip_amounts" => $suggested_tip_amounts,
            "start_parameter" => $start_parameter,
            "provider_data" => $provider_data,
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
            "disable_notification" => $disable_notification,
            "protect_content" => $protect_content,
            "message_effect_id" => $message_effect_id,
            "reply_parameters" => $reply_parameters,
            "reply_markup" => $reply_markup,
        ]);
    }

    /**
     * Получает обновления для бота.
     *
     * @param int|null $offset Идентификатор первого обновления, которое будет возвращено (необязательно).
     * @param int|null $limit Ограничение на количество возвращаемых обновлений (необязательно).
     * @param int|null $timeout Тайм-аут в секундах для долгого опроса (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getUpdates($offset = null, $limit = null, $timeout = null)
    {
        return $this->method('getUpdates', [
            'offset' => $offset,
            'limit' => $limit,
            'timeout' => $timeout,
        ]);
    }

    /**
     * Получает информацию о вебхуке.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getWebhookInfo()
    {
        return $this->method('getWebhookInfo');
    }

    /**
     * Отправляет анимацию в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $animation Путь к анимации или URL анимации.
     * @param string|null $caption Описание анимации (необязательно).
     * @param int|null $duration Продолжительность анимации в секундах (необязательно).
     * @param int|null $width Ширина анимации в пикселях (необязательно).
     * @param int|null $height Высота анимации в пикселях (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function sendAnimation($chat_id, $animation, $caption = null, $duration = null, $width = null, $height = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendAnimation', [
            'chat_id' => $chat_id,
            'animation' => $animation,
            'caption' => $caption,
            'duration' => $duration,
            'width' => $width,
            'height' => $height,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет информацию о месте в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param float $latitude Широта места.
     * @param float $longitude Долгота места.
     * @param string $title Название места.
     * @param string $address Адрес места.
     * @param string|null $foursquare_id Идентификатор Foursquare (необязательно).
     * @param string|null $foursquare_type Тип места в Foursquare (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function sendVenue($chat_id, $latitude, $longitude, $title, $address, $foursquare_id = null, $foursquare_type = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendVenue', [
            'chat_id' => $chat_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'title' => $title,
            'address' => $address,
            'foursquare_id' => $foursquare_id,
            'foursquare_type' => $foursquare_type,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет контакт в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $phone_number Номер телефона контакта.
     * @param string $first_name Имя контакта.
     * @param string|null $last_name Фамилия контакта (необязательно).
     * @param string|null $vcard VCard контакта (необязательно).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function sendContact($chat_id, $phone_number, $first_name, $last_name = null, $vcard = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendContact', [
            'chat_id' => $chat_id,
            'phone_number' => $phone_number,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'vcard' => $vcard,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет опрос в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $question Вопрос опроса.
     * @param array $options Варианты ответов.
     * @param bool $is_anonymous Анонимный опрос (по умолчанию true).
     * @param string $type Тип опроса (по умолчанию "regular").
     * @param bool $allows_multiple_answers Разрешить несколько ответов (по умолчанию false).
     * @param int|null $correct_option_id Идентификатор правильного ответа (для викторин).
     * @param string|null $explanation Объяснение правильного ответа (для викторин).
     * @param int|null $open_period Период времени в секундах, в течение которого опрос будет активен.
     * @param int|null $close_date Дата и время закрытия опроса в формате Unix.
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function sendPoll($chat_id, $question, $options, $is_anonymous = true, $type = 'regular', $allows_multiple_answers = false, $correct_option_id = null, $explanation = null, $open_period = null, $close_date = null, $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendPoll', [
            'chat_id' => $chat_id,
            'question' => $question,
            'options' => json_encode($options),
            'is_anonymous' => $is_anonymous,
            'type' => $type,
            'allows_multiple_answers' => $allows_multiple_answers,
            'correct_option_id' => $correct_option_id,
            'explanation' => $explanation,
            'open_period' => $open_period,
            'close_date' => $close_date,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
        ]);
    }

    /**
     * Отправляет кубик в чат.
     *
     * @param int $chat_id Идентификатор чата.
     * @param string $emoji Эмодзи кубика (по умолчанию 🎲).
     * @param bool $disable_notification Отключить уведомления о сообщении (по умолчанию false).
     * @param int|null $reply_to_message_id Идентификатор сообщения, на которое нужно ответить (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function sendDice($chat_id, $emoji = '🎲', $disable_notification = false, $reply_to_message_id = null)
    {
        return $this->method('sendDice', [
            'chat_id' => $chat_id,
            'emoji' => $emoji,
            'disable_notification' => $disable_notification,
            'reply_to_message_id' => $reply_to_message_id,
        ]);
    }

    /**
     * Получает информацию о чате.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getChat($chat_id)
    {
        return $this->method('getChat', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Получает список администраторов чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getChatAdministrators($chat_id)
    {
        return $this->method('getChatAdministrators', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Получает информацию о члене чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getChatMember($chat_id, $user_id)
    {
        return $this->method('getChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Получает количество участников чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getChatMembersCount($chat_id)
    {
        return $this->method('getChatMembersCount', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Покидает чат.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function leaveChat($chat_id)
    {
        return $this->method('leaveChat', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Закрепляет сообщение в чате.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $message_id Идентификатор сообщения.
     * @param bool $disable_notification Отключить уведомления о закреплении (по умолчанию false).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function pinChatMessage($chat_id, $message_id, $disable_notification = false)
    {
        return $this->method('pinChatMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'disable_notification' => $disable_notification,
        ]);
    }

    /**
     * Открепляет сообщение в чате.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $message_id Идентификатор сообщения (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function unpinChatMessage($chat_id, $message_id = null)
    {
        return $this->method('unpinChatMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ]);
    }

    /**
     * Открепляет все сообщения в чате.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function unpinAllChatMessages($chat_id)
    {
        return $this->method('unpinAllChatMessages', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Редактирует подпись сообщения.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $message_id Идентификатор сообщения.
     * @param string $caption Новая подпись сообщения.
     * @param string|null $parse_mode Режим парсинга (Markdown или HTML).
     * @param array|null $reply_markup Новая разметка клавиатуры (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function editMessageCaption($chat_id, $message_id, $caption, $parse_mode = null, $reply_markup = null)
    {
        return $this->method('editMessageCaption', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'caption' => $caption,
            'parse_mode' => $parse_mode,
            'reply_markup' => $reply_markup,
        ]);
    }

    /**
     * Редактирует медиа-сообщение.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $message_id Идентификатор сообщения.
     * @param array $media Новое медиа-содержимое.
     * @param array|null $reply_markup Новая разметка клавиатуры (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function editMessageMedia($chat_id, $message_id, $media, $reply_markup = null)
    {
        return $this->method('editMessageMedia', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'media' => $media,
            'reply_markup' => $reply_markup,
        ]);
    }

    /**
     * Редактирует разметку клавиатуры сообщения.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $message_id Идентификатор сообщения.
     * @param array|null $reply_markup Новая разметка клавиатуры (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function editMessageReplyMarkup($chat_id, $message_id, $reply_markup = null)
    {
        return $this->method('editMessageReplyMarkup', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => $reply_markup,
        ]);
    }

    /**
     * Останавливает опрос.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $message_id Идентификатор сообщения с опросом.
     * @param array|null $reply_markup Новая разметка клавиатуры (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function stopPoll($chat_id, $message_id, $reply_markup = null)
    {
        return $this->method('stopPoll', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => $reply_markup,
        ]);
    }

    /**
     * Удаляет фотографию чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function deleteChatPhoto($chat_id)
    {
        return $this->method('deleteChatPhoto', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Устанавливает название чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param string $title Новое название чата.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setChatTitle($chat_id, $title)
    {
        return $this->method('setChatTitle', [
            'chat_id' => $chat_id,
            'title' => $title,
        ]);
    }

    /**
     * Устанавливает описание чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param string $description Новое описание чата.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setChatDescription($chat_id, $description)
    {
        return $this->method('setChatDescription', [
            'chat_id' => $chat_id,
            'description' => $description,
        ]);
    }

    /**
     * Устанавливает фотографию чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param string $photo Путь к фотографии или URL изображения.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setChatPhoto($chat_id, $photo)
    {
        return $this->method('setChatPhoto', [
            'chat_id' => $chat_id,
            'photo' => $photo,
        ]);
    }

    /**
     * Устанавливает разрешения чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param array $permissions Новые разрешения чата.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setChatPermissions($chat_id, $permissions)
    {
        return $this->method('setChatPermissions', [
            'chat_id' => $chat_id,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Экспортирует ссылку на приглашение в чат.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function exportChatInviteLink($chat_id)
    {
        return $this->method('exportChatInviteLink', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Создает ссылку на приглашение в чат.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param string|null $name Название ссылки на приглашение (необязательно).
     * @param int|null $expire_date Дата истечения срока действия ссылки в формате Unix (необязательно).
     * @param int|null $member_limit Максимальное количество участников, которые могут присоединиться по этой ссылке (необязательно).
     * @param bool $creates_join_request Требуется ли одобрение запроса на присоединение (по умолчанию false).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function createChatInviteLink($chat_id, $name = null, $expire_date = null, $member_limit = null, $creates_join_request = false)
    {
        return $this->method('createChatInviteLink', [
            'chat_id' => $chat_id,
            'name' => $name,
            'expire_date' => $expire_date,
            'member_limit' => $member_limit,
            'creates_join_request' => $creates_join_request,
        ]);
    }

    /**
     * Редактирует ссылку на приглашение в чат.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param string $invite_link Ссылка на приглашение, которую нужно отредактировать.
     * @param string|null $name Новое название ссылки на приглашение (необязательно).
     * @param int|null $expire_date Новая дата истечения срока действия ссылки в формате Unix (необязательно).
     * @param int|null $member_limit Новое максимальное количество участников, которые могут присоединиться по этой ссылке (необязательно).
     * @param bool $creates_join_request Требуется ли одобрение запроса на присоединение (по умолчанию false).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function editChatInviteLink($chat_id, $invite_link, $name = null, $expire_date = null, $member_limit = null, $creates_join_request = false)
    {
        return $this->method('editChatInviteLink', [
            'chat_id' => $chat_id,
            'invite_link' => $invite_link,
            'name' => $name,
            'expire_date' => $expire_date,
            'member_limit' => $member_limit,
            'creates_join_request' => $creates_join_request,
        ]);
    }

    /**
     * Отзывает ссылку на приглашение в чат.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param string $invite_link Ссылка на приглашение, которую нужно отозвать.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function revokeChatInviteLink($chat_id, $invite_link)
    {
        return $this->method('revokeChatInviteLink', [
            'chat_id' => $chat_id,
            'invite_link' => $invite_link,
        ]);
    }

    /**
     * Одобряет запрос на присоединение к чату.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя, запрос которого нужно одобрить.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function approveChatJoinRequest($chat_id, $user_id)
    {
        return $this->method('approveChatJoinRequest', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Отклоняет запрос на присоединение к чату.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя, запрос которого нужно отклонить.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function declineChatJoinRequest($chat_id, $user_id)
    {
        return $this->method('declineChatJoinRequest', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Банит участника чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя.
     * @param int|null $until_date Дата и время окончания бана в формате Unix (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function banChatMember($chat_id, $user_id, $until_date = null)
    {
        return $this->method('banChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'until_date' => $until_date,
        ]);
    }

    /**
     * Разбанивает участника чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function unbanChatMember($chat_id, $user_id)
    {
        return $this->method('unbanChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
        ]);
    }

    /**
     * Ограничивает права участника чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя.
     * @param array $permissions Новые разрешения участника.
     * @param int|null $until_date Дата и время окончания ограничений в формате Unix (необязательно).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function restrictChatMember($chat_id, $user_id, $permissions, $until_date = null)
    {
        return $this->method('restrictChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'permissions' => $permissions,
            'until_date' => $until_date,
        ]);
    }

    /**
     * Повышает участника чата до администратора.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя.
     * @param array $permissions Новые разрешения администратора.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function promoteChatMember($chat_id, $user_id, $permissions)
    {
        return $this->method('promoteChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Устанавливает пользовательский заголовок администратора чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $user_id Идентификатор пользователя.
     * @param string $custom_title Новый пользовательский заголовок.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setChatAdministratorCustomTitle($chat_id, $user_id, $custom_title)
    {
        return $this->method('setChatAdministratorCustomTitle', [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'custom_title' => $custom_title,
        ]);
    }

    /**
     * Банит отправителя сообщений в чате.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $sender_chat_id Идентификатор отправителя сообщений.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function banChatSenderChat($chat_id, $sender_chat_id)
    {
        return $this->method('banChatSenderChat', [
            'chat_id' => $chat_id,
            'sender_chat_id' => $sender_chat_id,
        ]);
    }

    /**
     * Разбанивает отправителя сообщений в чате.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param int $sender_chat_id Идентификатор отправителя сообщений.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function unbanChatSenderChat($chat_id, $sender_chat_id)
    {
        return $this->method('unbanChatSenderChat', [
            'chat_id' => $chat_id,
            'sender_chat_id' => $sender_chat_id,
        ]);
    }

    /**
     * Устанавливает команды бота.
     *
     * @param array $commands Массив команд.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setMyCommands($commands)
    {
        return $this->method('setMyCommands', [
            'commands' => json_encode($commands),
        ]);
    }

    /**
     * Удаляет команды бота.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function deleteMyCommands()
    {
        return $this->method('deleteMyCommands');
    }

    /**
     * Получает команды бота.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getMyCommands()
    {
        return $this->method('getMyCommands');
    }

    /**
     * Устанавливает кнопку меню чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     * @param array $menu_button Кнопка меню чата.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setChatMenuButton($chat_id, $menu_button)
    {
        return $this->method('setChatMenuButton', [
            'chat_id' => $chat_id,
            'menu_button' => $menu_button,
        ]);
    }

    /**
     * Получает кнопку меню чата.
     *
     * @param int|string $chat_id Идентификатор чата или имя пользователя.
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getChatMenuButton($chat_id)
    {
        return $this->method('getChatMenuButton', [
            'chat_id' => $chat_id,
        ]);
    }

    /**
     * Устанавливает права администратора по умолчанию.
     *
     * @param array $rights Права администратора.
     * @param bool $for_channels Устанавливать права для каналов (по умолчанию false).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function setMyDefaultAdministratorRights($rights, $for_channels = false)
    {
        return $this->method('setMyDefaultAdministratorRights', [
            'rights' => $rights,
            'for_channels' => $for_channels,
        ]);
    }

    /**
     * Получает права администратора по умолчанию.
     *
     * @param bool $for_channels Получать права для каналов (по умолчанию false).
     *
     * @return \Illuminate\Http\Client\Response|null Ответ от Telegram API.
     */
    public function getMyDefaultAdministratorRights($for_channels = false)
    {
        return $this->method('getMyDefaultAdministratorRights', [
            'for_channels' => $for_channels,
        ]);
    }
}
