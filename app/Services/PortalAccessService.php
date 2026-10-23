<?php

namespace App\Services;

class PortalAccessService
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function requiresPayment(): bool
    {
        return $this->envBoolean('portal.accessRequiresPayment', false);
    }

    public function checkoutEnabled(): bool
    {
        return $this->envBoolean('portal.checkoutEnabled', false);
    }

    public function liveConference(): ?array
    {
        return $this->db->table('tbl_conferences')
            ->where('status', 'live')
            ->orderBy('conference_id', 'DESC')
            ->get()
            ->getRowArray();
    }

    public function liveConferenceId(): ?int
    {
        $conference = $this->liveConference();

        return $conference ? (int) $conference['conference_id'] : null;
    }

    public function attendeeHasPaid(int $userId, int $attendeeRowId = 0, ?int $conferenceId = null): bool
    {
        $conferenceId = $conferenceId ?: $this->liveConferenceId();
        if (! $conferenceId) {
            return false;
        }

        $attendeeIds = array_values(array_unique(array_filter([$userId, $attendeeRowId])));
        if ($attendeeIds === []) {
            return false;
        }

        return $this->db->table('tbl_attendee_payments')
            ->where('conference_id', $conferenceId)
            ->whereIn('attendee_id', $attendeeIds)
            ->countAllResults() > 0;
    }

    public function ticketPriceForCountry(?int $conferenceId, string $country): array
    {
        $ticket = $conferenceId
            ? $this->db->table('tbl_ticket_prices')->where('conference_id', $conferenceId)->get()->getRowArray()
            : null;

        if (! $ticket) {
            return ['amount' => null, 'currency' => 'USD'];
        }

        return match (strtolower($country)) {
            'nigeria' => ['amount' => (float) $ticket['amount_naira'], 'currency' => 'NGN'],
            'kenya' => ['amount' => (float) $ticket['amount_shillings'], 'currency' => 'KES'],
            'south africa' => ['amount' => (float) $ticket['amount_rands'], 'currency' => 'ZAR'],
            default => ['amount' => (float) $ticket['amount_dollar'], 'currency' => 'USD'],
        };
    }

    private function envBoolean(string $key, bool $default): bool
    {
        $value = env($key);
        if ($value === null || $value === '') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
