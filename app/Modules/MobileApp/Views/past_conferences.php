<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/welcome'), 'activeTab' => 'explore', 'title' => 'Past Conferences', 'screenLabel' => 'Past Conferences']) ?>

<div class="m-past">
    <a class="m-backlink" href="<?= site_url('mobile/welcome') ?>" data-back="<?= site_url('mobile/welcome') ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK</a>
    <h1 class="m-title">Past Conferences</h1>

    <?php if (empty($conferences)): ?>
        <div class="m-empty" style="margin-top:30px">No past conferences are available at this time.</div>
    <?php else: ?>
        <div class="m-past__grid">
            <?php foreach ($conferences as $index => $item): ?>
                <?php
                $icon = !empty($item['icon'])
                    ? base_url('uploads/conferences/' . rawurlencode(basename((string) $item['icon'])))
                    : mobile_asset_url('images/eventslogo.png');
                $year = (int) ($item['year'] ?? 0);
                if ($year === 0 && !empty($item['start_date'])) {
                    $year = (int) date('Y', strtotime($item['start_date']));
                }
                ?>
                <a class="m-past__item" href="<?= site_url('mobile/pastConference/' . (int) $item['conference_id']) ?>">
                    <span class="m-past__icon <?= $index === 0 ? 'is-light' : '' ?>"><img src="<?= esc($icon, 'attr') ?>" alt="<?= esc($item['title'] ?? 'Past conference', 'attr') ?>" loading="lazy"></span>
                    <span class="m-past__name"><?= esc($item['title'] ?? 'Past Conference') ?></span>
                    <span class="m-past__year"><?= $year ?: 'ARCHIVE' ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="m-past__foot">
        <span class="m-kicker">Past Conferences · Emergence <?= date('Y') ?></span>
        <img src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="">
    </div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
