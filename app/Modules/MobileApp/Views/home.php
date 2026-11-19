<?= module_view('MobileApp', 'includes/header', ['shell' => 'plain', 'back' => site_url('mobile/welcome'), 'activeTab' => '', 'title' => 'Welcome', 'screenLabel' => 'Welcome']) ?>
<?php
$conferenceTitle = trim((string) ($conference['title'] ?? 'Accelerate')) ?: 'Accelerate';
$conferenceYear  = (string) ($conference['year'] ?? date('Y'));
$welcomeVideoUrl = trim((string) ($welcome_video_url ?? ''));
$hasWelcomeVideo = $welcomeVideoUrl !== '' && filter_var($welcomeVideoUrl, FILTER_VALIDATE_URL);
$transparentWelcomeVideo = ! empty($welcome_video_is_transparent);
?>

<div class="m-welcome">
    <div class="m-welcome__head">
        <span class="m-kicker">Emergence Conference · <?= esc($conferenceYear) ?></span>
        <h1 class="m-welcome__title">Welcome to <?= esc($conferenceTitle) ?></h1>
    </div>

    <div class="m-welcome__video<?= $hasWelcomeVideo ? '' : ' is-unavailable' ?><?= $transparentWelcomeVideo ? ' is-transparent' : '' ?>"<?= $hasWelcomeVideo ? ' data-video-src="' . esc($welcomeVideoUrl, 'attr') . '" data-video-title="Welcome video"' . ($transparentWelcomeVideo ? ' data-transparent-video="true"' : '') : '' ?>>
        <?php if ($hasWelcomeVideo): ?>
            <button class="m-play" type="button" data-play data-video-hide aria-label="Play welcome video"><i class="fa fa-play" aria-hidden="true"></i></button>
            <span class="m-welcome__label" data-video-hide>WELCOME FILM</span>
        <?php else: ?>
            <img class="m-welcome__placeholder" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="<?= esc($conferenceTitle, 'attr') ?>">
            <span class="m-welcome__unavailable">Conference film coming soon</span>
        <?php endif; ?>
    </div>

    <a class="m-btn m-welcome__enter" href="<?= site_url('mobile/lobby') ?>">Enter Lobby <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
