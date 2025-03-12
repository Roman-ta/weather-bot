<?php
namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

class StartCommand extends UserCommand
{
    protected $name = 'start';
    protected $description = 'Приветственное сообщение';
    protected $usage = '/start';

    public function execute(): \Longman\TelegramBot\Entities\ServerResponse
    {

        $chat_id = $this->getMessage()->getChat()->getId();
        $messageId = $this->getMessage()->getMessageId();
//      "reply_to_message_id" => $messageId  - ответ на конкретное сообщение
        $text = "Привет! Я бот, который может помочь тебе узнать погоду в любом городе. Ты хочешь узнать погоду на день или на неделю?";

        $inline_keyboard = [[
                ['text' => 'на 1 день', 'callback_data' => 'today'],
                ['text' => 'на неделю', 'callback_data' => 'week']
            ],
        ];
        $keyboard = [
            'inline_keyboard' => $inline_keyboard,
        ];

        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => json_encode($keyboard)
        ]);

    }
}
