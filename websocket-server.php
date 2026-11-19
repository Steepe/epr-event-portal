<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 22:57
 */


#!/usr/bin/env php

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as Reactor;
use App\Services\ChatServer;

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$port = (int) (getenv('WEBSOCKET_PORT') ?: 8081);
$loop = LoopFactory::create();
$socket = new Reactor('0.0.0.0:' . $port, $loop);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new ChatServer($loop)
        )
    ),
    $socket,
    $loop
);

echo "ChatServer running on ws://localhost:{$port}\n";
$server->run();
