<?php

require 'vendor/autoload.php';




use Telegram\Bot\Api;
$telegram = new Api('297809022:AAHaM0c6-mE2PvrFlEnV7JeHnKXor7JCSgM');


$content = $telegram->getWebhookUpdates();

//$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$text = trim($text);
$text = strtolower($text);

$telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]);
