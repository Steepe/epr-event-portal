<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class SessionReplayService
{
    public function isPublished(array $session): bool
    {
        if (trim((string) ($session['replay_bunny_embed_url'] ?? '')) === ''
            || (int) ($session['replay_product_id'] ?? 0) < 1) {
            return false;
        }

        $date = (string) ($session['event_date'] ?? '');
        $time = (string) ($session['end_time'] ?? '');
        if ($date === '' || $time === '') {
            return false;
        }

        try {
            $end = new DateTimeImmutable($date . ' ' . $time, new DateTimeZone('UTC'));
            return $end <= new DateTimeImmutable('now', new DateTimeZone('UTC'));
        } catch (\Exception) {
            return false;
        }
    }

    public function state(array $session, int $userId): array
    {
        if (! $this->isPublished($session)) {
            return ['published' => false, 'url' => '', 'product_id' => 0];
        }

        $productId = (int) $session['replay_product_id'];
        $store = new EmergenceStoreService();
        $hasAccess = $userId > 0 && $store->ready() && $store->hasAccess($userId, $productId);

        return [
            'published' => true,
            'url' => $hasAccess ? trim((string) $session['replay_bunny_embed_url']) : '',
            'product_id' => $productId,
        ];
    }
}
