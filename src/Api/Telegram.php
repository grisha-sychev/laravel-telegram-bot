<?php

namespace Tgb\Api;

use Tgb\Services\Appendix;

use Tgb\Data\CallbackQuery;
use Tgb\Data\MessageQuery;
use Tgb\Data\MyChatMemberQuery;
use Tgb\Data\PreCheckoutQuery;
use Tgb\Data\ChannelPostQuery;

use Illuminate\Support\Facades\Request;

/**
 * Класс Api Client для управления Telegram ботом.
 */
class Telegram
{
    use Methods;

    /**
     * @var \Illuminate\Http\Client\Response|null $response HTTP-ответ от API Telegram.
     */
    private ?\Illuminate\Http\Client\Response $response;

    // /**
    //  * @var CallbackQuery|MessageQuery|PreCheckoutQuery|null $user Информация о пользователе, которая может быть либо CallbackQuery, либо MessageQuery, или null, если не установлена.
    //  */
    private $user = null;

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
     * @var string|null $hostname host, связанный с ботом.
     */
    public ?string $hostname = null;

    /**
     * Конструктор класса Client.
     *
     * @param string|null $bot Имя бота (необязательный параметр).
     */
    public function __construct()
    {
        $this->bot = $bot ?? $this->bot;
        $this->hostname = $hostname ?? $this->hostname;
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
     * Получает все данные запроса от Telegram и возвращает их в виде массива.
     *
     * Данные запроса от Telegram в виде обьекта.
     */
    public function request()
    {
        $all = Request::json()->all();

        switch (true) {
            case isset($all['callback_query']):
                return new CallbackQuery($all);
            case isset($all['pre_checkout_query']):
                return new PreCheckoutQuery($all);
            case isset($all['message']):
                return new MessageQuery($all);
            case isset($all['channel_post']):
                return new ChannelPostQuery($all);
            default:
                return new \stdClass();
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
     * Получает данные о пользователе из запроса.
     *
     * @return CallbackQuery|MessageQuery|PreCheckoutQuery|MyChatMemberQuery
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
        $user = $this->getUser();

        if (method_exists($user, 'getFromId')) {
            return $user->getFromId();
        }

        return;
    }

    /**
     * Проверяет, является ли пользователь ботом.
     *
     * @return bool
     */
    public function isUserBot()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getFromIsBot')) {
            return $user->getFromIsBot();
        }
        return false;
    }

    /**
     * Получает логин пользователя.
     *
     * @return string|null
     */
    public function getUsername()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getFromUsername')) {
            return $user->getFromUsername();
        }
        return;
    }

    /**
     * Получает имя пользователя.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getFromFirstName')) {
            return $user->getFromFirstName();
        }
        return;
    }

    /**
     * Получает фамилию пользователя.
     *
     * @return string|null
     */
    public function getLastName()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getFromLastName')) {
            return $user->getFromLastName();
        }
        return;
    }

    /**
     * Получает тип чата пользователя.
     *
     * @return string|null
     */
    public function getChatType()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getChatType')) {
            return $user->getChatType();
        }
        return;
    }

    /**
     * Получает file_id аватара пользователя.
     * TODO: Надо создать обьект photos и переделать это недоразумение
     * @return string|null
     */
    public function getUserAvatarFileId()
    {
        $userProfilePhotos = json_decode($this->getUserProfilePhotos($this->getUserId()), true);

        if (isset($userProfilePhotos['result']['photos']) && !empty($userProfilePhotos['result']['photos'])) {
            return $userProfilePhotos['result']['photos'][0][0]['file_id'];
        } else {
            return;
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
        return;
    }

    /**
     * Получает id сообщения.
     *
     * @return string|null
     */
    public function getMessageId()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getMessageId')) {
            return $user->getMessageId();
        }
        return;
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
        return;
    }

    /**
     * Получает дату сообщения.
     *
     * @return int|null
     */
    public function getMessageDate()
    {
        $user = $this->getUser();

        if (method_exists($user, 'getMessageDate')) {
            return $user->getMessageDate();
        }

        return;
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

        return;
    }

    /**
     * Получает данные о чате.
     *
     * @return object|null
     */
    public function getChatData()
    {
        $user = $this->getUser();

        if ($user && method_exists($user, 'getChatId') && method_exists($user, 'getChatFirstName') && method_exists($user, 'getChatUsername') && method_exists($user, 'getChatLastName')) {
            return (object) [
                'chat_id' => $user->getChatId(),
                'first_name' => $user->getChatFirstName(),
                'last_name' => $user->getChatLastName(),
                'username' => $user->getChatUsername(),
            ];
        }

        return;
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
        return;
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
                'callback_text' => $this->request()->getInlineKeyboard()[0][0]['text'],
                'callback_data' => $user->getCallbackData(),
            ];
        }

        return;
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
}
