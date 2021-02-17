<?php

namespace Sprained\Rastreio\Services;

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

        file_get_contents($_ENV['API_URL'] . $method, false, stream_context_create($options));
    }
}