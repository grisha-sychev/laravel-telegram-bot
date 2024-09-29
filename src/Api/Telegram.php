<?php

namespace Tgb\Api;

use Tgb\Services\Appendix;

use Tgb\Data\CallbackQuery;
use Tgb\Data\MessageQuery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

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
     * @var CallbackQuery|MessageQuery|null $user Информация о пользователе, которая может быть либо CallbackQuery, либо MessageQuery, или null, если не установлена.
     */
    private CallbackQuery|MessageQuery|null $user = null;

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
        $this->response = Http::withoutVerifying()->get("https://api.telegram.org/bot" . $this->getToken() . "/" . $method . ($query ? '?' . http_build_query($query) : ''));
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

        if (isset($all['callback_query'])) {
            return new CallbackQuery($all);
        } else {
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
     * Определяет команду для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения или callback.
     *
     * @param string|array $command Команда, начинающаяся с символа "/" (например, "/start") или массив команд.
     * @param Closure $callback Функция-обработчик для выполнения, если команда или callback совпадают.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function command($command, $callback)
    {
        // Приводим команду к массиву, если это строка
        $commands = is_array($command) ? $command : [$command];

        // Преобразуем команды, добавляя "/" к каждой, если необходимо
        $commands = array_map(function ($cmd) {
            return "/" . ltrim($cmd, '/');
        }, $commands);

        // Привязываем callback к текущему объекту
        $callback = $callback->bindTo($this, $this);

        // Получаем текст сообщения и данные callback
        $messageText = $this->getMessageText();
        $cb = $this->getCallbackData();

        // Проверка для текста сообщения
        foreach ($commands as $cmd) {
            if ($cmd === Appendix::firstword($messageText)) {
                $arguments = $this->getArguments($messageText);
                $callback($arguments);
            }
        }

        // Проверка для callback данных
        if ($cb && is_object($cb)) {
            foreach ($commands as $cmd) {
                if ($cmd === "/" . $cb->callback_data) { // сравниваем с callback_data
                    $arguments = $this->getArguments($cb->callback_data);
                    $callback($arguments);
                }
            }
        }

        return null;
    }

    /**
     * Извлекает аргументы из текста команды.
     *
     * @param string $text Текст команды.
     *
     * @return array Массив аргументов.
     */
    private function getArguments($text)
    {
        $parts = explode(' ', $text);
        array_shift($parts); // Удаляем первую часть, так как это команда
        return $parts;
    }

    /**
     * Определяет callback для бота и выполняет соответствующий обработчик, если команда совпадает с текстом сообщения.
     *
     * @param string|array $pattern  Это строка или массив строк, по которым будет искать какой callback выполнить, например hello{id} или greet{name}.
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает с паттерном.
     * @param bool $deleteInlineButtons Удалять ли кнопки прошлого сообщения
     * 
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function callback($pattern, $callback, $deleteInlineButtons = false)
    {
        $cb = $this->getCallbackData();

        // Добавляем проверку на существование и тип переменной $cb
        if ($cb && is_object($cb)) {
            // Приводим паттерн к массиву, если это строка
            $patterns = is_array($pattern) ? $pattern : [$pattern];

            // Пробегаемся по каждому паттерну
            foreach ($patterns as $singlePattern) {
                // Преобразуем паттерн с параметрами в регулярное выражение
                $singlePattern = str_replace(['{', '}'], ['(?P<', '>[^}]+)'], $singlePattern);
                $singlePattern = "/^" . str_replace('/', '\/', $singlePattern) . "$/";

                if (preg_match($singlePattern, $cb->callback_data, $matches)) {
                    // Извлекаем только значения параметров из совпавших данных и передаем их в функцию-обработчик
                    $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                    // Вызываем answerCallbackQuery только если паттерн совпадает
                    $this->answerCallbackQuery($cb->callback_query_id);

                    if ($deleteInlineButtons) {
                        $this->editMessage($this->getUserId(), $this->getMessageId(), $this->getMessageText());
                    }

                    $callback(...$parameters);
                    exit; // Завершаем выполнение скрипта после выполнения callback
                }
            }
        }

        return null;
    }

    /**
     * Определяет сообщение для бота и выполняет соответствующий обработчик, если сообщение совпадает с паттерном.
     *
     * @param string|array $pattern Это строка или массив строк/регулярных выражений, по которым будет искать совпадение с сообщением.
     * @param Closure $callback Функция-обработчик для выполнения, если сообщение совпадает с паттерном.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function message($pattern, $callback)
    {
        $messageText = $this->getMessageText();

        // Приводим паттерн к массиву, если это строка
        $patterns = is_array($pattern) ? $pattern : [$pattern];

        // Пробегаемся по каждому паттерну
        foreach ($patterns as $singlePattern) {
            // Проверяем, является ли паттерн регулярным выражением
            $isRegex = preg_match('/^\/.*\/[a-z]*$/i', $singlePattern);

            // Если это не регулярное выражение, преобразуем паттерн с параметрами в регулярное выражение
            if (!$isRegex) {
                $singlePattern = str_replace(['{', '}'], ['(?P<', '>[^}]+)'], $singlePattern);
                $singlePattern = "/^" . str_replace('/', '\/', $singlePattern) . "$/";
            }

            if (preg_match($singlePattern, $messageText, $matches)) {
                // Извлекаем только значения параметров из совпавших данных и передаем их в функцию-обработчик
                $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Вызываем функцию-обработчик с параметрами
                $callback(...$parameters);
                exit; // Завершаем выполнение скрипта после выполнения callback
            }
        }

        return null;
    }

    /**
     * Определяет сообщение от пользователя и выполняет ошибку.
     *
     * @param mixed $message Любое сообщение кроме команды.
     * @param array|null $array Данные
     * @param Closure $callback Функция-обработчик для выполнения, если команда совпадает.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function error($message, $array, $callback)
    {
        $callback = $callback->bindTo($this);

        if ($array === null) {
            if ($message === $this->getMessageText()) {
                $callback();
            }
        } else {
            if ($this->findMatch($message, $array)) {
                $callback();
            }
        }
    }

    /**
     * Находит совпадение для заданного шаблона в предоставленном тексте.
     *
     * @param string $pattern Регулярное выражение для поиска.
     * @param string $text Текст, в котором будет производиться поиск.
     * @return array|null Возвращает массив совпадений, если они найдены, или null, если совпадений не найдено.
     */
    private function findMatch($data, $array)
    {
        foreach ($array as $value) {
            if (stripos($data, $value) !== false) {
                return false; // Найдено совпадение
            }
        }
        return true; // Совпадений не найдено
    }

    /**
     * Определяет действие для бота и выполняет соответствующий обработчик, если текст сообщения не начинается с "/".
     *
     * @param Closure $callback Функция-обработчик для выполнения, если текст сообщения не является командой.
     *
     * @return mixed Результат выполнения функции-обработчика.
     */
    public function anyMessage($callback)
    {
        if (mb_substr($this->getMessageText(), 0, 1) !== "/") {
            return $callback();
        }
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
        $host = $this->domain ?? $_SERVER['HTTP_HOST'];

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
     * @return CallbackQuery|MessageQuery
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
        return Appendix::issetKey($this->getUserProfilePhotos($this->getUserId()), 0);
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
