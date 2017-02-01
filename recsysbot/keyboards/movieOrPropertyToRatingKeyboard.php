<?php 

use GuzzleHttp\Client;

function movieOrPropertyToRatingKeyboard($chatId){  

   file_put_contents("php://stderr", "movieOrPropertyToRatingKeyboard".PHP_EOL);

   $data = getAllMovieOrPropertyRatings($chatId);
//   echo '<pre>'; print_r($data); echo '</pre>';
  
   $keyboard = array();
   $propertyArray = array();
   $result = array();
   if ($data !== "null") {
      foreach ($data as $uriAndTypeKey => $rating) {
      	$property = explode(',', $uriAndTypeKey);
      	$propertyValueUri = $property[0];
      	$propertyTypeUri = $property[1];
         $propertyValue = replaceUriWithName($propertyValueUri);
         $propertyType = replaceUriWithName($propertyTypeUri);
         
         $movieOrPropertyRating = addmovieOrPropertyRating($propertyValue, $propertyType, $rating);
         switch ($propertyType) {
	         case "/directors": case "directors": case "director":
	            $result[] = array("🎬"." ".$movieOrPropertyRating);
	            break;
	         case "/starring": case "starring":
	            $result[] = array("🕴"." ".$movieOrPropertyRating);
	            break;
	         case "/categories": case "categories": case "category":
	            $movieOrPropertyRating = str_replace("Category:", "", $movieOrPropertyRating);
	            $result[] = array("📼"." ".$movieOrPropertyRating);
	            break;
	         case "/genres": case "genres": case "genre":
	            $result[] = array("🎞"." ".$movieOrPropertyRating);
	            break;
	         case "/writers": case "writers": case "writer":
	             $result[] = array("🖊"." ".$movieOrPropertyRating);
	            break;
	         case "/producers": case "producers": case "producer":
	             $result[] = array("💰"." ".$movieOrPropertyRating);
	            break;
	         case "/release year": case "release year": case "releaseYear":
	             $result[] = array("🗓"." ".$movieOrPropertyRating);
	            break;
	         case "/music composers": case "music composers": case "music composer": case "musicComposer": case "music":
	            $result[] = array("🎼"." ".$movieOrPropertyRating);
	            break;
	         case "/runtimeRange": case "runtimeRange": case "runtime":
	            $result[] = array("🕰"." Under ".$movieOrPropertyRating." minutes");
	            break;
	         case "/cinematographies": case "cinematographies": case "cinematography":
	             $result[] = array("📷"." ".$movieOrPropertyRating);
	            break;
	         case "/based on": case "based on": case "basedOn":
	             $result[] = array("📔"." ".$movieOrPropertyRating);
	            break;
	         case "/editings": case "editings": case "editing":
	             $result[] = array("💼"." ".$movieOrPropertyRating);
	            break;
	         case "/distributors": case "distributors": case "distributor":
	             $result[] = array("🏢"." ".$movieOrPropertyRating);
	            break;
	         case "movie": 
	             $result[] = array("📽"." ".$movieOrPropertyRating);
	            break;
	         default:
	            break;
	      }
	   }   
   }     
 
   if(!empty($result)){
	   $keyboard = $result;
	   $keyboard[] = array("🔙 Go to the list of Properties");
	}
	else{
		$keyboard[] = array("Your profile is empty");
		$keyboard[] = array("🔙 Go to the list of Properties");
	}


   return $keyboard;
}
