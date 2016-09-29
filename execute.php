<?php

require 'vendor/autoload.php';

foreach(glob("vendor/recsysbot/replies/*.php") as $file){
    require $file;
}
//require 'vendor/recsysbot/replies/menuReply.php';
//require 'vendor/recsysbot/replies/fullMenuReply.php';


use GuzzleHttp\Client;
use Telegram\Bot\Api;
$telegram = new Api('297809022:AAHaM0c6-mE2PvrFlEnV7JeHnKXor7JCSgM');



// recupero il contenuto inviato da Telegram
$content = $telegram->getWebhookUpdates();

// converto il contenuto da JSON ad array PHP
//$content = file_get_contents("php://input");
$update = json_decode($content, true);

// se la richiesta è null interrompo lo script
if(!$update)
{
  exit;
}

$telegram->addCommand(Vendor\Recsysbot\Commands\HelpCommand::class);
$telegram->addCommand(Vendor\Recsysbot\Commands\InfoCommand::class);
$telegram->addCommand(Vendor\Recsysbot\Commands\ProfileCommand::class);
$telegram->addCommand(Vendor\Recsysbot\Commands\StartCommand::class);




// assegno alle seguenti variabili il contenuto ricevuto da Telegram
$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

// pulisco il messaggio ricevuto togliendo eventuali spazi prima e dopo il testo
$text = trim($text);
// converto tutti i caratteri alfanumerici del messaggio in minuscolo
$text = strtolower($text);

switch ($text) {
   case "/start": case "/help": case "/info":            
      $telegram->commandsHandler(true);
      break;
   case "/runtime": case "runtime":
      runtimeReply($telegram, $chatId);
      break;
   case "/writers": case "writers": case "writer":
      $propertyType = "writer";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/producers": case "producers": case "producer":
      $propertyType = "producer";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/release date": case "release date": case "releaseDate":
      $propertyType = "releaseDate";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/music composers": case "music composers": case "music composer":
      $propertyType = "musicComposer";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/cinematographies": case "cinematographies": case "cinematography":
      $propertyType = "cinematography";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/based on": case "based on": case "basedOn":
      $propertyType = "basedOn";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/editings": case "editings": case "editing":
      $propertyType = "editing";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/distributors": case "distributors": case "distributor":
      $propertyType = "distributor";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case strpos($msg, '📽'):
      $propertyType = "director";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case "/no": case "no":            
      noReply($telegram, $chatId);
      break;
   case "menu": case "/yes": case "yes": case "<-":         
      menuReply($telegram, $chatId);
      break;
   case "->":   
      fullMenuReply($telegram, $chatId);
      break;
   case "/directors": case "directors": case "director":            
      $propertyType = "director";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/starring": case "starring":
      $propertyType = "starring";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/categories": case "categories": case "category":
      $propertyType = "category";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case "/genres": case "genres": case "genre":
      $propertyType = "genre";
      propertyReply($telegram, $chatId, $propertyType);
      break;
   case strpos($msg, '🕴'):
      $propertyType = "starring";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '🗒'):
      $propertyType = "category";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '🎬'):
      $propertyType = "genre";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '⏳'):
      $propertyType = "runtime";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );         
      break;
   case strpos($msg, '✍'):
      $propertyType = "writer";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '💰'):
      $propertyType = "producer";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '🗓'):
      $propertyType = "releaseDate";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '🎼'):
      $propertyType = "musicComposer";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '📷'):
      $propertyType = "cinematography";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '📔'):
      $propertyType = "basedOn";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '💼'):
      $propertyType = "editing";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
   case strpos($msg, '🏢'):🏢���:
      $propertyType = "distributor";
      getFilmsToReply($telegram, $chatId, $propertyType, $msg );
      break;
 case strpos($msg, '👍'):
      likeReply($telegram, $chatId);
      menuReply($telegram, $chatId);
      break;
   case strpos($msg, '👎'):
      dislikeReply($telegram, $chatId);
      menuReply($telegram, $chatId);
      break;
   case strpos($msg, '⏭'):
      skipReply($telegram, $chatId);
      menuReply($telegram, $chatId);
      break;
   case ($msg[0] != "/"):
      //$telegram->sendMessage(['chat_id' => $chatId, 'text' => $textSorry]);
      getFilmExplanation($telegram, $chatId, $msg);
      break;
   default:
      $telegram->commandsHandler(true);
      break;
}
