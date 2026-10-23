<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 03/11/2025
 * Time: 22:56
 */


namespace App\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

/**
 * Handles real-time WebSocket messaging for the event portal.
 * The HTTP app pushes events into Redis, and this server delivers them to
 * registered browser sockets.
 */
class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected array $connections = [];
    protected array $connectionUsers = [];
    private ?\Redis $redis = null;
    private string $queue = 'portal_socket_events';

    public function __construct(?LoopInterface $loop = null)
    {
        $this->clients = new \SplObjectStorage;

        if ($loop !== null) {
            $loop->addPeriodicTimer(0.5, function (): void {
                $this->drainRealtimeQueue();
            });
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Connection opened ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!is_array($data)) {
            return;
        }

        switch ($data['type'] ?? null) {
            case 'register':
                $userId = (int) ($data['user_id'] ?? 0);
                if ($userId <= 0) {
                    return;
                }

                $this->connections[$userId][$from->resourceId] = $from;
                $this->connectionUsers[$from->resourceId] = $userId;
                echo "Registered user {$userId} ({$from->resourceId})\n";

                $this->markUserOnline($userId, $from->resourceId);
                break;

            case 'message':
                $targetId = (int) $data['to'];
                $this->sendToUser($targetId, [
                        'type' => 'message',
                        'from' => $data['from'] ?? null,
                        'message' => $data['message'] ?? '',
                        'timestamp' => date('H:i'),
                ]);
                break;

            default:
                echo "Unknown message type\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        $userId = $this->connectionUsers[$conn->resourceId] ?? null;
        if ($userId !== null) {
            unset($this->connections[$userId][$conn->resourceId], $this->connectionUsers[$conn->resourceId]);

            if (empty($this->connections[$userId])) {
                unset($this->connections[$userId]);
                $this->markUserOffline((int) $userId);
            }

            echo "User {$userId} disconnected\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function markUserOnline(int $userId, string $socketId)
    {
        $redis = $this->redis();
        if ($redis === null) {
            return;
        }

        $redis->set("user:{$userId}:socket", $socketId);
        $redis->set("user:{$userId}:online", 1);
    }

    private function markUserOffline(int $userId)
    {
        $redis = $this->redis();
        if ($redis === null) {
            return;
        }

        $redis->set("user:{$userId}:online", 0);
    }

    private function drainRealtimeQueue(): void
    {
        $redis = $this->redis();
        if ($redis === null) {
            return;
        }

        for ($i = 0; $i < 25; $i++) {
            $message = $redis->lPop($this->queue);
            if ($message === false || $message === null) {
                return;
            }

            $event = json_decode((string) $message, true);
            if (! is_array($event)) {
                continue;
            }

            $targetUserId = (int) ($event['to'] ?? $event['receiver_id'] ?? 0);
            if ($targetUserId <= 0) {
                continue;
            }

            $this->sendToUser($targetUserId, $event);
        }
    }

    private function sendToUser(int $userId, array $payload): void
    {
        if ($userId <= 0 || empty($this->connections[$userId])) {
            return;
        }

        $message = json_encode($payload);
        if ($message === false) {
            return;
        }

        foreach ($this->connections[$userId] as $connection) {
            $connection->send($message);
        }
    }

    private function redis(): ?\Redis
    {
        if ($this->redis instanceof \Redis) {
            return $this->redis;
        }

        if (! class_exists(\Redis::class)) {
            return null;
        }

        try {
            $redis = new \Redis();
            $redis->connect(getenv('REDIS_HOST') ?: '127.0.0.1', (int) (getenv('REDIS_PORT') ?: 6379), 0.2);

            $password = (string) (getenv('REDIS_PASSWORD') ?: '');
            if ($password !== '') {
                $redis->auth($password);
            }

            $database = (int) (getenv('REDIS_DATABASE') ?: 0);
            if ($database > 0) {
                $redis->select($database);
            }

            $this->redis = $redis;
        } catch (\Throwable $e) {
            echo "Redis unavailable: {$e->getMessage()}\n";
            $this->redis = null;
        }

        return $this->redis;
    }
}
