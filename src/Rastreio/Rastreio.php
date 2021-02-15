<?php

namespace Sprained\Rastreio;

use Sprained\Rastreio\Constants\Api;
use Sprained\Rastreio\Services\Message;
use Sprained\Rastreio\Services\Rastreio as ServicesRastreio;

class Rastreio
{
    function __construct()
    {
        $response = $this->response();
        if (isset($response)) {
            $this->processMessage($response);
        }
    }

    /**
     * Função que retorna ultimo chat que foi conectado com o bot
     * 
     * @return array
     */
    private function response()
    {
        $response = file_get_contents(Api::URL . 'getUpdates');

        $json = json_decode($response, true);

        $length = count($json['result']);

        return $json['result'][$length - 1]['message'];

        // $response = file_get_contents("php://input");

        // $json = json_decode($response, true);

        // return $json['message'];
    }

    /**
     * Verifica qual mensagem foi enviada pelo usuario retorna uma mensagem
     * 
     * @param array $message
     */
    private function processMessage($message)
    {
        $send = new Message();
        $rast = new ServicesRastreio();

        $chat_id = $message['chat']['id'];

        if (isset($message['text'])) {
            $text = $message['text'];

            if (strpos($text, '/start') === 0) {
                $send->sendMessage('sendMessage', ['chat_id' => $chat_id, 'text' =>
                'Olá ' . $message['from']['first_name'] . '!
Sou um bot para lhe atualizar do seu rastreio.
Para adicionar um codigo de rastreio digite o comando
/codigo: O codigo']);
            } else if(strpos($text, '/codigo:') === 0) {
                $codigo = trim(explode(':', $text)[1]);

                print_r($rast->registerCod($codigo, $chat_id));
            } else {
                $send->sendMessage('sendMessage', ['chat_id' => $chat_id, 'text' => 'Não entendi, poderia repetir?']);
            }
        } else {
            $send->sendMessage('sendMessage', ['chat_id' => $chat_id, 'text' => 'Favor informe as mensagens em texto']);
        }
    }
}
