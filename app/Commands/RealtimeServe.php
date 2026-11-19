<?php

namespace App\Commands;

use App\Services\ChatServer;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as SocketServer;

class RealtimeServe extends BaseCommand
{
    protected $group = 'Realtime';
    protected $name = 'realtime:serve';
    protected $description = 'Run the authenticated portal WebSocket server.';
    protected $usage = 'realtime:serve [--host 127.0.0.1] [--port 8081]';

    public function run(array $params)
    {
        $host = (string) (CLI::getOption('host') ?: env('websocket.bindHost') ?: '127.0.0.1');
        $port = (int) (CLI::getOption('port') ?: env('websocket.port') ?: 8081);

        if ($port < 1 || $port > 65535) {
            CLI::error('WebSocket port must be between 1 and 65535.');
            return;
        }

        if (trim((string) (env('websocket.secret') ?: env('WEBSOCKET_SECRET') ?: '')) === '') {
            CLI::error('Set websocket.secret before starting the WebSocket server.');
            return;
        }

        $loop = LoopFactory::create();
        $socket = new SocketServer($host . ':' . $port, $loop);
        $server = new IoServer(
            new HttpServer(new WsServer(new ChatServer($loop))),
            $socket,
            $loop
        );

        CLI::write("Portal WebSocket server listening on {$host}:{$port}", 'green');
        $server->run();
    }
}
