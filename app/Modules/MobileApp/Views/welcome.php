<?= module_view('MobileApp', 'includes/header', ['shell' => 'plain', 'back' => false, 'activeTab' => '', 'title' => 'Start']) ?>
<?php
$firstName      = trim((string) session('firstname'));
$conferenceYear = (string) ($conference['year'] ?? date('Y'));
$isLive         = !empty($conference);
$pastIcons      = [];
foreach (array_reverse($pastConferences ?? []) as $item) {
    if (!empty($item['icon'])) {
        $pastIcons[] = base_url('uploads/conferences/' . rawurlencode(basename((string) $item['icon'])));
    }
}
if (count($pastIcons) < 3) {
    $pastIcons = [mobile_asset_url('images/innovate-icon.png'), mobile_asset_url('images/dominate-icon.png'), mobile_asset_url('images/unleash-icon.png')];
}
?>

<div class="m-start">
    <div class="m-start__head">
        <span class="m-kicker">Welcome back<?= $firstName !== '' ? ', ' . esc($firstName) : '' ?></span>
        <h1 class="m-start__title">Where would you like to go?</h1>
    </div>

    <a class="m-start__hero" href="<?= site_url('mobile/home') ?>">
        <div class="m-start__row">
            <span class="m-start__module">MODULE 02</span>
            <?php if ($isLive): ?><span class="m-start__live">LIVE NOW</span><?php endif; ?>
        </div>
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
            <span class="m-start__module m-start__module--dim">MODULE 01</span>
            <div class="m-start__icons">
                <?php foreach (array_slice($pastIcons, 0, 3) as $index => $icon): ?>
                    <img class="<?= $index === 2 ? 'is-light' : '' ?>" src="<?= esc($icon, 'attr') ?>" alt="">
                <?php endforeach; ?>
            </div>
            <div class="m-start__foot"><span>Past Conferences</span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
        </a>
        <a class="m-start__card" href="<?= site_url('mobile/webinars') ?>">
            <span class="m-start__nub m-start__nub--top"></span>
            <span class="m-start__module m-start__module--dim">MODULE 03</span>
            <img class="m-start__webinar" src="<?= mobile_asset_url('images/webinars.png') ?>" alt="EPR Global Webinars">
            <div class="m-start__foot"><span>Webinars</span><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
        </a>
    </div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
