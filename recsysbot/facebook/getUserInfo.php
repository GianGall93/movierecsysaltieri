<?php

function getUserInfo($chat_id) {
	
	$utils = require '/app/recsysbot/facebook/utils.php';
	$token = $utils->token();
	
	$url = 'https://graph.facebook.com/v2.6/' . $chat_id . '?fields=first_name,last_name&access_token=' . $token;
	$result = json_decode(file_get_contents($url), true);
	file_put_contents("php://stderr", "\nUser info: " . $result);
	return $result;
}