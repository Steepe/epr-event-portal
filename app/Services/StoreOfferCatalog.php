<?php

namespace App\Services;

/**
 * Presentation catalogue for the two curated Emergence offers.
 *
 * Products, prices and access remain managed in tbl_store_products. This
 * class only matches those products to their approved storefront designs.
 */
class StoreOfferCatalog
{
    public static function offers(array $products, array $entitledProductIds = []): array
    {
        $replay = self::findReplayProduct($products);
        $blueprint = self::findBlueprintProduct($products, $replay);

        return [
            self::offer('blueprint', $blueprint, $entitledProductIds),
            self::offer('replay', $replay, $entitledProductIds),
        ];
    }

    private static function offer(string $type, ?array $product, array $entitledProductIds): array
    {
        $isReplay = $type === 'replay';
        $fallbackAmount = $isReplay ? 9.99 : 19.99;
        $currency = strtoupper((string) ($product['currency'] ?? 'USD'));
        $amount = $product ? (float) ($product['price_amount'] ?? $fallbackAmount) : $fallbackAmount;
        $price = ($currency === 'USD' ? '$' : $currency . ' ') . number_format($amount, 2);
        $productId = (int) ($product['product_id'] ?? 0);
        $owned = $productId > 0 && in_array($productId, $entitledProductIds, true);
        $accessUrl = '';

        if ($owned && ($product['access_type'] ?? '') === 'replays') {
            $accessUrl = site_url('attendees/store/replays/' . $productId);
        } elseif ($owned && ($product['access_type'] ?? '') === 'external' && ! empty($product['access_url'])) {
            $accessUrl = (string) $product['access_url'];
        }

        $common = [
            'type' => $type,
            'product' => $product,
            'product_id' => $productId,
            'price' => $price,
            'owned' => $owned,
            'access_url' => $accessUrl,
            'available' => $product !== null,
        ];

        if ($isReplay) {
            return $common + [
                'eyebrow' => 'CONFERENCE REPLAY PASS',
                'title' => 'Emergence 2026 Replay',
                'description' => "Keep today's insights working for you long after the conference ends.",
                'price_label' => "TODAY'S REPLAY PRICE",
                'full_price' => '$19.99',
                'offer_label' => 'ONE DAY ONLY · NOV 21',
                'button_label' => 'GET THE REPLAY',
                'benefit_heading' => 'WHY GET THE REPLAY',
                'benefits' => [
                    ['Never miss a session', 'Catch anything you missed live or had to leave early.'],
                    ['Learn at your own pace', 'Pause, rewind and take notes when it suits you.'],
                    ['Spot what you missed', 'Notice insights that passed you by the first time.'],
                    ['Finish your workbook', 'Complete every session check with time to reflect.'],
                    ['Revisit when stuck', 'Return to the right session when progress stalls.'],
                    ['Fuel your 2027 goals', 'Rewatch at key moments, like planning and reviews.'],
                    ['Explore every Track Lab', 'Learn from all four tracks, not just the one you joined.'],
                    ['Six months of access', 'Keep coming back as your plan moves forward.'],
                ],
            ];
        }

        return $common + [
            'eyebrow' => 'LIVE GROUP COACHING + BLUEPRINT',
            'title' => 'Execution Blueprint Coaching Session',
            'description' => 'Build your full 2027 plan in a live group session.',
            'price_label' => 'EMERGENCE CONFERENCE PRICE',
            'full_price' => '$29.00',
            'offer_label' => 'ONE DAY ONLY · NOV 21',
            'button_label' => 'RESERVE YOUR SEAT',
            'benefit_heading' => "WHAT YOU'LL WALK AWAY WITH",
            'benefits' => [
                ['Live group coaching', 'Build your 2027 plan live in a coach-led group.'],
                ['Builds on your workbook', 'Your baseline scores and gaps become the starting point.'],
                ['Identity, vision & skills', "Clarify who you're becoming and the skills to build."],
                ['Marketplace alignment', 'Match your goal to real demand in your marketplace.'],
                ['Your top goals for 2027', 'Choose up to three goals and map each with SYSTEM.'],
                ['A sequenced 90-day plan', 'Milestones, 30-day priorities and weekly routines.'],
                ['Your written Blueprint', 'Your full plan, trackers and 7, 30 and 90-day reviews.'],
                ['6 months of replays', 'Rewatch every keynote, panel and track lab at your pace.'],
            ],
        ];
    }

    private static function findReplayProduct(array $products): ?array
    {
        foreach ($products as $product) {
            $haystack = strtolower((string) ($product['slug'] ?? '') . ' ' . (string) ($product['name'] ?? ''));
            if (($product['access_type'] ?? '') === 'replays' || str_contains($haystack, 'replay')) {
                return $product;
            }
        }

        return null;
    }

    private static function findBlueprintProduct(array $products, ?array $replay): ?array
    {
        foreach ($products as $product) {
            if ((int) ($product['product_id'] ?? 0) === (int) ($replay['product_id'] ?? 0)) {
                continue;
            }
            $haystack = strtolower((string) ($product['slug'] ?? '') . ' ' . (string) ($product['name'] ?? ''));
            if (($product['access_type'] ?? '') === 'coaching_interest'
                || str_contains($haystack, 'blueprint')
                || (str_contains($haystack, 'execution') && str_contains($haystack, 'coaching'))) {
                return $product;
            }
        }

        return null;
    }
}
