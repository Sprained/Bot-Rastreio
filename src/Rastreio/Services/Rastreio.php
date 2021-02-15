<?php

namespace Sprained\Rastreio\Services;

use Sprained\Correios\Tracking;
use Sprained\Rastreio\Database\Conexao;
use Sprained\Rastreio\Services\Message;
use Sprained\Correios\Exceptions\TrackingException;
use Sprained\Rastreio\Interfaces\RastreioInterface;

class Rastreio extends Conexao implements RastreioInterface
{
    public function verifyCod($codigo)
    {
        $db = $this->connect();

        if($cod = $db->prepare('SELECT chat_id, updated_at FROM rastreio WHERE cod_rastreio = ?')) {
            $chat_id = '';
            $updated_at = '';
            $cod_replace = preg_replace('/[\'"]/i', null, $codigo);

            $cod->bind_param('s', $cod_replace);
            $cod->execute();
            $cod->bind_result($chat_id, $updated_at);

            if(!$cod->fetch()) {
                $cod->close();
                return null;
            }

            $cod->close();
        }

        return ['chat_id' => $chat_id, 'updated_at' =>$updated_at];
    }

    public function getCods()
    {
        $db = $this->connect();

        $cods = $db->query('SELECT * FROM rastreio')->fetch_all(MYSQLI_ASSOC);

        return $cods;
    }

    public function registerCod($codigo, $chat_id)
    {
        $send = new Message();
        $cod_replace = preg_replace('/[\'"]/i', null, $codigo);

        try {
            $track = new Tracking();

            $rastreio = json_decode($track->tracking($cod_replace), true);
            $destino = isset($rastreio['tracking'][0]['destino']) ? 'Destino: ' . $rastreio['tracking'][0]['destino'] : '';
        } catch(TrackingException $e) {
            return $send->sendMessage('sendMessage', ['chat_id' => $chat_id, 'text' => json_decode($e->getMessage(), true)['message']]);
        }

        if($this->verifyCod($cod_replace)) {
            return $send->sendMessage('sendMessage', ['chat_id' => $chat_id, 'text' =>

'Código de rastreio já cadastrado!

Status: ' . $rastreio['tracking'][0]['status'] . '
Local: ' . $rastreio['tracking'][0]['local'] .'
' . $destino
            ]);
        }

        $db = $this->connect();

        $cod = $db->prepare('INSERT INTO rastreio (chat_id, cod_rastreio) VALUES (?,?)');
        $cod->bind_param('is', $chat_id, $cod_replace);
        if($cod->execute()) {
            $cod->close();

            return $send->sendMessage('sendMessage', ['chat_id' => $chat_id, 'text' =>
'Rastreio cadastrado na base de dados!

Status: ' . $rastreio['tracking'][0]['status'] . '
Local: ' . $rastreio['tracking'][0]['local'] .'
' . $destino
            ]);
        }
    }
}