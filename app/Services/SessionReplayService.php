<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class SessionReplayService
{
    private array $accessCache = [];
    private ?bool $storeReady = null;

    public static function normaliseBunnyEmbedUrl(string $url): ?string
    {
        $url = trim($url);
        if (! preg_match('~\Ahttps://iframe\.mediadelivery\.net/(?:play|embed)/([0-9]+)/([a-f0-9-]{16,64})(\?[^#\s]*)?\z~i', $url, $matches)) {
            return null;
        }

        return 'https://iframe.mediadelivery.net/embed/' . $matches[1] . '/' . strtolower($matches[2]) . ($matches[3] ?? '');
    }

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
        $cacheKey = $userId . ':' . $productId;
        if (! array_key_exists($cacheKey, $this->accessCache)) {
            $store = new EmergenceStoreService();
            $this->storeReady ??= $store->ready();
            $this->accessCache[$cacheKey] = $userId > 0 && $this->storeReady && $store->hasReplayAccess($userId, $productId);
        }
        $hasAccess = $this->accessCache[$cacheKey];

        return [
            'published' => true,
            'url' => $hasAccess ? trim((string) $session['replay_bunny_embed_url']) : '',
            'product_id' => $productId,
        ];
    }
}
