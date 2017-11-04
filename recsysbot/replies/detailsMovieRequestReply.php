<?php
use GuzzleHttp\Client;
use Telegram\Bot\FileUpload\InputFile;

function detailsMovieRequestReply($telegram, $chatId, $movie_name, $userMovieprofile){
  
	$emojis = require '/app/recsysbot/variables/emojis.php';
	
/*   $pagerankCicle = getNumberPagerankCicle($chatId);
   $movie = movieToRatingSelected($chatId, $pagerankCicle);
   $movie_name = str_replace(' ', '_', $movie); */

   //inserisce la richiesta di details del film 
   $userMovieprofile->putUserDetailsMovieRequest($chatId, $movie_name);

   $data = getAllPropertyListFromMovie($movie_name);

   $result = array();
   $keyboard = array();

   $directors = $starring = $categories = $genres = $producers = $writers = array();
   $runtimeMinutes = $releaseYear = $title = $imdbRating = $plot = $awards = $poster = $trailer = "";

   if ($data == "null") {
      $text ="Sorry...\nI'm not able to find details 🤔";
      $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);
      $telegram->sendMessage(['chat_id' => $chatId, 'text' => $text]);
   } else {
      foreach ($data as $key => $value){
         foreach ($value as $k => $v) {
            $propertyType = str_replace("http://dbpedia.org/ontology/", "", $v[1]);
            $property = str_replace("http://dbpedia.org/resource/", "", $v[2]);
            $property = str_replace('_', ' ', $property); // Replaces all underscore with spaces.

            switch ($propertyType) {
               case "director":
                  $directors[] = $property;
                  break;
               case "starring":
                  $starring[] = $property;
                  break;
               case "/categories": case "categories": case "category": case "http://purl.org/dc/terms/subject":
                  $property = str_replace("Category:", "", $property);
                  $categories[] = $property;
                  break;
               case "genre":
                   $genres[] = $property;
                  break;
               case "runtimeMinutes":      
                  $runtimeMinutes = $property;
                  break;
               case "writer":
                   $writers[] = $property;
                  break;
               case "producer":
                   $producers[] = $property;
                  break;
               case "releaseYear":
                  $releaseYear = $property;
                  break;
               case "title":
                  $title = $property;
                  break;
               case "plot":
                  $plot = $property;
                  break;
               case "awards":
                  $awards = $property;
                  break;
               case "imdbRating":
                  $imdbRating = $property;
                  break;
               case "poster":
                  $poster = $property;
                  break;
               case "trailer":
                  $trailer = $property;
                  break;
               default:
                  //test
                  //$telegram->sendMessage(['chat_id' => $chatId, 'text' => $textSorry]);
                  break;
            }
         }
      }

      // $director = implode(", ", array_reverse($directors));
      // $star = implode(", ", array_reverse($starring));
      // $category = implode(", ", array_reverse($categories));
      // $genre = implode(", ", array_reverse($genres));

      // $producer = implode(", ",array_reverse($producers));
      // $writer = implode(", ", array_reverse($writers));

      $director = implode(", ", $directors);
      $star = implode(", ", $starring);
      $category = implode(", ", $categories);
      $genre = implode(", ", $genres);

      $producer = implode(", ",$producers);
      $writer = implode(", ", $writers);

      $text = "";
      if ($title !== '') {$text .= "*".$title."*";}
         if ($releaseYear !== '') {$text .= " *(".$releaseYear.")*";}
      //if ($runtimeMinutes !== '') {$text .= " ".$emojis['clockflat']."".$runtimeMinutes."min";}
      if ($runtimeMinutes !== '') {$text .= "\n_".$runtimeMinutes."min_ "."⭐️*".$imdbRating."*"." @imdb";}
      if ($director !== '') {$text .= "\n\n".$emojis['clapperboard']." *Director: *".$director;}
         elseif($producer !== '') {$text .= "\n\n".$emojis['moneybag']." *Producers: *".$producer;}
            elseif($writer !== '') {$text .= "\n\n".$emojis['pen']." *Writers: *".$writer;}

      if ($star !== '') {$text .= "\n\n".$emojis['manhovering']." *Actors: *".$star;}
         elseif($director !== '' && $producer !== '') {$text .= "\n\n".$emojis['moneybag']." *Producers: *".$producer;}
            elseif($director !== '' && $writer !== '') {$text .= "\n\n".$emojis['pen']." *Writers: *".$writer;}
      
      if ($category !== '') {$text .= "\n\n".$emojis['videocassette']." *Categories: *".$category;}
      if ($genre !== '') {$text .= "\n\n".$emojis['filmframe']." *Genres: *".$genre;}

      if ($awards !== '') {$text .= "\n\n🏆 *Awards: *".$awards;}

      if ($plot !== '') {$text .= "\n\n".$plot;}    

/*      if ($poster != '' AND $poster != "N/A" ) {   
         $img = './recsysbot/images/poster.jpg';
         copy($poster, $img); //copia nell'immagine l'immagine del poster
         $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_photo']);
         $telegram->sendPhoto(['chat_id' => $chatId,'photo' => $img]);
         copy('./recsysbot/images/default.jpg', './recsysbot/images/poster.jpg'); //copia nel poster l'immagine di default
      }*/

      if ($poster != '' AND $poster != "N/A" ) {            
         //controllo sulla grandezza dell'immagine della locandina
         $img = './recsysbot/images/poster.jpg';
         copy($poster, $img); //copia nell'immagine l'immagine del poster
         $filesize = filesize($img); // bytes
         $filesize = round($filesize / 1024, 2); 
         file_put_contents("php://stderr", "userMovieprofile->movieRating() filesize: ".$filesize.PHP_EOL);
         if ($filesize <= 4900) {
            $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_photo']);
            $telegram->sendPhoto(['chat_id' => $chatId,'photo' => $img]);
         }
         else{
            copy('./recsysbot/images/default.jpg', './recsysbot/images/poster.jpg'); //copia nel poster l'immagine di default
            $img = './recsysbot/images/poster.jpg';
            $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'upload_photo']);
            $telegram->sendPhoto(['chat_id' => $chatId,'photo' => $img]);
         }
         copy('./recsysbot/images/default.jpg', './recsysbot/images/poster.jpg'); //copia nel poster l'immagine di default
      }


      if ($trailer !== ''){
         $inline_keyboard[] = [
                                 ['text' => 'Trailer', 'url' => $trailer]
                              ];

         $inlineKeyboardMarkup = $telegram->replyKeyboardMarkup(['inline_keyboard' => $inline_keyboard]);
         //$reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);

         $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
         $telegram->sendMessage(['chat_id' => $chatId, 
                                 'text' => $text,
                                 'reply_markup' => $inlineKeyboardMarkup,
                                 'parse_mode' => 'Markdown']);

      }
      else{
         $telegram->sendChatAction(['chat_id' => $chatId, 'action' => 'typing']);  
         $telegram->sendMessage(['chat_id' => $chatId, 
                                 'text' => $text,
                                 'parse_mode' => 'Markdown']);
      }

      


   }   
   
}
