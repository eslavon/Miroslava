<?php
require_once __DIR__.'/vendor/autoload.php';

use DialogFlow\Client;
use DialogFlow\Model\Query;
use DialogFlow\Method\QueryApi;

$json = file_get_contents("php://input");
$data = json_decode($json);

switch ($data->type) {
    case "confirmation";
        echo "ecab86fa";
        break;
    case "message_new";
    $user_id = $data->object->message->from_id;
    $peer_id = $data->object->message->peer_id;
    $message = $data->object->message->text;
    $client = new Client('bb07361461f4461f8237521cd2d6706e');
    $queryApi = new QueryApi($client);
    $meaning = $queryApi->extractMeaning($message, [
        'sessionId' =>  '251510315',
        'lang' => 'ru',
    ]);
    $response = new Query($meaning);
    $result =  $response->getResult();
    $s = $result->getFulfillment();
    $params["message"] =  $s->getSpeech();
    echo "ok";
    $params["access_token"] = "93c0f24bbab1e58a14a6cbec5b0fb3e55401b2ce49d4df7990e21e94475e046b5f4a87c425812bd652bf0";
    $params["v"] = "5.103";
    $params["peer_id"] = $peer_id;
    $params["random_id"] = mt_rand(0,9999999);
    
    $parameters["access_token"] = "93c0f24bbab1e58a14a6cbec5b0fb3e55401b2ce49d4df7990e21e94475e046b5f4a87c425812bd652bf0892";
    $parameters["v"] = "5.103";
    $parameters["peer_id"] = $peer_id;
    //$parameters["group_id"] = 190338372;
    $parameters["type"] = "typing";
    $url = "https://api.vk.com/method/messages.setActivity";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    $res = curl_exec($ch);
    curl_close($ch);
    
    sleep(1);
    
    $url = "https://api.vk.com/method/messages.send";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($ch);
    curl_close($ch);
    
    break;
}