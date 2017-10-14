<?php

/**
 * @author Francesco Baccaro
 */
use GuzzleHttp\Client;
use Telegram\Bot\Api;

require 'vendor/autoload.php';
$config = require __DIR__ . '/recsysbot/config/movierecsysbot-config.php';

//richiedo tutti le funzioni, i messaggi di risposta e i servizi che mi servono per l'esecuzione
foreach(glob("recsysbot/classes/*.php") as $file){require $file;}
foreach(glob("recsysbot/functions/*.php") as $file){require $file;}
foreach(glob("recsysbot/keyboards/*.php") as $file){require $file;}
foreach(glob("recsysbot/messages/*.php") as $file){require $file;}
foreach(glob("recsysbot/replies/*.php") as $file){require $file;}
foreach(glob("recsysbot/restService/*.php") as $file){require $file;}

//This is suggested from Guzzle
date_default_timezone_set($config['timezone']);
$token = $config['token'];

$telegram = new Api($token);

// recupero il contenuto inviato da Telegram
//$content = file_get_contents("php://input");
$content = $telegram->getWebhookUpdates();

// converto il contenuto da JSON ad array PHP
$update = json_decode($content, true);

// se la richiesta è null interrompo lo script
if(!$update)
{
  exit;
}

$telegram->addCommand(Recsysbot\Commands\HelpCommand::class);
$telegram->addCommand(Recsysbot\Commands\InfoCommand::class);
$telegram->addCommand(Recsysbot\Commands\ResetCommand::class);
$telegram->addCommand(Recsysbot\Commands\StartCommand::class);

// assegno alle seguenti variabili il contenuto ricevuto da Telegram
$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";
$globalDate = gmdate("Y-m-d\TH:i:s\Z", $date);

// gestisci edited_message, per evitare blocco del bot
if($chatId == "")
{
   $message = isset($update['edited_message']) ? $update['edited_message'] : "";
   $messageId = isset($message['message_id']) ? $message['message_id'] : "";
   $chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
   $firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
   $lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
   $username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
   $date = isset($message['date']) ? $message['date'] : "";
   $text = isset($message['text']) ? $message['text'] : "";
   $globalDate = gmdate("Y-m-d\TH:i:s\Z", $date);
   file_put_contents("php://stderr", "edited_message execute.php - chatId: ".$chatId." - update: ".print_r($update, true).PHP_EOL);
}
$botName = checkUserAndBotNameFunction($chatId, $firstname, $lastname, $username, $date);

// Stampa nel log
file_put_contents("php://stderr","chatId:".$chatId." - firstname:".$firstname." - botName".$botName. " - Date:".$globalDate." - text:".$text.PHP_EOL);

// pulisco il messaggio ricevuto togliendo eventuali spazi prima e dopo il testo
$text = trim($text);

// converto tutti i caratteri alfanumerici del messaggio in minuscolo
//$text = strtolower($text);
try {
      //gestisco il tipo di messaggio: testo
      if (isset ($message['text'])){
         
         if (($text == "/start")) {
            putUserDetail($chatId, $firstname, $lastname, $username);
            messageDispatcher($telegram, $chatId, $messageId, $date, $text, $firstname, $botName);
         }
         else{
            messageDispatcher($telegram, $chatId, $messageId, $date, $text, $firstname, $botName);
         }

      }

      elseif (isset ($message['audio'])){
         $response = "I'm sorry. I received an audio message";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['document'])){
         $response = "I'm sorry. I received a message document, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['photo'])){
         $response = "I'm sorry. I received a message photo, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['sticker'])){
         $response = "I'm sorry. I received a sticker message, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['video'])){
         $response = "I'm sorry. I received a video message, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['voice'])){
         $response = "I'm sorry. I received a voice message, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['contact'])){
         $response = "I'm sorry. I received a message contact, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['location'])){
         $response = "I'm sorry. I received a location message, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      elseif (isset ($message['venue'])){
         $response = "I'm sorry. I received a venue, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
      else{
         $response = "I'm sorry. I received a message, but i can't unswer";
         $telegram->sendMessage(['chat_id' => $chatId, 'text' => $response]);
      }
   } catch (Exception $e) {
      file_put_contents("php://stderr","Exception chatId:".$chatId." - firstname:".$firstname." - botName".$botName. " - Date:".$globalDate." - text:".$text.PHP_EOL);
      file_put_contents("php://stderr","Exception chatId:".$chatId." Caught exception: ".print_r($e->getTraceAsString(), true).PHP_EOL);
}
