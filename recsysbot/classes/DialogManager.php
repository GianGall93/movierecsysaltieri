<?php

namespace Recsysbot\Classes;
use GuzzleHttp\Client;

class DialogManager
{
    protected $telegram;
    protected $chatId;

    public function __construct($telegram, $chatId){
        $this->setTelegram($telegram);
        $this->setChatId($chatId);
    }

    public function sendMessage($text) {
        $client = new Client(['base_uri'=>'https://api.api.ai']);
        $stringGetRequest = '/v1/query?v=20150910&query='.$text.'&sessionId='.$this->chatId;
        $response = $client->request('GET', $stringGetRequest, [
            'headers' => [
                'Authorization'     => 'Bearer 4ef6680948dc45ed8097591046388029'
            ]
        ]);
        $bodyMsg = $response->getBody()->getContents();
        $data = json_decode($bodyMsg, true);
        file_put_contents("php://stderr", "Sent message /return:".print_r($data, true).PHP_EOL);

        $this->handleResponse($data);
    }

    public function handleResponse($data) {
        $textResponse = $data['result']['fulfillment']['speech'];
        /*$this->telegram->sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);
        $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $textResponse]);*/
        $this->writeText($textResponse);

        //Controlla se c'è un'immagine da visualizzare
        if ($data['result']['fulfillment']['data']['image'] != null) {
            $this->telegram->sendPhoto([
                'chat_id' => $this->chatId,
                'photo' => $data['result']['fulfillment']['data']['image'],
                'caption' => $data['result']['fulfillment']['data']['imageCaption']
            ]);
        }

        //Controlla se si deve chiamare un'api
        if ($data['result']['fulfillment']['data']['apiURL'] != null) {
            file_put_contents("php://stderr", "Found an auxiliary API request".PHP_EOL);
            $this->sendAuxiliaryRequest($data['result']['fulfillment']['data']['apiURL']);
        }

    }

    public function sendAuxiliaryRequest($uri) {
        file_put_contents("php://stderr", "Sending a request to:".$uri.PHP_EOL);

        $client = new Client();
        $response = $client->request('GET', $uri);
        $bodyMsg = $response->getBody()->getContents();
        $data = json_decode($bodyMsg, true);

        file_put_contents("php://stderr", "Auxiliary request sent:".print_r($data, true).PHP_EOL);
        $this->handleAuxiliaryRequestResponse($data);
    }

    public function handleAuxiliaryRequestResponse($data) {
        $textResponse = $data['speech'];
        /*$this->telegram->sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);
        $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $textResponse]);*/
        $this->writeText($textResponse);

        if ($data['data']['image'] != null) {
            $response = $this->telegram->sendPhoto([
                'chat_id' => $this->chatId,
                'photo' => $data['data']['image'],
                'caption' => $data['data']['imageCaption']
            ]);

        }
    }

    public function writeText($text) {
        $messages = explode("\n\n", $text);
        for ($i = 0; $i < sizeof($messages); $i++) {
            if (strlen($messages[$i]) > 0) {
                $this->telegram->sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);
                $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $messages[$i]]);
            }
        }
    }

    private function setTelegram($telegram){
        $this->telegram = $telegram;
    }
    public function getTelegram(){
        return $this->telegram;
    }
    public function setChatId($chatId){
        $this->chatId = $chatId;
    }
    public function getChatId(){
        return $this->chatId;
    }
}