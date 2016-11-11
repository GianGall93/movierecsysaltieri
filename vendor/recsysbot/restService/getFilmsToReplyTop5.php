<?php 

function getFilmsToReplyTop5($telegram, $chatId){


   $keyboard = getKeyboardFilmsWithScoreTop5($chatId);
       
   if (sizeof($keyboard) == 1) {

      $text = "Unfortunately, i didn’t find movies for you.\nLet me know additional information about you."; 
      $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
      $telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]); 
      //torna al menu...
   } 
   else {
      foreach ($keyboard as $key => $property) {
         if ($property[0] != "Menu") {
            $movie = $property[0];
            getFilmDetailTop5($telegram, $chatId, $movie);
         }         
      }

      $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

      $text = "Please, choose the movie you like more";
      $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
      $telegram->sendMessage(['chat_id' => $chatId, 
                              'text' => $text,
                              'reply_markup' => $reply_markup]);
   } 
}