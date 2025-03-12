<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

class CallbackqueryCommand extends SystemCommand
{
    protected $name = 'callbackquery';
    protected $description = 'Reply to callback query';
    protected $usage = '/callbackquery';

    public function execute(): \Longman\TelegramBot\Entities\ServerResponse
    {
        $callback_query = $this->getCallbackQuery();
        $userState = new UserState();
        $chat_id = $callback_query->getMessage()->getChat()->getId(); // Получаем chat_id пользователя
        $data = $callback_query->getData(); // Получаем данные из callback_data
        $responseMessage = '';
        switch ($data) {
            case 'today':
                $responseMessage = 'Вы выбрали погоду на 1 день. Пожалуйста, отправьте название города.';
                break;
            case 'week':
                $responseMessage = 'Вы выбрали погоду на неделю. Пожалуйста, отправьте название города.';
                break;
            default:
                $responseMessage = 'Что-то пошло не так!';
                break;
        }
        // Отправляем сообщение с инструкцией для ввода города
        return Request::sendMessage([
            'chat_id' => $chat_id,
            'text'    => $responseMessage,
        ]);
    }
}
