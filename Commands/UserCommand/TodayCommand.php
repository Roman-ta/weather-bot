<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TodayCommand extends UserCommand
{
    protected $name = 'today';
    protected $description = 'Погода на сегодня';
    protected $usage = '/today [город]';

    public function execute(): \Longman\TelegramBot\Entities\ServerResponse
    {
        $chat_id = $this->getMessage()->getChat()->getId();
        $messageFull = trim($this->getMessage()->getText(true));
        $messageText = explode(" ", $messageFull);

        // Проверка: введен ли город
        if (!isset($messageText[0]) || empty($messageText[0])) {
            return Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => "Пожалуйста, укажите город. Например: /today Москва",
            ]);
        }

        $city = $messageText[0];
        $client = new Client();

        try {
            $res = $client->request('GET', "https://api.openweathermap.org/data/2.5/weather", [
                'query' => [
                    'q'     => $city,
                    'appid' => WEATHER_TOKEN,  // Замените на свой реальный API-ключ
                    'units' => 'metric',
                    'lang'  => 'ru'
                ]
            ]);

            $r = json_decode($res->getBody(), true);

            // Проверка на ошибки в ответе API
            if (isset($r['cod']) && $r['cod'] != 200) {
                return Request::sendMessage([
                    'chat_id' => $chat_id,
                    'text'    => "❌ Ошибка: возможно, город *{$city}* не найден.",
                    'parse_mode' => 'Markdown',
                ]);
            }

            // Получаем данные о погоде
            $icon = $r['weather'][0]['icon'] ?? '01d';
            $icons = "https://openweathermap.org/img/wn/{$icon}@4x.png";
            $temperature = round($r['main']['temp'] ?? 0, 1);
            $temperatureFeels = round($r['main']['feels_like'] ?? 0, 0);
            $windSpeed = $r['wind']['speed'] ?? 0;

            $responseMessage = "🌡 Температура в *{$city}*:  *{$temperature}°C* ({$r['weather'][0]['description']})\n";
            $responseMessage .= "😌 Ощущается как: *{$temperatureFeels}°C*\n";
            $responseMessage .= "💨 Скорость ветра: *{$windSpeed} м/с*";

            return Request::sendPhoto([
                'chat_id'  => $chat_id,
                'photo'    => $icons,
                'caption'  => $responseMessage,
                'parse_mode' => 'Markdown', // Для красивого форматирования
            ]);
        } catch (GuzzleException $e) {
            return Request::sendMessage([
                'chat_id' => $chat_id,
                'text'    => "❌ Ошибка при получении данных о погоде.",
                'parse_mode' => 'Markdown',
            ]);
        }
    }
}
