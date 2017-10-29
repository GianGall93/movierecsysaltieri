<?php

namespace Recsysbot\Classes;
use Exception;
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
        $client = new Client(['base_uri'=> getApiAiBaseURL()]);
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
            $this->sendImage($data['result']['fulfillment']['data']['image'],
                $data['result']['fulfillment']['data']['imageCaption']);

            //Se c'è un messaggio da visualizzare dopo l'immagine
            if ($data['result']['fulfillment']['data']['postImageSpeech'] != null) {
                $this->writeText($data['result']['fulfillment']['data']['postImageSpeech']);
            }
        }

        //Controlla se si deve chiamare un'api
        if ($data['result']['fulfillment']['data']['apiURL'] != null) {
            file_put_contents("php://stderr", "Found an auxiliary API request".PHP_EOL);
            $this->sendAuxiliaryRequest($data['result']['fulfillment']['data']['apiURL']);
        }
        if ($data['result']['fulfillment']['data']['auxAPI'] != null) {
            file_put_contents("php://stderr", "Found an auxiliary POST API request".PHP_EOL);
            $this->sendAuxiliaryPostRequest($data['result']['fulfillment']['data']['auxAPI']);
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

    public function sendAuxiliaryPostRequest($data) {
        $url = $data['apiURL'];
        $parameters = $data['parameters'];
        $client = new Client();
        file_put_contents("php://stderr", "Body is:".json_encode($parameters).PHP_EOL);
        $response = $client->post($url, [
            'json' => $parameters
        ]);
        $bodyMsg = $response->getBody()->getContents();
        $responseDecoded = json_decode($bodyMsg, true);
        file_put_contents("php://stderr", "Response is:".print_r($responseDecoded).PHP_EOL);
        $this->handleAuxiliaryRequestResponse($responseDecoded);
    }

    public function handleAuxiliaryRequestResponse($data) {
        $textResponse = $data['speech'];
        /*$this->telegram->sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);
        $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $textResponse]);*/
        $this->writeText($textResponse);

        if ($data['data']['image'] != null) {
            $this->sendImage($data['data']['image'], $data['data']['imageCaption']);

            file_put_contents("php://stderr", "After sendImage".PHP_EOL);

            //Se c'è un messaggio da visualizzare dopo l'immagine
            if ($data['data']['postImageSpeech'] != null) {
                $this->writeText($data['data']['postImageSpeech']);
            }
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

    public function sendImage($image, $caption) {
        try {
            $response = $this->telegram->sendPhoto([
                'chat_id' => $this->chatId,
                'photo' => $image,
                'caption' => $caption
            ]);
        } catch (Exception $e) {
            file_put_contents("php://stderr", "Image is not valid! Sending default image".PHP_EOL);
            $this->telegram->sendPhoto([
                'chat_id' => $this->chatId,
                'photo' => "./recsysbot/images/default.jpg",
                'caption' => $caption
            ]);
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