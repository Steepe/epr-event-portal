<?php

namespace App\Services;

class RealtimeNotifier
{
    private const QUEUE = 'portal_socket_events';

    public static function push(array $event): bool
    {
        $payload = json_encode($event);
        if ($payload === false) {
            return false;
        }

        try {
            $redis = new \Redis();
            $redis->connect(self::host(), self::port(), 0.2);

            $password = self::password();
            if ($password !== '') {
                $redis->auth($password);
            }

            $database = self::database();
            if ($database > 0) {
                $redis->select($database);
            }

            $redis->rPush(self::QUEUE, $payload);
            $redis->close();

            return true;
        } catch (\Throwable $e) {
            log_message('debug', '[RealtimeNotifier] Unable to push websocket event: ' . $e->getMessage());
            return false;
        }
    }

    private static function host(): string
    {
        return (string) (env('redis.host') ?: '127.0.0.1');
    }

    private static function port(): int
    {
        return (int) (env('redis.port') ?: 6379);
    }

    private static function password(): string
    {
        return (string) (env('redis.password') ?: '');
    }

    private static function database(): int
    {
        return (int) (env('redis.database') ?: 0);
    }
}
