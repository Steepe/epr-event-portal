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
use Redis;

/**
 * Handles real-time WebSocket messaging for the event portal.
 * Works with MessagesController + Redis pub/sub.
 */
class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected $connections = []; // Maps user_id → connection

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;

        // 🔁 Start a Redis listener thread
        $this->startRedisListener();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "🟢 Connection opened ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!is_array($data)) {
            return;
        }

        switch ($data['type'] ?? null) {
            case 'register':
                // Register user and socket
                $userId = (int) $data['user_id'];
                $this->connections[$userId] = $from;
                echo "✅ Registered user {$userId} ({$from->resourceId})\n";

                // Update DB or Redis to mark user online
                $this->markUserOnline($userId, $from->resourceId);
                break;

            case 'message':
                // Deliver message live if recipient connected
                $targetId = (int) $data['to'];
                if (isset($this->connections[$targetId])) {
                    $this->connections[$targetId]->send(json_encode([
                        'type' => 'message',
                        'from' => $data['from'],
                        'message' => $data['message'],
                        'timestamp' => date('H:i')
                    ]));
                }
                break;

            default:
                echo "⚠️ Unknown message type\n";
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        // Clean up if connection belongs to a user
        foreach ($this->connections as $userId => $socket) {
            if ($socket === $conn) {
                unset($this->connections[$userId]);
                $this->markUserOffline($userId);
                echo "🔴 User {$userId} disconnected\n";
                break;
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "❌ Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function markUserOnline(int $userId, string $socketId)
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->set("user:{$userId}:socket", $socketId);
        $redis->set("user:{$userId}:online", 1);
    }

    private function markUserOffline(int $userId)
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->set("user:{$userId}:online", 0);
    }

    private function startRedisListener()
    {
        // 👂 This runs in the same process, listening for pushViaSocket() messages
        $pid = pcntl_fork();
        if ($pid == 0) {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->subscribe(['chat_channel'], function ($redis, $chan, $msg) {
                $payload = json_decode($msg, true);
                $socketId = $payload['socket_id'] ?? null;
                $data = $payload['data'] ?? null;

                if ($data && isset($data['type']) && $data['type'] === 'message') {
                    // This is just an example — in production, use a socket map or queue
                    echo "📨 Received Redis message: " . json_encode($data) . "\n";
                }
            });
            exit;
        }
    }
}
