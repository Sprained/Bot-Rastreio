<?php

namespace Sprained\Rastreio\Services;

use Sprained\Rastreio\Constants\Api;
use Sprained\Rastreio\Interfaces\MessageInterface;

class Message implements MessageInterface
{
    public function sendMessage($method, $params)
    {
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => json_encode($params),
                'header'=>  "Content-Type: application/json" .
                "Accept: application/json"
            ]
        ];

        file_get_contents(Api::URL . $method, false, stream_context_create($options));
    }
}