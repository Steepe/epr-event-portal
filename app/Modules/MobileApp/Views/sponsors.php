<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => true, 'activeTab' => 'lobby', 'title' => 'Sponsors']) ?>
<?php
$tiers = [];
foreach (($sponsors ?? []) as $sponsor) {
    $tier = trim((string) ($sponsor['tier'] ?? '')) ?: 'Partner';
    $tiers[$tier][] = $sponsor;
}

// Size and glow follow the design's three tiers; other tier names fall back to the nearest one.
$tierStyle = ['diamond' => 'platinum', 'platinum' => 'platinum', 'gold' => 'gold', 'silver' => 'silver'];
$tierOrder = ['diamond' => 0, 'platinum' => 1, 'gold' => 2, 'silver' => 3];
uksort($tiers, static function ($a, $b) use ($tierOrder) {
    $aKey = strtolower((string) $a);
    $bKey = strtolower((string) $b);

    return ($tierOrder[$aKey] ?? 99) <=> ($tierOrder[$bKey] ?? 99) ?: strcmp($aKey, $bKey);
});
?>

<div class="m-screen m-screen--28">
    <div class="m-head">
        <span class="m-kicker">Accelerate 2026</span>
        <h1 class="m-title">Sponsors</h1>
        <span class="m-sub">The partners powering Accelerate 2026.</span>
    </div>

    <?php foreach ($tiers as $tier => $items): ?>
        <section class="m-tier m-tier--<?= $tierStyle[strtolower((string) $tier)] ?? 'silver' ?>">
            <div class="m-tier__head">
                <span class="m-tier__name"><?= esc(strtoupper((string) $tier)) ?></span>
                <span class="m-tier__count"><?= count($items) ?> <?= count($items) === 1 ? 'PARTNER' : 'PARTNERS' ?></span>
            </div>
            <div class="m-tier__items">
                <?php foreach ($items as $sponsor): ?>
                    <?php
                    $logoFile = basename((string) ($sponsor['logo'] ?? ''));
                    $logo     = '';
                    if ($logoFile !== '') {
                        $logo = file_exists(FCPATH . 'uploads/sponsors_logo/' . $logoFile)
                            ? base_url('uploads/sponsors_logo/' . rawurlencode($logoFile))
                            : base_url('uploads/sponsors/' . rawurlencode($logoFile));
                    }
                    $link = trim((string) ($sponsor['website'] ?? ($sponsor['link'] ?? '')));
                    if ($link !== '' && ! preg_match('#^https?://#i', $link)) {
                        $link = 'https://' . $link;
                    }
                    ?>
                    <a class="m-sponsor" title="<?= esc($sponsor['name'], 'attr') ?>"<?= $link !== '' ? ' href="' . esc($link, 'attr') . '" target="_blank" rel="noopener"' : '' ?>>
                        <?php if ($logo !== ''): ?>
                            <img src="<?= esc($logo, 'attr') ?>" alt="<?= esc($sponsor['name'], 'attr') ?>" loading="lazy" onerror="this.replaceWith(document.createTextNode(this.alt))">
                        <?php else: ?>
                            <?= esc($sponsor['name']) ?>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <?php if (! $tiers): ?>
        <div class="m-empty">No sponsors are available yet.</div>
    <?php endif; ?>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
