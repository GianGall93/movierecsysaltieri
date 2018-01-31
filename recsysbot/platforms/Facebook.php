<?php 

require_once "recsysbot/platforms/Platform.php";
require "recsysbot/facebook/sendMessage.php";
require "recsysbot/facebook/setBotProfile.php";
require "recsysbot/facebook/setGetStarted.php";
require "recsysbot/facebook/setGreeting.php";
require "recsysbot/facebook/setPersistentMenu.php";
require "recsysbot/facebook/getUserInfo.php";
require "recsysbot/facebook/sendChatAction.php";
$config = require_once '/app/recsysbot/config/movierecsysbot-config.php';


class Facebook implements Platform {
	
	public function getTypingAction() {
		return 'typing_on';
	}
	
	public function sendMessage($chat_id, $text, $reply_markup) {
		sendMessage($chat_id, $text);
		/*
		 * Aggiungere l'invio dei quick reply
		 */
	}
	
	public function sendPhoto($chat_id, $photo, $caption, $reply_markup) {
		
	}
	
	public function sendLink($chat_id, $text, $url, $reply_markup) {
		
	}
	
	public function sendChatAction($chat_id, $action) {
		sendChatAction($chat_id, $action);
	}
	
	private function replyKeyboardMarkup($keyboard) {
		
		$quick_replies = array();
		
		foreach ($keyboard as $item) {
			$quick_replies['content_type'] = 'text';
			$quick_replies['title'] = $item;
			$quick_replies['payload'] = $item;
		}
		
		return $quick_replies;
	
	}
	
	public function getWebhookUpdates() {
		return file_get_contents("php://input");
	}
	
	//TODO Da testare
	public function getMessageInfo($json) {
		
		$message = $json['entry'][0]['messaging'][0];
		$userInfo = getUserInfo($message['sender']['id']);

		$info = array(
				'message' => $message,
				'messageId' => isset ($message['message']['mid']) ? $message['message']['mid'] : "",
				'chatId' => isset ($message['sender']['id']) ? $message['sender']['id'] : "",
				'firstname' => isset ($userInfo) ? $userInfo['first_name'] : "",
				'lastname' => isset ($userInfo) ? $userInfo['last_name'] : "",
				'username' => "", //Non viene restituito dalla chiamata
				'date' => isset ($json['entry'][0]['time']) ? $json['entry'][0]['time'] : "",
				'text' => isset ($message['message']['text']) ? $message['message']['text'] : "",
				'globalDate' => isset ($json['entry'][0]['time']) ? gmdate("Y-m-d\TH:i:s\Z", $json['entry'][0]['time']) : "",
				
				// Contiene il payload per i pulsanti nel caso vengano utilizzati
				'postbackPayload' => isset ($message['postback']['payload']) ? $message['postback']['payload'] : ""
		);
		file_put_contents("php://stderr", $info['postbackPayload'] . PHP_EOL);
		if  ($info['postbackPayload'] != null) {
			$info['text'] = $info['postbackPayload'];
		}
		
		return $info;
	}
	
}

?>