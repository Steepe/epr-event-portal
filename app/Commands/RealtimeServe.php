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

        // The event/ev/uv loops can serve thousands of sockets, but the CLI
        // process still needs enough file descriptors for those connections.
        if ((extension_loaded('event') || extension_loaded('ev') || extension_loaded('uv'))
            && function_exists('posix_getrlimit') && function_exists('posix_setrlimit')) {
            $limits = posix_getrlimit();
            $soft = (int) ($limits['soft openfiles'] ?? 0);
            $hard = $limits['hard openfiles'] ?? 0;
            $hardValue = $hard === 'unlimited' ? POSIX_RLIMIT_INFINITY : (int) $hard;
            $target = $hard === 'unlimited' ? 8192 : min(8192, $hardValue);
            if ($target > $soft && ! @posix_setrlimit(POSIX_RLIMIT_NOFILE, $target, $hardValue)) {
                CLI::write('Unable to raise the WebSocket open-file limit.', 'yellow');
            }
        }

        $loop = LoopFactory::create();
        if ($loop instanceof \React\EventLoop\StreamSelectLoop) {
            CLI::write('Warning: stream_select limits this process to about 1,024 sockets. Install ext-event, ext-ev, or ext-uv for large events.', 'yellow');
        }
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
