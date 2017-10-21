<?php

use GuzzleHttp\Client; include "urls.php";

function putPropertyUriRating($chatId, $propertyTypeURI, $propertyURI, $rating, $lastChange){

	$userID = $chatId;
	//$client = new Client(['base_uri'=>'http://localhost:8080']);
   $client = new Client(['base_uri'=>getServiceBaseURL()]);
   $stringGetRequest = '/movierecsysrestful/restService/propertyRating/putPropertyRating?userID='.$userID.'&propertyTypeURI='.urlencode($propertyTypeURI).'&propertyURI='.urlencode($propertyURI).'&rating='.$rating.'&lastChange='.$lastChange;
   $response = $client->request('GET', $stringGetRequest);
   $bodyMsg = $response->getBody()->getContents();
   $data = json_decode($bodyMsg);

   file_put_contents("php://stderr", getServiceBaseURL().$stringGetRequest."/return:".$data.PHP_EOL);

   return $data;
   
}