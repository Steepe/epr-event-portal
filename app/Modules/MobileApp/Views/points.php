<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/explore'), 'activeTab' => 'explore', 'title' => 'Earn Points', 'screenLabel' => 'Earn Points']) ?>

<div class="m-screen">
    <div class="m-head">
        <span class="m-kicker">Conference Rewards</span>
        <h1 class="m-title">Earn Points</h1>
        <span class="m-sub">Complete activities and keep your conference momentum visible.</span>
    </div>

    <?php foreach (($point_guides ?? []) as $guide): ?>
        <?php $image = !empty($guide['image']) ? mobile_asset_url('images/' . rawurlencode($guide['image'])) : mobile_asset_url('images/earnpoints-icon.png'); ?>
        <article class="m-card m-card--20 m-activity">
            <span class="m-activity__icon"><img src="<?= esc($image, 'attr') ?>" alt="" onerror="this.onerror=null;this.src='<?= mobile_asset_url('images/earnpoints-icon.png') ?>'"></span>
            <div class="m-activity__copy"><b><?= esc($guide['activity']) ?></b><span>Reward value: <?= esc($guide['points']) ?> points</span></div>
            <span class="m-activity__earn">Earn <?= esc($guide['points']) ?></span>
        </article>
    <?php endforeach; ?>

    <?php if (empty($point_guides)): ?>
        <div class="m-empty">No activities available yet. Check back soon!</div>
    <?php endif; ?>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
