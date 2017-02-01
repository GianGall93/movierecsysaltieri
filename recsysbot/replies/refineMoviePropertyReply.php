<?php

//function refineMoviePropertyReply($telegram, $chatId, $text, $pagerankCicle){
function refineMoviePropertyReply($telegram, $chatId){

   $pagerankCicle = getNumberPagerankCicle($chatId);
   $movie = lastMovieToRefine($chatId, $pagerankCicle);
   

   $text = "".$pagerankCicle."^ cicle of recommendation...";
   $text .= "\nIn this cycle you have chosen:";
   $text .= "\n\"".ucwords($movie)."\"";
   $text .= "\nWe continue with Classic Refine";

   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
   $telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]);
   
   putNumberPagerankCicle($chatId, 1);

	$keyboard = refineMoviePropertyKeyboard($chatId, $movie);
   $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

   $text = "Which properties of the movie you want to change?";
   $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
   $telegram->sendMessage(['chat_id' => $chatId, 
                           'text' => $text,
                           'reply_markup' => $reply_markup]);
}