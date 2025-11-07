<?php


namespace App\Traits;

use App\Services\UserService;
use App\Services\MessageService;

trait AttendeeTrait
{
    /**
     * ✅ Get full attendee details via API
     */
    protected function getAttendeeData(): ?array
    {
        $attendeeId = session('attendee_id');
        if (!$attendeeId) return null;

        $userService = new UserService();
        $attendee = $userService->getUser($attendeeId);

        // 🖼️ Ensure profile picture fallback
        if (!empty($attendee['profile_picture'])) {
            $attendee['profile_picture_url'] = base_url('uploads/attendee_pictures/') . $attendee['profile_picture'];
        } else {
            $attendee['profile_picture_url'] = asset_url('images/user.png');
        }

        return $attendee;
    }

    /**
     * 📅 Get sessions the attendee added to their agenda
     */
    protected function getAttendeeSessions(): array
    {
        $attendeeId = session('attendee_id');
        if (!$attendeeId) return [];

        $userService = new UserService();
        return $userService->getUserSessions($attendeeId);
    }

    /**
     * 📩 Get unread message count via API
     */
    protected function getUnreadMessagesCount(): int
    {
        $attendeeId = session('attendee_id');
        if (!$attendeeId) return 0;

        $messageService = new MessageService();
        return $messageService->getUnreadCount($attendeeId);
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
