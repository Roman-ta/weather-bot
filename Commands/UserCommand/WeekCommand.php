<?php
namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
class WeekCommand extends UserCommand
{
    protected $name = 'week';
    protected $description = 'Погода на неделю';
    protected $usage = '/week';

    public function execute(): \Longman\TelegramBot\Entities\ServerResponse
    {
        $chat_id = $this->getMessage()->getChat()->getId();
        $messageFull = $this->getMessage()->getText();
        $client = new Client();
        $messageText = explode(" ", $messageFull);
        try {
            $res = $client->request('GET', "https://api.openweathermap.org/data/2.5/forecast", [
                'query' => [
                    'q' => $messageText[1],
                    'appid' => WHETHER_TOKEN,
                    'units' => 'metric',
                    'lang' => 'ru'
                ]
            ]);
            $r = json_decode($res->getBody(), true);
            echo '<pre>';
            print_r($r);
            echo '</pre>';
            exit();
            $icon = $r['weather'][0]['icon'];
            $icons = "https://openweathermap.org/img/wn/{$icon}@4x.png";
            $temperature = round($r['main']['temp'], 1);
            $temperatureFeels = round($r['main']['feels_like'], 0);
            $responseMessage = "Температура в {$messageText[1]}:  " . $temperature . "°C " . $r['weather'][0]['description'] . "\n";
            $responseMessage .= "Ощущается как: " . $temperatureFeels . "°C ". "\n";
            $responseMessage .= "Скорость ветра: " . $r['wind']['speed'] . " м/с". "\n";
            return  Request::sendPhoto([
                'chat_id' => $chat_id,
                'photo' => $icons,
                'caption' => $responseMessage,
            ]);
        } catch (GuzzleException $e) {
            return Request::sendMessage([
                'chat_id' => $chat_id,
                'text' => "Ошибка: возможно, город '{$messageText[1]}' не найден.",
            ]);
        }
    }
}
