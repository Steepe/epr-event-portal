<?= module_view('MobileApp', 'includes/header', ['shell' => 'plain', 'back' => false, 'activeTab' => '', 'title' => 'Start', 'screenLabel' => 'Landing']) ?>
<?php
$firstName      = trim((string) session('firstname'));
$conferenceYear = (string) ($conference['year'] ?? date('Y'));
$pastIcons      = [];
$archiveFallbacks = [
    mobile_asset_url('images/innovate-icon.png'),
    mobile_asset_url('images/dominate-icon.png'),
    mobile_asset_url('images/unleash-icon.png'),
];
foreach (array_reverse($pastConferences ?? []) as $item) {
    $iconFile = basename((string) ($item['icon'] ?? ''));
    if ($iconFile !== '' && is_file(FCPATH . 'uploads/conferences/' . $iconFile)) {
        $pastIcons[] = base_url('uploads/conferences/' . rawurlencode($iconFile));
    }
}
foreach ($archiveFallbacks as $fallback) {
    if (count($pastIcons) >= 3) {
        break;
    }
    $pastIcons[] = $fallback;
}
?>

<div class="m-start">
    <div class="m-start__head">
        <span class="m-kicker">Welcome back<?= $firstName !== '' ? ', ' . esc($firstName) : '' ?></span>
        <h1 class="m-start__title">Where would you like to go?</h1>
    </div>

    <a class="m-start__hero" href="<?= site_url('mobile/home') ?>">
        <div class="m-start__brand">
            <img src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="<?= esc($conference['title'] ?? 'Accelerate', 'attr') ?>">
            <span class="m-start__year"><?= esc($conferenceYear) ?></span>
        </div>
        <div class="m-start__enter"><span>ENTER CONFERENCE</span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
        <span class="m-start__nub m-start__nub--bottom"></span>
    </a>

    <svg class="m-start__wire" viewBox="0 0 350 34" preserveAspectRatio="none" aria-hidden="true">
        <path d="M175 0 V17 H82 V34 M175 17 H268 V34" fill="none" stroke="rgba(239,177,30,.55)" stroke-width="1.2" stroke-dasharray="3 4" vector-effect="non-scaling-stroke"></path>
    </svg>

    <div class="m-start__grid">
        <a class="m-start__card" href="<?= site_url('mobile/past-conferences') ?>">
            <span class="m-start__nub m-start__nub--top"></span>
            <div class="m-start__visual m-start__icons">
                <?php foreach (array_slice($pastIcons, 0, 3) as $index => $icon): ?>
                    <img class="<?= $index === 2 ? 'is-light' : '' ?>" src="<?= esc($icon, 'attr') ?>" alt="" onerror="this.onerror=null;this.src='<?= esc($archiveFallbacks[$index], 'attr') ?>'">
                <?php endforeach; ?>
            </div>
            <div class="m-start__foot"><span>Past Conferences</span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
        </a>
        <a class="m-start__card" href="<?= site_url('mobile/webinars') ?>">
            <span class="m-start__nub m-start__nub--top"></span>
            <div class="m-start__visual"><img class="m-start__webinar" src="<?= mobile_asset_url('images/webinars.png') ?>" alt="EPR Global Webinars"></div>
            <div class="m-start__foot"><span>Webinars</span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
        </a>
    </div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
