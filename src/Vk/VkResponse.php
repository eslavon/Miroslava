<?php


namespace Eslavon\Miroslava\Vk;


class VkResponse
{
    public $type;
    public $group_id;
    public $user_id;
    public $peer_id;
    public $message;

    public function __construct($json)
    {
        $data = json_decode($json);
        $this->type = $data->type;
        switch ($this->type) {
            case "confirmation";
                $this->confirmation($data);
                break;
            case "message_new";
                $this->newMessage($data);
                break;
        }
    }

    private function confirmation($data)
    {
        $this->group_id = $data->group_id;
    }

    private function newMessage($data)
    {
        $this->group_id = $data->group_id;
        $this->user_id = $data->object->message->from_id;
        $this->peer_id = $data->object->message->peer_id;
        $this->message = $data->object->message->text;
    }
}