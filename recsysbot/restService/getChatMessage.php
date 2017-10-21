<?php
 
use GuzzleHttp\Client; include "urls.php";

function getChatMessage($chatId, $context, $pagerankCicle){

	$userID = $chatId;
	//$client = new Client(['base_uri'=>'http://localhost:8080']);
   $client = new Client(['base_uri'=>getServiceBaseURL()]);
   $stringGetRequest = '/movierecsysrestful/restService/chatMessages/getChatMessage?userID='.$userID.'&context='.$context.'&pagerankCicle='.$pagerankCicle;
   $response = $client->request('GET', $stringGetRequest);
   $bodyMsg = $response->getBody()->getContents();
   $data = json_decode($bodyMsg, true);

   file_put_contents("php://stderr", getServiceBaseURL().$stringGetRequest."/return:".$bodyMsg.PHP_EOL);

   return $data;   
   
}
