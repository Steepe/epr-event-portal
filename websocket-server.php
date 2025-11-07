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
use App\Services\ChatServer;

require __DIR__ . '/vendor/autoload.php';

// ✅ Use port 8081 (not 8080)
$port = 8081;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ChatServer()
        )
    ),
    $port
);

echo "🚀 ChatServer running on ws://localhost:{$port}\n";
$server->run();
