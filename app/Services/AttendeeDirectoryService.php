<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;

final class AttendeeDirectoryService
{
    private const PAGE_SIZE = 60;

    /** @return array{attendees: array, total: int, all_count: int, online_count: int, page: int, per_page: int, has_more: bool} */
    public function page(int $currentUserId, string $search = '', string $letter = '', bool $onlineOnly = false, int $page = 1): array
    {
        $db = db_connect();
        $search = mb_substr(trim($search), 0, 100);
        $letter = preg_match('/^[A-Za-z]$/', $letter) ? $letter : '';
        $page = max(1, $page);
        $presence = $db->tableExists('tbl_portal_user_presence') ? 'portal'
            : ($db->tableExists('tbl_user_status') ? 'legacy' : 'none');

        $total = $this->query($db, $presence, $search, $letter, $onlineOnly)->countAllResults();
        $page = min($page, max(1, (int) ceil($total / self::PAGE_SIZE)));
        $allCount = $db->table('tbl_attendees a')
            ->join('tbl_users u', 'u.id = a.attendee_id')
            ->where('u.deleted_at', null)
            ->countAllResults();
        $onlineCount = $presence === 'portal'
            ? $db->table('tbl_portal_user_presence p')
                ->join('tbl_attendees a', 'a.attendee_id = p.user_id')
                ->join('tbl_users u', 'u.id = p.user_id')
                ->where('u.deleted_at', null)
                ->where('p.last_seen_at >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)', null, false)
                ->countAllResults()
            : ($presence === 'legacy'
                ? $db->table('tbl_user_status s')
                    ->join('tbl_attendees a', 'a.attendee_id = s.user_id')
                    ->join('tbl_users u', 'u.id = s.user_id')
                    ->where('u.deleted_at', null)
                    ->where('s.is_online', 1)
                    ->where('s.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)', null, false)
                    ->countAllResults()
                : 0);

        $offset = ($page - 1) * self::PAGE_SIZE;
        $attendees = $this->query($db, $presence, $search, $letter, $onlineOnly)
            ->select('a.id, a.attendee_id, a.firstname, a.lastname, a.company, a.position, a.country, a.state, a.city, a.profile_picture, a.facebook, a.twitter, a.instagram, a.linkedin, u.email')
            ->orderBy("LOWER(TRIM(COALESCE(a.firstname, '')))", 'ASC', false)
            ->orderBy("LOWER(TRIM(COALESCE(a.lastname, '')))", 'ASC', false)
            ->orderBy('a.attendee_id', 'ASC')
            ->limit(self::PAGE_SIZE, $offset)
            ->get()->getResultArray();

        if (! $onlineOnly) {
            $this->applyRedisPresence($attendees);
        }
        $this->applyConnections($db, $attendees, $currentUserId);

        return [
            'attendees' => $attendees,
            'total' => $total,
            'all_count' => $allCount,
            'online_count' => $onlineCount,
            'page' => $page,
            'per_page' => self::PAGE_SIZE,
            'has_more' => $offset + count($attendees) < $total,
        ];
    }

    private function query(BaseConnection $db, string $presence, string $search, string $letter, bool $onlineOnly): BaseBuilder
    {
        $builder = $db->table('tbl_attendees a')
            ->join('tbl_users u', 'u.id = a.attendee_id')
            ->where('u.deleted_at', null);

        if ($presence === 'portal') {
            $builder->join('tbl_portal_user_presence p', 'p.user_id = a.attendee_id', 'left')
                ->select('p.last_seen_at AS last_seen, CASE WHEN p.last_seen_at >= DATE_SUB(NOW(), INTERVAL 2 MINUTE) THEN 1 ELSE 0 END AS is_online', false);
            if ($onlineOnly) {
                $builder->where('p.last_seen_at >= DATE_SUB(NOW(), INTERVAL 2 MINUTE)', null, false);
            }
        } elseif ($presence === 'legacy') {
            $builder->join('tbl_user_status s', 's.user_id = a.attendee_id', 'left')
                ->select('s.last_seen, CASE WHEN s.is_online = 1 AND s.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) THEN 1 ELSE 0 END AS is_online', false);
            if ($onlineOnly) {
                $builder->where('s.is_online', 1)->where('s.last_seen >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)', null, false);
            }
        } else {
            $builder->select('NULL AS last_seen, 0 AS is_online', false);
            if ($onlineOnly) {
                $builder->where('1 = 0', null, false);
            }
        }

        if ($search !== '') {
            $builder->groupStart()
                ->like('a.firstname', $search)
                ->orLike('a.lastname', $search)
                ->orLike("CONCAT(a.firstname, ' ', a.lastname)", $search, 'both')
                ->orLike('a.country', $search)
                ->orLike('a.city', $search)
                ->orLike('a.company', $search)
                ->orLike('a.position', $search)
                ->groupEnd();
        }
        if ($letter !== '') {
            $builder->like('a.firstname', $letter, 'after');
        }

        return $builder;
    }

    private function applyRedisPresence(array &$attendees): void
    {
        if ($attendees === [] || ! class_exists(\Redis::class)) {
            return;
        }
        try {
            $redis = new \Redis();
            $redis->connect(env('redis.host') ?: '127.0.0.1', (int) (env('redis.port') ?: 6379), 0.2);
            $statuses = $redis->mGet(array_map(static fn (array $row): string => 'user:' . (int) $row['attendee_id'] . ':online', $attendees));
            $redis->close();
            foreach ($attendees as $index => &$attendee) {
                if (($statuses[$index] ?? false) !== false) {
                    $attendee['is_online'] = (string) $statuses[$index] === '1' ? 1 : 0;
                }
            }
            unset($attendee);
        } catch (\Throwable $e) {
            log_message('debug', '[AttendeeDirectory] Redis presence unavailable: ' . $e->getMessage());
        }
    }

    private function applyConnections(BaseConnection $db, array &$attendees, int $currentUserId): void
    {
        foreach ($attendees as &$attendee) {
            $attendee['is_connected'] = 0;
            $attendee['connection_id'] = null;
            $attendee['connection_status'] = null;
            $attendee['connection_direction'] = null;
        }
        unset($attendee);
        if ($currentUserId <= 0 || $attendees === [] || ! $db->tableExists('tbl_attendee_connections')) {
            return;
        }

        $ids = array_values(array_unique(array_map(static fn (array $row): int => (int) $row['attendee_id'], $attendees)));
        $connections = $db->table('tbl_attendee_connections')
            ->groupStart()->where('requester_id', $currentUserId)->whereIn('receiver_id', $ids)->groupEnd()
            ->orGroupStart()->where('receiver_id', $currentUserId)->whereIn('requester_id', $ids)->groupEnd()
            ->get()->getResultArray();
        $byAttendee = [];
        foreach ($connections as $connection) {
            $otherId = (int) $connection['requester_id'] === $currentUserId
                ? (int) $connection['receiver_id'] : (int) $connection['requester_id'];
            $byAttendee[$otherId] = $connection;
        }
        foreach ($attendees as &$attendee) {
            $connection = $byAttendee[(int) $attendee['attendee_id']] ?? null;
            if ($connection === null) continue;
            $attendee['connection_id'] = (int) $connection['id'];
            $attendee['connection_status'] = $connection['status'];
            $attendee['connection_direction'] = (int) $connection['requester_id'] === $currentUserId ? 'sent' : 'received';
            $attendee['is_connected'] = $connection['status'] === 'accepted' ? 1 : 0;
        }
        unset($attendee);
    }
}
