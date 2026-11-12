<?= module_view('MobileApp', 'includes/header', ['shell' => 'plain', 'back' => site_url('mobile/welcome'), 'activeTab' => '', 'title' => 'Welcome', 'screenLabel' => 'Home']) ?>
<?php
$conferenceTitle = trim((string) ($conference['title'] ?? 'Accelerate')) ?: 'Accelerate';
$conferenceYear  = (string) ($conference['year'] ?? date('Y'));
$welcomeVideoUrl = (string) ($welcome_video_url ?? '');
?>

<div class="m-welcome">
    <div class="m-welcome__head">
        <span class="m-kicker">Emergence Conference · <?= esc($conferenceYear) ?></span>
        <h1 class="m-welcome__title">Welcome to <?= esc($conferenceTitle) ?></h1>
    </div>

    <div class="m-welcome__video" data-video-src="<?= esc($welcomeVideoUrl, 'attr') ?>" data-video-title="Welcome video">
        <?php if ($welcomeVideoUrl !== ''): ?>
            <button class="m-play" type="button" data-play data-video-hide aria-label="Play welcome video"><i class="fa fa-play" aria-hidden="true"></i></button>
        <?php endif; ?>
        <span class="m-welcome__label" data-video-hide>WELCOME VIDEO</span>
    </div>

    <a class="m-btn m-welcome__enter" href="<?= site_url('mobile/lobby') ?>">Enter Lobby <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
