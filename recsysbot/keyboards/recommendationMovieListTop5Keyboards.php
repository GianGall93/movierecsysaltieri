<?php 

use GuzzleHttp\Client;

function recommendationMovieListTop5Keyboards($chatId){

	$emojis = require '/app/recsysbot/variables/emojis.php';
	
   $propertyType = 'movie';   
      
   $data = null;
   $data = getUserRecMovieList($chatId);
   //chiama il pagerank
   //$data = getPropertyValueListFromPropertyType($chatId, $propertyType);
  
   $result = array();
   $keyboard = array();
   if ($data !== "null") {
         foreach ($data as $score => $value) {
            $propertyStr = str_replace("http://dbpedia.org/resource/", "", $value);
            $propertyStr = str_replace('_', ' ', $propertyStr); // Replaces all underscore with spaces.
            $propertyArray[$score] = $propertyStr;
            krsort($propertyArray);
         }
      $i = 1;
      if ($propertyType == 'movie') {
         foreach ($propertyArray as $key => $property) {
            //$result[] = array("".$i."^ "."".$emojis['moviecamera'].""." ".ucwords($property));
            $result[] = array("".$emojis['moviecamera'].""." ".$property);
            $i++;
         }
      }
   } 

   $keyboard = array_slice($result, 0, 5);
   $keyboard[] = array("".$emojis['backarrow']." Home","".$emojis['gear']." Profile");

   //file_put_contents("php://stderr", "recommendationMovieListTop5Keyboards return:".$keyboard.PHP_EOL);
   return $keyboard;
}

/*function recommendationMovieListTop5Keyboards($chatId){

   $propertyType = 'movie';
   
      
   //chiama il pagerank
   $data = null;
   $data = getPropertyValueListFromPropertyType($chatId, $propertyType);
  
   $result = array();
   $keyboard = array();
   if ($data !== "null") {
      foreach ($data as $key => $value){
         foreach ($value as $k => $v) {
            $propertyStr = str_replace("http://dbpedia.org/resource/", "", $v);
            $propertyStr = str_replace('_', ' ', $propertyStr); // Replaces all underscore with spaces.
            list($score, $nodo) = explode(',', $propertyStr);
            $propertyArray[$score] = $nodo;
            krsort($propertyArray);
         }
      }

      if ($propertyType == 'movie') {
         foreach ($propertyArray as $key => $property) {
            $result[] = array("".$property);
         }
      }
   } 

   $keyboard = array_slice($result, 0, 5);
   $keyboard[] = array("".$emojis['backarrow']." Return to the list of Properties");

   //file_put_contents("php://stderr", "recommendationMovieListTop5Keyboards return:".$keyboard.PHP_EOL);
   return $keyboard;
}*/