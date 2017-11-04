<?php

function startProfileAcquisitioReply($telegram, $chatId){

	$emojis = require '/app/recsysbot/variables/emojis.php';
	
   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);

   $numberRatedMovies = getNumberRatedMovies($chatId);
   $numberRatedProperties = getNumberRatedProperties($chatId);
   $needNumberOfRatedProperties = 3 - ($numberRatedProperties + $numberRatedMovies);

   if ($needNumberOfRatedProperties <= 0){
      $keyboard = userPropertyValueKeyboard();
      $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]); 

      $text = "Let me recommend a movie  😃";
      $text .= "\nTap on \"".$emojis['globe']." Recommend Movies\" button, otherwise you can enrich your profile by providing further ratings ".$emojis['smile']."";
   	$telegram->sendMessage(['chat_id' => $chatId, 'text' => $text, 'reply_markup' => $reply_markup]);       
   }
   else{
      $keyboard = startProfileAcquisitionKeyboard();
      $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]); 

   	$text = "I need at least 3 preferences for generating recommendations.";
   	$telegram->sendMessage(['chat_id' => $chatId, 'text' => $text, 'reply_markup' => $reply_markup]); 

   	$text = "Let me to recommend a movie.\nPlease, tell me something about you \nor type your preference ".$emojis['smilesimple']."";  
   	$telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]);    
   }
   
    	
                           
}
