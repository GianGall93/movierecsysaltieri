<?php

function basePropertyTypeReply($telegram, $chatId){
	
	$emojis = require '/app/recsysbot/variables/emojis.php';

   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);
   
   $fullMenuArray = propertyTypeKeyboard($chatId);

   $keyboard = array();
   foreach ($fullMenuArray as $key => $property) {
       $result[] = array($property);
   }

      $numberRatedMovies = getNumberRatedMovies($chatId);
      $numberRatedProperties = getNumberRatedProperties($chatId);
      $needNumberOfRatedProperties = 3 - ($numberRatedProperties + $numberRatedMovies);



   if ($needNumberOfRatedProperties <= 0) {
      if ($needNumberOfRatedProperties == 0){
         $text = "I am now able to recommend you some movies " . $emojis['smile'];
         $text .= "\nTap on \"".$emojis['globe']."Recommend Movies\" button, otherwise you can enrich your profile by providing further ratings ".$emojis['wink'];
      }
      elseif ($needNumberOfRatedProperties < 0){
         $text = "Let me recommend a movie  " . $emojis['smile'];
         $text .= "\nTap on \"".$emojis['globe']."Recommend Movies\" button, otherwise you can enrich your profile by providing further ratings ".$emojis['wink'];
      }

      $keyboard = [
                     [$emojis['globe']." Recommend Movies"],
                     [$result[0][0], $result[1][0]],
                     [$result[2][0], $result[3][0]],
                     ['🔵 Movies','⚙️ Profile', 'More 👉']
                  ];
   }
   else{
      $text = "Please, choose among the most popular properties";

      $keyboard = [
                     [$result[0][0], $result[1][0]],
                     [$result[2][0], $result[3][0]],
                     ['🔵 Movies','⚙️ Profile', 'More 👉']
                  ];
   }

   $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
   
   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
   $telegram->sendMessage(['chat_id' => $chatId, 
                           'text' => $text,
                           'reply_markup' => $reply_markup]);
}
