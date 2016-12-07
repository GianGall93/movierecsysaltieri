<?php

use GuzzleHttp\Client;

function getMovieListFromProperty($chatId, $propertyName, $propertyType){

   $userID = $chatId;
   	// $client = new Client(['base_uri'=>'http://localhost:8080']);
   $client = new Client(['base_uri'=>'http://193.204.187.192:8080']);
   $stringGetRequest = '/lodrecsysrestful/restService/movieList/getMovieListFromProperty?userID='.$userID.'&propertyName='.$propertyName.'&propertyType='.$propertyType;
   $response = $client->request('GET', $stringGetRequest);
   $bodyMsg = $response->getBody()->getContents();
   $data = json_decode($bodyMsg);
   
   file_put_contents("php://stderr", "http://193.204.187.192:8080".$stringGetRequest.PHP_EOL);

   return $data;
   
}
