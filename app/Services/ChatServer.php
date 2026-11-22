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
    protected array $sessionConnections = [];
    protected array $connectionSessions = [];
    private array $pendingRegistration = [];
    private ?\Redis $redis = null;
    private string $queue = 'portal_socket_events';

    public function __construct(?LoopInterface $loop = null)
    {
        $this->clients = new \SplObjectStorage;

        if ($loop !== null) {
            $loop->addPeriodicTimer(0.5, function (): void {
                $this->drainRealtimeQueue();
            });
            $loop->addPeriodicTimer(5, function (): void {
                foreach ($this->pendingRegistration as [$connection, $openedAt]) {
                    if ($openedAt <= time() - 10) {
                        $connection->close();
                    }
                }
            });
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->pendingRegistration[$conn->resourceId] = [$conn, time()];
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
                $expires = (int) ($data['expires'] ?? 0);
                $token = (string) ($data['token'] ?? '');
                if (! RealtimeToken::verify($userId, $expires, $token)) {
                    // $from is the socket passed to this handler. Using an
                    // undefined connection here caused realtime:serve to
                    // crash whenever it rejected a registration request.
                    $from->close();
                    return;
                }

                $this->connections[$userId][$from->resourceId] = $from;
                $this->connectionUsers[$from->resourceId] = $userId;
                unset($this->pendingRegistration[$from->resourceId]);

                $this->markUserOnline($userId, $from->resourceId);
                break;

            case 'subscribe_session':
                $sessionId = (int) ($data['session_id'] ?? 0);
                if ($sessionId <= 0 || ! isset($this->connectionUsers[$from->resourceId])) {
                    return;
                }

                $this->sessionConnections[$sessionId][$from->resourceId] = $from;
                $this->connectionSessions[$from->resourceId][$sessionId] = true;
                break;

            default:
                echo "Unknown message type\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->pendingRegistration[$conn->resourceId]);

        $userId = $this->connectionUsers[$conn->resourceId] ?? null;
        if ($userId !== null) {
            unset($this->connections[$userId][$conn->resourceId], $this->connectionUsers[$conn->resourceId]);

            if (empty($this->connections[$userId])) {
                unset($this->connections[$userId]);
                $this->markUserOffline((int) $userId);
            }

        }

        foreach (array_keys($this->connectionSessions[$conn->resourceId] ?? []) as $sessionId) {
            unset($this->sessionConnections[$sessionId][$conn->resourceId]);
            if (empty($this->sessionConnections[$sessionId])) {
                unset($this->sessionConnections[$sessionId]);
            }
        }
        unset($this->connectionSessions[$conn->resourceId]);
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

            if (($event['type'] ?? '') === 'session_chat') {
                $sessionId = (int) ($event['session_id'] ?? 0);
                if ($sessionId > 0) {
                    $this->sendToSession($sessionId, $event);
                }
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

    private function sendToSession(int $sessionId, array $payload): void
    {
        if ($sessionId <= 0 || empty($this->sessionConnections[$sessionId])) {
            return;
        }

        $message = json_encode($payload);
        if ($message === false) {
            return;
        }

        foreach ($this->sessionConnections[$sessionId] as $connection) {
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
            $redis->connect((string) (env('redis.host') ?: '127.0.0.1'), (int) (env('redis.port') ?: 6379), 0.2);

            $password = (string) (env('redis.password') ?: '');
            if ($password !== '') {
                $redis->auth($password);
            }

            $database = (int) (env('redis.database') ?: 0);
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
