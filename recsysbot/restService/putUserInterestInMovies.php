<?php

use GuzzleHttp\Client; include "urls.php";

function putUserInterestInMovies($chatId, $interestInMovies){

	$userID = $chatId;
	//$client = new Client(['base_uri'=>'http://localhost:8080']);
   $client = new Client(['base_uri'=>getServiceBaseURL()]);
   $stringGetRequest = '/movierecsysrestful/restService/interestInMovies/putUserInterestInMovies?userID='.$userID.'&interestInMovies='.$interestInMovies;
   $response = $client->request('GET', $stringGetRequest);
   $bodyMsg = $response->getBody()->getContents();
   $data = json_decode($bodyMsg);

   file_put_contents("php://stderr", getServiceBaseURL().$stringGetRequest."/return:".$data.PHP_EOL);

   return $data;
   
}