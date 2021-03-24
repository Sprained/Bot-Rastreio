<?php

namespace Sprained\Rastreio\Interfaces;

interface RastreioInterface
{
    /**
     * Registra o codigo de rastreio no banco de dados
     * 
     * @param string $codigo
     * @param int $chat_id
     */
    public function registerCod($codigo, $chat_id);

    /**
     * Verifica se codigo de rastreio se ja encontra cadastrado no banco de dados e retorna infos
     * 
     * @param string $codigo
     * @param string $chat
     * 
     * @return array
     */
    public function verifyCod($codigo, $chat);
    
    /**
     * Retorna todos os codigos de rastreio para verificação
     * 
     * @return array
     */
    public function getCods();
}