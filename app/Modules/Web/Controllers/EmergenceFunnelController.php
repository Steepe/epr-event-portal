<?php

namespace App\Modules\Web\Controllers;

use App\Controllers\BaseController;

class EmergenceFunnelController extends BaseController
{
    public function index(): string
    {
        $checkoutUrl = env('funnel.emergence.checkoutUrl');
        if (! $checkoutUrl || str_contains($checkoutUrl, 'your-checkout-url')) {
            $checkoutUrl = '/emergence/checkout';
        }

        $portalUrl = $this->portalUrl();

        return module_view('Web', 'emergence_funnel', [
            'apiEndpoint' => '/emergence/funnel/register',
            'checkoutUrl' => $checkoutUrl,
            'portalUrl' => $portalUrl,
            'upsellPrice' => env('funnel.emergence.upsellPrice') ?: '$50',
            'mailchimpTags' => implode(',', $this->mailchimpTags()),
            'countries' => $this->countries(),
            'showUpsell' => $this->envBoolean('funnel.emergence.showUpsell', false),
            'calendarEvent' => $this->calendarEvent($portalUrl),
        ]);
    }

    private function portalUrl(): string
    {
        $url = trim((string) env('funnel.emergence.portalUrl'));
        if ($url === '' || str_contains($url, 'your-portal-url')) {
            return site_url('attendees/login');
        }

        if (! preg_match('#^https?://#i', $url)) {
            return site_url(ltrim($url, '/'));
        }

        return $url;
    }

    private function calendarEvent(string $portalUrl): array
    {
        $timezone = trim((string) env('funnel.emergence.calendarTimezone')) ?: 'Africa/Lagos';
        $conference = $this->liveConference();
        $startSource = env('funnel.emergence.calendarStart') ?: ($conference['start_date'] ?? null);
        $endSource = env('funnel.emergence.calendarEnd') ?: ($conference['end_date'] ?? null);
        $allDay = $this->envBoolean('funnel.emergence.calendarAllDay', $this->isDateOnly($startSource));
        $start = $this->calendarDate($startSource, $timezone);
        $end = $this->calendarDate($endSource, $timezone);

        if ($start && ! $end) {
            $end = $start->modify($allDay ? '+1 day' : '+1 hour');
        }

        $title = trim((string) env('funnel.emergence.calendarTitle'))
            ?: trim((string) ($conference['title'] ?? 'EPR Global Conference'));
        $description = trim((string) env('funnel.emergence.calendarDescription'))
            ?: 'Your registration is confirmed. Access the portal: ' . $portalUrl;

        return [
            'title' => $title,
            'description' => $description,
            'location' => trim((string) env('funnel.emergence.calendarLocation')) ?: 'Online',
            'timezone' => $timezone,
            'start' => $start?->format(DATE_ATOM),
            'end' => $end?->format(DATE_ATOM),
            'allDay' => $allDay,
            'portalUrl' => $portalUrl,
        ];
    }

    private function liveConference(): ?array
    {
        try {
            return db_connect()
                ->table('tbl_conferences')
                ->where('status', 'live')
                ->orderBy('conference_id', 'DESC')
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'Emergence funnel live conference fetch failed: ' . $e->getMessage());

            return null;
        }
    }

    private function calendarDate(?string $value, string $timezone): ?\DateTimeImmutable
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone($timezone));
        } catch (\Throwable $e) {
            log_message('error', 'Emergence funnel calendar date parse failed: ' . $e->getMessage());

            return null;
        }
    }

    private function isDateOnly(mixed $value): bool
    {
        return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($value)) === 1;
    }

    private function countries(): array
    {
        try {
            return db_connect()
                ->table('tbl_countries')
                ->select('country_name')
                ->orderBy('country_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', 'Emergence funnel country fetch failed: ' . $e->getMessage());

            return [];
        }
    }

    private function mailchimpTags(): array
    {
        $tags = array_merge(
            $this->splitTags(env('funnel.emergence.mailchimpTags') ?: 'emergence-registrant'),
            $this->splitTags(env('funnel.emergence.conferenceTag') ?: 'conference-2026')
        );

        return array_values(array_unique(array_filter($tags)));
    }

    private function splitTags(string $tags): array
    {
        return array_map('trim', explode(',', $tags));
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
