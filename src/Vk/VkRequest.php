<?php


namespace Eslavon\Miroslava\Vk;


class VkRequest
{
    private $access_token;
    private $version;

    public function __construct($access_token,$version)
    {
        $this->access_token = $access_token;
        $this->version = $version;
    }

    private function request($method,$parameters)
    {
        $url = "https://api.vk.com/method/".$method;
        $parameters["access_token"] = $this->access_token;
        $parameters["v"] = $this->version;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    private function random()
    {
        return mt_rand(0,999999999);
    }

    public function sendMessage($peer_id,$message)
    {
        $parameters["peer_id"] = $peer_id;
        $parameters["message"] = $message;
        $parameters["random_id"] = $this->random();
        $method = "messages.send";
        $this->request($method,$parameters);
    }
}