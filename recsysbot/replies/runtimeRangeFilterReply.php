<?php

function runtimeRangeFilterReply($telegram, $chatId, $propertyType, $propertyValue, $addFilter){

   if ($propertyType !== "null") {
      if (strcasecmp($addFilter, "yes") == 0) {
         $data = putRuntimeRangeFilter($chatId, $propertyType, $propertyValue);
         $text = "You have added the filter \"".$propertyValue."\"";
      } 
      elseif (strcasecmp($addFilter, "no") == 0) {
         $data = putRuntimeRangeFilter($chatId, $propertyType, $propertyValue);
         $text = "No filter added for runtime range";
      }
      else{
         $text = "Problem with: No filter added for runtime range";
         file_put_contents("php://stderr", "ERROR - runtimeRangeFilterReply - propertyValue: ".$propertyValue.PHP_EOL);   
      }
   }
   else{
      $text = "Problem with: No filter added for runtime range";
   } 

   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);   
   $telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]);

   $numberRatedMovies = getNumberRatedMovies($chatId);
   $numberRatedProperties = getNumberRatedProperties($chatId);
   $needNumberOfRatedProperties = 3 - ($numberRatedProperties + $numberRatedMovies);

   if ($needNumberOfRatedProperties <= 0) {      
      $text = "Do you prefer to tell me something else about you \nor can I recommend you a movie?";

      $pagerankCicle = getNumberPagerankCicle($chatId);
      $movie =  oldRecMovieToRefineSelected($chatId, $pagerankCicle);
      file_put_contents("php://stderr", "Let me rate other properties of: ".$movie.PHP_EOL);    
      if ($movie !== "null") {
         $keyboard = [
                        ["🔎 Rate other properties of "."\"".ucwords($movie)."\""],
                        ["🔙 Back to Movies"]
                    ];
      } 
      else {
         $keyboard = userPropertyValueKeyboard();
      }
      
      
   }
   else{
      $text = "Do you want tell me something else about you?";
      $keyboard = startProfileAcquisitionKeyboard();
   }

   $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);
   $telegram->sendMessage(['chat_id' => $chatId, 'text' => $text, 'reply_markup' => $reply_markup]);

}

