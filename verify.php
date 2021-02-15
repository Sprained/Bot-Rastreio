<?php

require __DIR__ . '/vendor/autoload.php';

use Sprained\Rastreio\Verify;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$verify = new Verify();