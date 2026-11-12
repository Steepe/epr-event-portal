<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => false, 'activeTab' => 'explore', 'title' => 'Explore', 'screenLabel' => 'Explore']) ?>
<?php
$isPremium = (int) ($premium ?? 0) === 1;
$explore   = [
    ['fa-bell', 'Notifications', 'mobile/notifications'],
    ['fa-star', 'Earn Points', 'mobile/points'],
    ['fa-eye', 'Envision', 'mobile/envision'],
    ['fa-history', 'Past Conferences', 'mobile/past-conferences'],
    ['fa-video-camera', 'Webinars', 'mobile/webinars'],
    ['fa-diamond', 'Sponsors', 'mobile/sponsors'],
    ['fa-building', 'Exhibitions', 'mobile/exhibitors'],
    ['fa-unlock-alt', $isPremium ? 'Premium Access · Active' : 'Upgrade to Premium', 'mobile/upgrade'],
];
?>

<div class="m-screen">
    <div class="m-head">
        <span class="m-kicker">Accelerate 2026</span>
        <h1 class="m-title">Explore</h1>
        <span class="m-sub">Everything else in the conference, in one place.</span>
    </div>

    <div class="m-card m-card--clip">
        <?php foreach ($explore as [$icon, $label, $route]): ?>
            <a class="m-me__link" href="<?= site_url($route) ?>"><i class="fa <?= $icon ?>" aria-hidden="true"></i><span><?= esc($label) ?></span><i class="fa fa-angle-right" aria-hidden="true"></i></a>
        <?php endforeach; ?>
    </div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
