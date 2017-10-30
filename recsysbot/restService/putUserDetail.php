<?php
use GuzzleHttp\Client;
function putUserDetail($chatId, $firstname, $lastname, $username) {
	$userID = $chatId;
	// $client = new Client(['base_uri'=>'http://localhost:8080']);
	$client = new Client ( [ 
			'base_uri' => 'http://127.0.0.1:8080' 
	] );
	$stringGetRequest = '/movierecsysrestful/restService/detail/putUserDetail?userID=' . $userID . '&firstname=' . $firstname . '&lastname=' . $lastname . '&username=' . $username;
	$response = $client->request ( 'GET', $stringGetRequest );
	$bodyMsg = $response->getBody ()->getContents ();
	$data = json_decode ( $bodyMsg );
	//192.168.1.107
	file_put_contents ( "php://stderr", "http://127.0.0.1:8080" . $stringGetRequest . "/return:" . $data . PHP_EOL );
	
	return $data;
}
