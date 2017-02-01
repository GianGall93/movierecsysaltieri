<?php

function profileReply($telegram, $chatId){

	$telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']); 

	$keyboard = movieOrPropertyToRatingKeyboard($chatId);
	$reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => false]);

	$text = "You have rated:";
	
	$telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);   
	$telegram->sendMessage(['chat_id' => $chatId, 'text' => $text, 'reply_markup' => $reply_markup]); 

}