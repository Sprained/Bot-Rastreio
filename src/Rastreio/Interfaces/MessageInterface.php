<?php

namespace Sprained\Rastreio\Interfaces;

interface MessageInterface
{
    /**
     * Função para envio de mensagem no telegram
     * 
     * @param string $method
     * @param array $params
     */
    public function sendMessage($method, $params);
}