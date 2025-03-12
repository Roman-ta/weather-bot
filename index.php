<?php

use Longman\TelegramBot\Telegram;
use GuzzleHttp\Client;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\BotCommand;

require __DIR__ . "/vendor/autoload.php";
const BOT_NAME = "roman_ta_bot";


$dataBaseConfig = require __DIR__ . '/config/config.php';

$telegram = new Telegram(BOT_TOKEN, BOT_NAME);
$client = new Client();
$telegram->enableMySql($dataBaseConfig);
$telegram->addCommandsPath(__DIR__ . '/Commands/UserCommand');
$telegram->addCommandsPath(__DIR__ . '/Commands/SystemCommand');
$serverResponse = $telegram->handleGetUpdates();


$commands = [
    new BotCommand(['command' => 'help', 'description' => 'Помощь']),
];
Request::setMyCommands(['commands' => $commands]);


