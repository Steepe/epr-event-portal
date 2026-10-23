<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tiers = [];
if (!empty($sponsors)) {
    foreach ($sponsors as $sponsor) {
        $tier = $sponsor['tier'] ?? 'Partner';
        $tiers[$tier][] = $sponsor;
    }
}

$tierSettings = [
    'platinum' => ['label' => 'PLATINUM', 'color' => '#ffffff', 'width' => '220px', 'height' => '130px', 'glow' => '0 0 30px rgba(255,255,255,.45)'],
    'gold' => ['label' => 'GOLD', 'color' => '#EFB11E', 'width' => '180px', 'height' => '108px', 'glow' => '0 0 26px rgba(239,177,30,.45)'],
    'silver' => ['label' => 'SILVER', 'color' => '#c8c8c8', 'width' => '150px', 'height' => '90px', 'glow' => '0 0 22px rgba(200,200,200,.35)'],
];
$tierOrder = ['platinum' => 1, 'gold' => 2, 'silver' => 3];
uksort($tiers, static function ($a, $b) use ($tierOrder) {
    $aKey = strtolower(trim((string) $a));
    $bKey = strtolower(trim((string) $b));
    return ($tierOrder[$aKey] ?? 99) <=> ($tierOrder[$bKey] ?? 99) ?: strcmp($aKey, $bKey);
});
?>

<main class="rc-page rc-page-center" data-screen-label="Sponsors">
    <div class="rc-container" style="max-width:1120px;gap:40px">
        <div class="rc-title-stack">
            <span class="rc-kicker">Accelerate 2026</span>
            <span class="rc-page-title">Sponsors</span>
            <span class="rc-page-sub">The partners powering Accelerate 2026.</span>
        </div>

        <?php if (!empty($tiers)): ?>
            <?php foreach ($tiers as $tier => $items): ?>
                <?php
                $key = strtolower(trim((string) $tier));
                $setting = $tierSettings[$key] ?? [
                    'label' => strtoupper((string) $tier),
                    'color' => '#ff78d1',
                    'width' => '165px',
                    'height' => '96px',
                    'glow' => '0 0 24px rgba(216,25,142,.35)',
                ];
                ?>
                <section class="rc-sponsor-tier">
                    <div class="rc-sponsor-label">
                        <span class="rc-mono" style="font-size:12px;letter-spacing:.22em;color:<?php echo esc($setting['color']); ?>"><?php echo esc($setting['label']); ?></span>
                        <span class="rc-mono" style="font-size:11px;color:rgba(255,255,255,.5)"><?php echo count($items); ?> PARTNERS</span>
                    </div>
                    <div class="rc-sponsor-logos">
                        <?php foreach ($items as $sponsor): ?>
                            <?php
                            $logoFile = $sponsor['logo'] ?? '';
                            $logoPath = 'uploads/sponsors/' . $logoFile;
                            if (!empty($logoFile) && file_exists(FCPATH . 'uploads/sponsors_logo/' . $logoFile)) {
                                $logoPath = 'uploads/sponsors_logo/' . $logoFile;
                            }
                            $logo = base_url($logoPath);
                            $website = !empty($sponsor['website']) ? $sponsor['website'] : null;
                            ?>
                            <a href="<?php echo !empty($sponsor['website']) ? esc($sponsor['website']) : '#'; ?>"
                               target="<?php echo $website ? '_blank' : '_self'; ?>"
                               rel="noopener"
                               title="<?php echo esc($sponsor['name']); ?>"
                               class="rc-sponsor-tile"
                               style="width:<?php echo esc($setting['width']); ?>;height:<?php echo esc($setting['height']); ?>;box-shadow:0 0 0 3px #bd159e, <?php echo esc($setting['glow']); ?>">
                                <img src="<?php echo $logo; ?>"
                                     alt="<?php echo esc($sponsor['name']); ?>"
                                     onerror="this.src='<?php echo asset_url('images/company-placeholder.png'); ?>';">
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="rc-empty">No sponsors found.</div>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
