<?php


namespace App\Traits;

trait AttendeeTrait
{
    /**
     * Load the signed-in attendee without making a second HTTP request to this server.
     */
    protected function getAttendeeData(): ?array
    {
        $userId = (int) (session('user_id') ?: session('attendee_id'));
        if ($userId <= 0) return null;

        $attendee = db_connect()->table('tbl_users u')
            ->select('u.id, u.email, u.role, a.firstname, a.lastname, a.country, a.profile_picture, a.company, a.position')
            ->join('tbl_attendees a', 'a.attendee_id = u.id', 'left')
            ->where('u.id', $userId)
            ->where('u.deleted_at', null)
            ->orderBy('a.id', 'DESC')
            ->get(1)
            ->getRowArray();

        if ($attendee === null) return null;

        // 🖼️ Ensure profile picture fallback
        if (!empty($attendee['profile_picture'])) {
            $attendee['profile_picture_url'] = base_url('uploads/attendee_pictures/') . $attendee['profile_picture'];
        } else {
            $attendee['profile_picture_url'] = asset_url('images/user.png');
        }

        return $attendee;
    }

    /**
     * Get the attendee's recorded session IDs locally.
     */
    protected function getAttendeeSessions(): array
    {
        $userId = (int) (session('user_id') ?: session('attendee_id'));
        if ($userId <= 0) return [];

        $rows = db_connect()->table('tbl_attendee_sessions')
            ->select('session_id')
            ->where('user_id', $userId)
            ->get()
            ->getResultArray();

        return array_column($rows, 'session_id');
    }

    /**
     * Count unread direct messages locally.
     */
    protected function getUnreadMessagesCount(): int
    {
        $userId = (int) (session('user_id') ?: session('attendee_id'));
        if ($userId <= 0) return 0;

        return (int) db_connect()->table('tbl_messages')
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
    }

    /**
     * 🌍 Get attendee country (fallback to Nigeria)
     */
    protected function getAttendeeCountry(): string
    {
        $attendee = $this->getAttendeeData();
        return $attendee['country'] ?? 'Nigeria';
    }
}
