<?php

namespace App\Modules\MobileApp\Libraries;

/**
 * Notifications shown in the mobile bell sheet and on the notifications screen.
 * Mirrors the sources used by the desktop portal: pending connection requests
 * and admin portal messages, plus unread replies from Country Managers.
 */
class MobileNotifications
{
    public const ICONS = [
        'Sessions'   => 'fa-play-circle',
        'Messages'   => 'fa-comment',
        'Networking' => 'fa-user-plus',
        'Booths'     => 'fa-th-large',
        'Account'    => 'fa-lock',
    ];

    private static ?array $cache = null;

    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $items  = [];
        $userId = (int) (session('user_id') ?: session('attendee_id'));

        if ($userId <= 0) {
            return self::$cache = [];
        }

        try {
            $db = db_connect();

            if ($db->tableExists('tbl_attendee_connections')) {
                $requests = $db->table('tbl_attendee_connections c')
                    ->select('c.id, c.created_at, a.firstname, a.lastname')
                    ->join('tbl_attendees a', 'a.attendee_id = c.requester_id', 'left')
                    ->where('c.receiver_id', $userId)
                    ->where('c.status', 'pending')
                    ->orderBy('c.created_at', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($requests as $request) {
                    $name    = trim(($request['firstname'] ?? '') . ' ' . ($request['lastname'] ?? '')) ?: 'An attendee';
                    $items[] = self::item('conn-' . (int) $request['id'], 'Networking', true, 'Connection request', $name . ' wants to connect with you.', $request['created_at'] ?? null, (int) $request['id']);
                }
            }

            if ($db->tableExists('tbl_admin_portal_messages') && $db->tableExists('tbl_admin_portal_message_deliveries')) {
                $messages = $db->table('tbl_admin_portal_message_deliveries d')
                    ->select('d.delivery_id, d.read_at, d.created_at, m.subject, m.body')
                    ->join('tbl_admin_portal_messages m', 'm.admin_message_id = d.admin_message_id', 'inner')
                    ->where('d.user_id', $userId)
                    ->orderBy('d.delivery_id', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getResultArray();

                foreach ($messages as $message) {
                    $body    = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($message['body'] ?? ''))) ?? '');
                    $items[] = self::item('msg-' . (int) $message['delivery_id'], 'Messages', empty($message['read_at']), (string) ($message['subject'] ?: 'Message from EPR Global'), $body, $message['created_at'] ?? null);
                }
            }

            // Replies from a Country Manager that have not been opened yet, newest per community.
            if ($db->tableExists('tbl_country_communities') && $db->tableExists('tbl_country_community_messages')) {
                $replies = $db->table('tbl_country_community_messages m')
                    ->select('m.community_message_id, m.community_id, m.message, m.created_at, c.country')
                    ->join('tbl_country_communities c', 'c.community_id = m.community_id', 'inner')
                    ->where('m.attendee_id', $userId)
                    ->where('m.sender_type', 'admin')
                    ->where('m.read_at IS NULL', null, false)
                    ->where('c.is_active', 1)
                    ->orderBy('m.community_message_id', 'DESC')
                    ->limit(50)
                    ->get()
                    ->getResultArray();

                $seen = [];
                foreach ($replies as $reply) {
                    if (isset($seen[$reply['community_id']])) {
                        continue;
                    }
                    $seen[$reply['community_id']] = true;
                    $body    = trim(preg_replace('/\s+/', ' ', (string) $reply['message']) ?? '');
                    $items[] = self::item('community-' . (int) $reply['community_message_id'], 'Messages', true, 'Reply from your ' . $reply['country'] . ' Country Manager', $body . ' — open Communities in the Networking Center to reply.', $reply['created_at'] ?? null);
                }
            }
        } catch (\Throwable $e) {
            log_message('error', '[MobileNotifications] ' . $e->getMessage());
        }

        return self::$cache = $items;
    }

    public static function unreadCount(): int
    {
        return count(array_filter(self::all(), static fn (array $item): bool => $item['unread']));
    }

    private static function item(string $id, string $category, bool $unread, string $title, string $body, ?string $createdAt, ?int $connectionId = null): array
    {
        $timestamp = $createdAt ? strtotime($createdAt) : false;

        return [
            'id'            => $id,
            'cat'           => $category,
            'icon'          => self::ICONS[$category] ?? 'fa-bell',
            'unread'        => $unread,
            'title'         => $title,
            'body'          => $body,
            'connection_id' => $connectionId,
            'group'         => $timestamp && date('Y-m-d', $timestamp) === date('Y-m-d') ? 'TODAY' : 'EARLIER',
            'time'          => self::timeLabel($timestamp ?: null),
        ];
    }

    private static function timeLabel(?int $timestamp): string
    {
        if (! $timestamp) {
            return '';
        }

        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        }
        if ($diff < 3600) {
            return (int) floor($diff / 60) . ' min ago';
        }
        if ($diff < 86400 && date('Y-m-d', $timestamp) === date('Y-m-d')) {
            $hours = (int) floor($diff / 3600);

            return $hours . ' hr' . ($hours === 1 ? '' : 's') . ' ago';
        }

        return date('j M Y', $timestamp);
    }
}
