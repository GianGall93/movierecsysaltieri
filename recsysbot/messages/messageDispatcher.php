<?php

use Recsysbot\Classes\userMovieRecommendation;
use Recsysbot\Classes\UserProfileAcquisitionByMovie;

function messageDispatcher($telegram, $chatId, $messageId, $date, $text, $firstname, $botName){
   $textSorry ="Sorry :) \nI don't understand \nPlease enter a command (es.\"/start\") ";
   $textWorkInProgress = "Sorry :) \nWe are developing this functionality \nSoon will be available ;)";
   $userMovieprofile = new UserProfileAcquisitionByMovie($telegram, $chatId, $messageId, $date, $text, $botName);
   $userMovieRecommendation = new userMovieRecommendation($telegram, $chatId, $messageId, $date, $text, $botName);
   $dialogManager = new \Recsysbot\Classes\DialogManager($telegram, $chatId);

   switch ($text) {
      // /start...
      case strpos($text, '/start'): case strpos($text, '/help'): case strpos($text, '/info'): case strpos($text, '/reset'):
         $context = $text."CommandSelected";
         $replyText = $text;
         $replyFunctionCall = "commandsHandler"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "keyboard";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
         
         $telegram->commandsHandler(true);
         break;
      //Start - Home...
      case strpos($text, 'preferences'): case strpos($text, 'start'): case strpos($text, 'menu'): case strpos($text, 'home'):
         $context = "homeSelected";
         $replyText = $text;
         $replyFunctionCall = "startProfileAcquisitioReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "keyboard";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         startProfileAcquisitioReply($telegram, $chatId);        
         break;
      //Rate movies
      case strpos($text, '🔵'):
         $context = "rateMoviesSelected";
         $replyText = str_replace('🔵', 'icon movies,', $text);
         $replyFunctionCall = "userMovieprofileInstance"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         //prendi un film da valutare
         $movieName = $userMovieprofile->getAndSetUserMovieToRating($chatId); //chiama /getMovieToRating  
         //salva il film da valutare tra i messaggi della chat
         $userMovieprofile->putMovieToRating($movieName);                     //chiama /putChatMessage: movieToRatingSelected - movie, nome film
         //prendi il film da valutare, rispondi e costruisci la tastiera
         $userMovieprofile->handle();
         break;
      //Rate movies
      case strpos($text, 'movies'):
         $context = "rateMoviesSelected";
         $replyText = str_replace('🔵', 'icon movies,', $text);
         $replyFunctionCall = "userMovieprofileInstance"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "keyboard";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         //prendi un film da valutare
         $movieName = $userMovieprofile->getAndSetUserMovieToRating($chatId); //chiama /getMovieToRating  
         //salva il film da valutare tra i messaggi della chat
         $userMovieprofile->putMovieToRating($movieName);                     //chiama /putChatMessage: movieToRatingSelected - movie, nome film
         //prendi il film da valutare, rispondi e costruisci la tastiera
         $userMovieprofile->handle();
         break;
      //Details movies to rating
      case strpos($text, '📋'): 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = movieToRatingSelected($chatId, $pagerankCicle);

         $context = "detailsMovieToRatingSelected";
         $replyText = "detailsMovieToRating,".$movie;
         $replyFunctionCall = "detailReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         $movie_name = str_replace(' ', '_', $movie);
         detailsMovieRequestReply($telegram, $chatId, $movie_name, $userMovieprofile);  
         break;
      //Film Proposto valutato positivamente
      case strpos($text, '👍'):
         $rating = 1;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movieName = movieToRatingSelected($chatId, $pagerankCicle);
         $lastChange = "user"; 

         $context = "likeMovieToRatingSelected";
         $replyText = "likeMovieToRating,".$movieName;
         $replyFunctionCall = "userMovieRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         userMovieRatingReply($telegram, $chatId, $rating, $lastChange, $userMovieprofile);
         break;
      //Film Proposto valutato negativamente
      case strpos($text, '👎'):
         $rating = 0;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movieName = movieToRatingSelected($chatId, $pagerankCicle);
         $lastChange = "user"; 

         $context = "dislikeMovieToRatingSelected";
         $replyText = "dislikeMovieToRating,".$movieName;
         $replyFunctionCall = "userMovieRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         userMovieRatingReply($telegram, $chatId, $rating, $lastChange, $userMovieprofile);
         break;
      //Film Proposto non valutato
      case strpos($text, '➡'):
         $rating = 2;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movieName = movieToRatingSelected($chatId, $pagerankCicle);
         $lastChange = "user"; 

         $context = "skipMovieToRatingSelected";
         $replyText = "skipMovieToRating,".$movieName;
         $replyFunctionCall = "userMovieRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         userMovieRatingReply($telegram, $chatId, $rating, $lastChange, $userMovieprofile);
         break;
      //Rate movie properties
      case strpos($text, '🔴'):
         $context = "rateMoviePropertiesSelected";
         $replyText = str_replace('🔴', 'icon properties,', $text);
         $replyFunctionCall = "basePropertyTypeReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         basePropertyTypeReply($telegram, $chatId);
         break;
      //Rate movie properties
      case strpos($text, 'properties'): 
         $context = "rateMoviePropertiesSelected";
         $replyText = $text;
         $replyFunctionCall = "basePropertyTypeReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "keyboard";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         basePropertyTypeReply($telegram, $chatId);
         break;
      //Vai alla opportuno caso di backNext
      case stristr($text, '👈') !== false: case stristr($text, '👉') !== false:
         //la put del messaggio è richiamata nella funzione
         backNextFunction($telegram, $chatId, $messageId, $text, $botName, $date, $userMovieRecommendation);
         break;
      case strpos($text, '/directors'): case strpos($text, 'directors'): case strpos($text, 'director'):            
         $propertyType = "director";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/starring'): case strpos($text, 'starring'): case strpos($text, 'actor'): case strpos($text, 'actors'):
         $propertyType = "starring";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/categories'): case strpos($text, 'categories'): case strpos($text, 'category'):
         $propertyType = "category";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/genres'): case strpos($text, 'genres'): case strpos($text, 'genre'):
         $propertyType = "genre";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/writers'): case strpos($text, 'writers'): case strpos($text, 'writer'):
         $propertyType = "writer";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/producers'): case strpos($text, 'producers'): case strpos($text, 'producer'):
         $propertyType = "producer";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/musiccomposer'): case strpos($text, 'music composers'): case strpos($text, 'music composer'): case strpos($text, 'music'):
         $propertyType = "musicComposer";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/cinematographies'): case strpos($text, 'cinematographies'): case strpos($text, 'cinematography'):
         $propertyType = "cinematography";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/based on'): case strpos($text, 'based on'): case strpos($text, 'basedOn'):
         $propertyType = "basedOn";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/editings'): case strpos($text, 'editings'): case strpos($text, 'editing'): case strpos($text, 'editor'): case strpos($text, 'editors'):
         $propertyType = "editing";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/distributors'): case strpos($text, 'distributors'): case strpos($text, 'distributor'):
         $propertyType = "distributor";
         $context = "propertyTypeSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/releaseYear'): case strpos($text, 'release year'): case strpos($text, 'releaseyear'):
         $propertyType = "releaseYear";
         $context = "propertyTypeFilterSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '/runtime'): case strpos($text, 'runtime'): case strpos($text, 'runtimeRange'): case strpos($text, 'runtimerange'): case strpos($text, 'runtime range'):
         $propertyType = "runtimeRange";
         $context = "propertyTypeFilterSelected";
         $replyText = $propertyType;
         $replyFunctionCall = "propertyValueReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueReply($telegram, $chatId, $propertyType, $text);
         break;
      case strpos($text, '🎬'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('🎬', 'director,', $text); // Replaces all 🎬 with propertyType.
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '🕴'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('🕴', 'starring,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '📼'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('📼', 'category,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '🎞'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('🎞', 'genre,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '🖊'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('🖊', 'writer,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '💰'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('💰', 'producer,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '🎼'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('🎼', 'musicComposer,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '📷'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('📷', 'cinematography,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '📔'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('📔', 'basedOn,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '💼'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('💼', 'editing,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      case strpos($text, '🏢'):
         $text = clearLastPropertyTypeAndPropertyName($text);
         $text = str_replace('🏢', 'distributor,', $text);
         $context = "propertyTypeAndPropertyValueSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyValueRatingReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyValueRatingReply($telegram, $chatId, $pagerankCicle);
         break;
      //filtro sull'anno di realizzazione
      case strpos($text, '🗓'):
         $text = str_replace('🗓', 'releaseYear,', $text);
         $context = "releaseYearFilterSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyReleaseYearFilterReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyReleaseYearFilterReply($telegram, $chatId, $pagerankCicle);
         break;
      //aggiungi un filtro sull'anno di realizzazione
      case strpos($text, '📆'):
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $reply = releaseYearFilterSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = $reply[1];
         $addFilter = "yes"; 

         $context = "addReleaseYearFilterSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "releaseYearFilterReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         releaseYearFilterReply($telegram, $chatId, $propertyType, $propertyName, $addFilter);
         break;
      //elimina filtro sull'anno di realizzazione
      case strpos($text, '🔸'):
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $reply = releaseYearFilterSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = "no_release_year_filter";
         $addFilter = "no"; 

         $context = "deleteReleaseYearFilterSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "releaseYearFilterReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         releaseYearFilterReply($telegram, $chatId, $propertyType, $propertyName, $addFilter);
         break;
      //filtro sulla durata
      case strpos($text, '🕰'):
         $text = str_replace('🕰', 'runtimeRange, runtime', $text);
         $context = "runtimeRangeFilterSelected";
         $replyText = $text;
         $replyFunctionCall = "propertyRuntimeRangeFilterReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         propertyRuntimeRangeFilterReply($telegram, $chatId, $pagerankCicle);     
         break;
      //aggiungi un filtro sulla durata
      case strpos($text, '⌛'): 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $reply = runtimeRangeFilterSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = $reply[1];
         $addFilter = "yes"; 

         $context = "addRuntimeRangeFilterSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "runtimeRangeFilterReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         runtimeRangeFilterReply($telegram, $chatId, $propertyType, $propertyName, $addFilter);
         break;
      //elimina filtro sulla durata
      case strpos($text, '🔶'):
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $reply = runtimeRangeFilterSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = "no_runtime_range_filter";
         $addFilter = "no"; 

         $context = "deleteRuntimeRangeFilterSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "runtimeRangeFilterReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         runtimeRangeFilterReply($telegram, $chatId, $propertyType, $propertyName, $addFilter);
         break;
      //propertyValue gradita
      case strpos($text, '🙂'):
         $rating = 1;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $reply = propertyTypeAndPropertyValueSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = $reply[1];
         $lastChange = "user";

         $context = "likePropertyValueSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "userPropertyValueRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         userPropertyValueRatingReply($telegram, $chatId, $propertyType, $propertyName, $rating, $lastChange);
         break;
      //propertyValue non gradita
      case strpos($text, '😑'):
         $rating = 0;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $reply = propertyTypeAndPropertyValueSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = $reply[1];
         $lastChange = "user"; 

         $context = "dislikePropertyValueSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "userPropertyValueRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         userPropertyValueRatingReply($telegram, $chatId, $propertyType, $propertyName, $rating, $lastChange);
         break;
      //propertyValue indifferente
      case strpos($text, '🤔'):
         $rating = 2;
         $pagerankCicle = getNumberPagerankCicle($chatId);   
         $reply = propertyTypeAndPropertyValueSelected($chatId, $pagerankCicle);
         $propertyType = $reply[0];
         $propertyName = $reply[1];
         $lastChange = "user";

         $context = "indifferentPropertyValueSelected";
         $replyText = $propertyType.",".$propertyName;
         $replyFunctionCall = "userPropertyValueRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                     
         userPropertyValueRatingReply($telegram, $chatId, $propertyType, $propertyName, $rating, $lastChange);
         break;
      //Modifica la valutazione di un film valutato
      case strpos($text, '📽'):
         $text = str_replace('📽', '', $text);
         $text = clearLastPropertyTypeAndPropertyName($text);         
         $movieName = $text;
         $context = "changeMovieRatedSelected";
         $replyText = "ratedMovie, ".$movieName;
         $replyFunctionCall = "userMovieprofileInstance"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         $userMovieprofile->putMovieToRating($movieName);
         $userMovieprofile->setUserMovieToRating($movieName);
         $userMovieprofile->handle();
         break;
      //Film scelto dalla top 5 list
      case stristr($text, '🎥') !== false:
         $movieName = str_replace('🎥', '', $text);
         $movieName = trim($movieName);
         $page = $userMovieRecommendation->getPageFromMovieName($chatId,$movieName);
         $userMovieRecommendation->setPage($page);

         $context = "recMovieSelected";
         $replyText = $page."recMovie, ".$movieName;
         $replyFunctionCall = "userMovieprofileInstance"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         $userMovieRecommendation->handle();
         break;
      //Vai all'opportuno caso di back function
      case strpos($text, '🔙'):
         //la put del messaggio è richiamata nella funzione
         backFunction($telegram, $chatId, $messageId, $text, $botName, $date, $userMovieRecommendation);
         break;
      //Reset del profilo
      case strpos($text, '✖'):
         $context = "resetProfileSelected";
         $replyText = str_replace('✖', 'icon reset,', $text);
         $replyFunctionCall = "resetReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         resetReply($telegram, $chatId);
         break;
      case strpos($text, 'reset'):
         $context = "resetProfileSelected";
         $replyText = $text;
         $replyFunctionCall = "resetReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "keyboard";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         resetReply($telegram, $chatId);
         break;
      //delete all properties
      case strpos($text, '🔲'):
         $text = "delete, properties";
         $context = "resetCommandSelected";
         $replyText = $text;
         $replyFunctionCall = "resetProfileReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         resetProfileReply($telegram, $chatId, $pagerankCicle);
         break;
      //delete all movies
      case strpos($text, '🔳'):
         $text = "delete, movies";
         $context = "resetCommandSelected";
         $replyText = $text;
         $replyFunctionCall = "resetProfileReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         resetProfileReply($telegram, $chatId, $pagerankCicle);
         break;
      //delete all preference
      case strpos($text, '🗑'):
         $text = "delete, preferences";
         $context = "resetCommandSelected";
         $replyText = $text;
         $replyFunctionCall = "resetProfileReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         resetProfileReply($telegram, $chatId, $pagerankCicle);
         break;
      //conferm delete
      case strpos($text, '✔'):
         $context = "confermResetSelected";
         $replyText = $text;
         $replyFunctionCall = "resetConfirmReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
         
         $reply =  resetCommandSelected($chatId, $pagerankCicle);          
         $deleteType = $reply[0];
         $preference = $reply[1];
         $confirm = "yes";
         resetConfirmReply($telegram, $chatId, $firstname, $deleteType, $preference, $confirm);
         break;
      case strpos($text, '🚫'):
         $context = "confermResetSelected";
         $replyText = $text;
         $replyFunctionCall = "resetConfirmReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
         
         $reply =  resetCommandSelected($chatId, $pagerankCicle);          
         $deleteType = $reply[0];
         $preference = $reply[1];
         $confirm = "no";                 
         resetConfirmReply($telegram, $chatId, $firstname, $deleteType, $preference, $confirm);
         break;
      //Recommend movies
      case strpos($text, '🌐'): 
         $context = "recommendMoviesSelected";
         $replyText = str_replace('🌐', 'icon rec,', $text);
         $replyFunctionCall = "recommendationBackNextMovieReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         recommendationBackNextMovieReply($telegram, $chatId, $userMovieRecommendation);
         break;
       //film raccomandato valutato positivamente
      case strpos($text, '😃'):
         //TODO
         $rating = 1;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = recMovieSelected($chatId, $pagerankCicle);
         $page = $userMovieRecommendation->getPageFromMovieName($chatId, $movie);
         $lastChange = "user";

         $context = "likeRecMovieSelected";
         $replyText = $page."likeRecMovie,".$movie;
         $replyFunctionCall = "recMovieRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         recMovieRatingReply($telegram, $chatId, $rating, $lastChange, $messageId, $text, $botName, $date, $userMovieRecommendation);
         break;
      //film raccomandato valutato negativamente
      case strpos($text, '🙁'):
         //TODO
         $rating = 0;
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = recMovieSelected($chatId, $pagerankCicle);
         $page = $userMovieRecommendation->getPageFromMovieName($chatId,$movie);
         $lastChange = "user";

         $context = "dislikeRecMovieSelected";
         $replyText = $page."dislikeRecMovie,".$movie;
         $replyFunctionCall = "recMovieRatingReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         recMovieRatingReply($telegram, $chatId, $rating, $lastChange, $messageId, $text, $botName, $date, $userMovieRecommendation);
         break;
      //I Like but
      case strpos($text, '🌀'):
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = recMovieSelected($chatId, $pagerankCicle);

         $context = "recMovieToRefineSelected";
         $replyText = "refineRecMovie,".$movie;
         $replyFunctionCall = "callRefineOrRefocusFunction"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         //callRefineOrRefocusFunction($telegram, $chatId, $userMovieRecommendation);
         refineMoviePropertyReply($telegram, $chatId, $userMovieRecommendation);
         break;
      //Details of recommendation movies
      case strpos($text, '📑'):
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = recMovieSelected($chatId, $pagerankCicle);

         $context = "detailsRecMovieSelected";
         $replyText = "detailsRecMovie,".$movie;
         $replyFunctionCall = "detailReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         $movie_name = str_replace(' ', '_', $movie);
         detailsRecMovieRequestReply($telegram, $chatId, $movie_name, $userMovieRecommendation);
         break;
      //Why?
      case strpos($text, '📣'):
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = recMovieSelected($chatId, $pagerankCicle);

         $context = "whyRecMovieSelected";
         $replyText = "whyRecMovie,".$movie;
         $replyFunctionCall = "explanationMovieReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         explanationMovieReply($telegram, $chatId, $userMovieRecommendation);
         break;
      //Change - refocus
      case strpos($text, '💢'): 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $movie = recMovieSelected($chatId, $pagerankCicle);

         //$context = "refocusChangeRecMovieListSelected";
         $context = "recMovieToRefocusSelected";
         $replyText = str_replace('💢', 'icon change,', $text);
         $replyFunctionCall = "refocusChangeRecMovieListReply"; 
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);

         //refocusChangeRecMovieListReply($telegram, $chatId);
         //in questo caso azzera le property e i film...
         refocusChangeRecMovieListReply($telegram, $chatId, $userMovieRecommendation);
         break;
      //Refine le proprietà del film
      case strpos($text, '🔎'):
         $context = "recMovieToRefineSelected";
         $replyText = str_replace('🔎', 'icon refine other properties,', $text);
         $replyFunctionCall = "refineLastMoviePropertyReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                  
         //$replyText = oldRecMovieToRefineSelected($chatId, $pagerankCicle);
         refineLastMoviePropertyReply($telegram, $chatId, $userMovieRecommendation);
         break;
      //profile
      case strpos($text, '👤'):
         $context = "profileSelected";
         $replyText = str_replace('👤', 'icon profile,', $text);
         $replyFunctionCall = "profileReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
         
         profileInRecConxetReply($telegram, $chatId);  
         break;
      // ⚙️ profilo dalla schermata dei film raccomandati
      case strpos($text, '⚙️'):
         $context = "profileSelected";
         $replyText = str_replace('⚙️', 'icon profile,', $text);
         $replyFunctionCall = "profileReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         profileReply($telegram, $chatId);
         break;      
      case strpos($text, '📘'):
         $help = "rateMovieSelected";
         $context = "helpSelected";
         $replyText = "help, rateMovieSelected";
         $replyFunctionCall = "helpReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         helpReply($telegram, $chatId, $help);
         break;
      case strpos($text, '📗'):
         $help = "recMovieSelected";
         $context = "helpSelected";
         $replyText = "help,recMovieSelected";
         $replyFunctionCall = "helpReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         helpReply($telegram, $chatId, $help);
         break;
      case strpos($text, '📙'):
         $help = "profileSelected";
         $context = "helpSelected";
         $replyText = "help,profileSelected";
         $replyFunctionCall = "helpReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         helpReply($telegram, $chatId, $help);
         break;
      case strcasecmp($text, '🌟 🌟 🌟 🌟 🌟') == 0:  case strcasecmp($text, '🌟 🌟 🌟 🌟') == 0:  case strcasecmp($text, '🌟 🌟 🌟') == 0:  case strcasecmp($text, '🌟 🌟') == 0:  case strcasecmp($text, '🌟') == 0:
         $context = "experimentalValutationSelected";
         $replyText = "star,".$text;
         $replyFunctionCall = "experimentCompleteReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         experimentCompleteReply($telegram, $chatId, $text);
         break;
      //nuova sessione/passaggio a nuova configurazione
      case strpos($text, '🤖'): 
         $context = "newSessionSelected";
         $replyText = str_replace('🤖', 'icon new session', $text);
         $replyFunctionCall = "newSessionReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "button";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         newSessionReply($telegram, $chatId, $firstname, $date);
         break;
      case ($text[0] != "/"):
         /*$context = "findPropertyValueOrMovieSelected";
         $replyText = $text;
         $replyFunctionCall = "findPropertyValueOrMovieReply"; 
         $pagerankCicle = getNumberPagerankCicle($chatId);
         $responseType = "keyboard";
         $result = putChatMessage($chatId, $messageId, $context, $replyText, $replyFunctionCall, $pagerankCicle, $botName, $date, $responseType);
                 
         findPropertyValueOrMovieReply($telegram, $chatId,  $messageId, $date, $text, $userMovieprofile);
         break;*/
         $dialogManager->sendMessage($text);
         break;
      default:
         break;
      }
   }
