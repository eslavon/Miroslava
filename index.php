<?php
require_once __DIR__."/vendor/autoload.php";

use Dotenv\Dotenv;
use DialogFlow\Client;
use DialogFlow\Model\Query;
use DialogFlow\Method\QueryApi;
use Eslavon\Miroslava\Chatbase\Chatbase;
use Eslavon\Miroslava\Vk\VkResponse;
use Eslavon\Miroslava\Vk\VkRequest;

$dotenv  =  Dotenv::createImmutable ( __DIR__ );
$dotenv -> load ();

$json = file_get_contents("php://input");
if ($json == null) {
    exit (200);
}
$vk_response = new VkResponse($json);
$vk_request = new VkRequest($_ENV["VK_ACCESS_TOKEN"],$_ENV["VK_API_VERSION"]);
$chatbase = new Chatbase($_ENV["CHATBASE_TOKEN"]);


switch ($vk_response->type) {
    case "confirmation";
        $_ENV ["VK_CONFIRM_STRING"];
        break;
    case "message_new";
        echo "ok";
        $client = new Client($_ENV["DIALOG_FLOW_API_KEY"]);
        $queryApi = new QueryApi($client);
        $chatbase->userMessage($vk_response->user_id,"vk",$vk_response->message,"Location_chat",false,false);
        $meaning = $queryApi->extractMeaning($vk_response->message, [
            'sessionId' =>  '251510315',
            'lang' => 'ru',
        ]);
        $dialogflow_response = new Query($meaning);
        $dialogflow_result =  $dialogflow_response->getResult();
        $dialogflow_data = $dialogflow_result->getFulfillment();
        $dialogflow_answer =  $dialogflow_data->getSpeech();
        if ($dialogflow_answer !== "не распознано") {
            $vk_request->sendMessage($vk_response->peer_id,$dialogflow_answer);
            $chatbase->agentMessage($vk_response->user_id,"vk",$dialogflow_answer,"Location_chat");
        } else {
            $chatbase->agentMessage($vk_response->user_id,"vk",$dialogflow_answer,"Location_chat",true);
        }
    break;
}