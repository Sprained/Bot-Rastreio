<?php

namespace Sprained\Rastreio\Database;

class Conexao
{
    function __construct()
    {
        $this->connect();
    }

    function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Cria conexão com banco de dados
     * 
     * @return object
     */
    protected function connect()
    {
        return mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
    }

    /**
     * Fechar conexão com banco de dados
     */
    private function disconnect()
    {
        mysqli_close($this->connect());
    }
}