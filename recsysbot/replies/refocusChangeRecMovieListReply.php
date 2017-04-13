<?php

//Da capire se può essere ancora utile questa funzione
function refocusChangeRecMovieListReply($telegram, $chatId){

   $pagerankCicle = getNumberPagerankCicle($chatId);
   $movie = recMovieToRefocusSelected($chatId, $pagerankCicle);
   
   $text = refocusMovieFunction($chatId, $movie);
   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
   $telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]);   
   
   $text = "Do you prefer to tell me something else about you \nor can I recommend you a movie?";
   $keyboard = userPropertyValueKeyboard();

   $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);
   $telegram->sendMessage(['chat_id' => $chatId, 
                           'text' => $text,
                           'reply_markup' => $reply_markup]);
}