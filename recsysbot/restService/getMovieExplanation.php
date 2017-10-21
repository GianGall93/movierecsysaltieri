<?php
 
use GuzzleHttp\Client; include "urls.php";

function getMovieExplanation($chatId, $movie_name){

	$userID = $chatId;
 	//$client = new Client(['base_uri'=>'http://localhost:8080']);
   $client = new Client(['base_uri'=>getServiceBaseURL()]);
   $stringGetRequest ='/movierecsysrestful/restService/getMovieExplanation?userID='.$userID.'&movieName='.urlencode($movie_name);
   $response = $client->request('GET', $stringGetRequest);
   $bodyMsg = $response->getBody()->getContents();
   $data = json_decode($bodyMsg);

   file_put_contents("php://stderr", getServiceBaseURL().$stringGetRequest."/return:".$data.PHP_EOL);

   return $data;
   
}