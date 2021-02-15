<?php

namespace Sprained\Rastreio;

use DateTime;
use Sprained\Correios\Tracking;
use Sprained\Rastreio\Database\Conexao;
use Sprained\Rastreio\Services\Message;
use Sprained\Rastreio\Services\Rastreio;
use Sprained\Correios\Exceptions\TrackingException;

class Verify
{
    function __construct()
    {
        $this->verifyCod();
    }

    /**
     * Verifica o codigo de rastreio e retorna mensagem para o usuario o qual cadastrou o codigo
     */
    private function verifyCod()
    {
        $con = new Conexao();
        $send = new Message();
        $rast = new Rastreio();

        $db = $con->connect();
        $cods = $rast->getCods();

        foreach ($cods as $value) {
            if (explode(' ', $value['updated_at'])[0] != date('Y-m-d')) {

                $track = new Tracking();

                $rastreio = json_decode($track->tracking($value['cod_rastreio']), true);
                if (isset($rastreio['message'])) {
                    print_r($rastreio);
                    die;
                    $db->query("DELETE FROM rastreio WHERE id = " . $value['id']);
                } else if ($rastreio['last_status'] == 'Objeto entregue ao destinatário') {
                    $db->query("DELETE FROM rastreio WHERE id = " . $value['id']);
                } else {
                    $new_date = date('Y-m-d H:i:s', strtotime($rastreio['last_date']));
                    // echo $new_date;die;
                    if ($value['updated_at'] != $new_date) {
                        $destino = isset($rastreio['tracking'][0]['destino']) ? 'Destino: ' . $rastreio['tracking'][0]['destino'] : '';
                        $send->sendMessage('sendMessage', ['chat_id' => $value['chat_id'], 'text' =>
'Status: ' . $rastreio['tracking'][0]['status'] . '
Local: ' . $rastreio['tracking'][0]['local'] . '
' . $destino]);

                        $db->query("UPDATE rastreio SET updated_at = '$new_date' WHERE id = " . $value['id']);
                    }
                }
            }
        }
    }
}
