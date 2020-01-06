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
        $user_chatbase_id = "user-".(string)$vk_response->user_id;
        $chatbase_request_data = $chatbase->userMessage($user_chatbase_id,"OurPlatform",$vk_response->message,"Miroslava",false,false);
        $chatbase->send($chatbase_request_data);
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
            $chatbase_request_data = $chatbase->agentMessage($user_chatbase_id,"OurPlatform",$dialogflow_answer,"Miroslava");
            $chatbase->send($chatbase_request_data);
        } else {
            $chatbase_request_data = $chatbase->agentMessage($user_chatbase_id,"OurPlatform",$dialogflow_answer,"Miroslava",true);
            $chatbase->send($chatbase_request_data);
        }
        break;
}