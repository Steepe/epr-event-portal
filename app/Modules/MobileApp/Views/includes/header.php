<?php
$mobileConferenceId = session()->get('live-conference-id');
$mobileProfileImage = session()->get('profile_picture')
    ? base_url('uploads/attendee_pictures/' . session()->get('profile_picture'))
    : asset_url('images/user.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#261528">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?= isset($title) ? esc($title) . ' — EPR Mobile' : 'EPR Mobile | Emergence Conference Global' ?></title>
    <link rel="shortcut icon" href="<?= asset_url('images/favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset_url('css/bootstrap.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/eprglobal.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/mobile.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('font-awesome/css/font-awesome.min.css') ?>">
</head>
<body class="epr-mobile-app">
<header class="epr-mobile-topbar">
    <a class="epr-mobile-brand" href="<?= base_url('mobile/lobby') ?>" aria-label="EPR Mobile home">
        <span>EPR</span>
        <small>ACCELERATE 2026</small>
    </a>
    <div class="epr-mobile-actions">
        <button class="epr-icon-button epr-back-button" type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='<?= base_url('mobile/lobby') ?>'" aria-label="Go back">
            <i class="fa fa-chevron-left"></i>
        </button>
        <button class="epr-icon-button" id="openMobileMenu" type="button" aria-label="Open menu"><i class="fa fa-bars"></i></button>
    </div>
</header>

<div class="epr-menu-scrim" id="overlay"></div>
<nav class="epr-mobile-menu" id="mobileMenu" aria-label="Mobile menu">
    <div class="epr-menu-heading">
        <div>
            <img src="<?= esc($mobileProfileImage) ?>" alt="" class="epr-menu-avatar">
            <span><?= esc(session()->get('firstname') ?? 'Attendee') ?></span>
        </div>
        <button class="epr-icon-button" id="closeMobileMenu" type="button" aria-label="Close menu"><i class="fa fa-times"></i></button>
    </div>
    <a href="<?= base_url('mobile/lobby') ?>"><i class="fa fa-th-large"></i> Lobby</a>
    <a href="<?= base_url('mobile/agenda/' . $mobileConferenceId) ?>"><i class="fa fa-calendar"></i> Agenda</a>
    <a href="<?= base_url('mobile/networking-center') ?>"><i class="fa fa-users"></i> Networking</a>
    <a href="<?= base_url('mobile/exhibitors') ?>"><i class="fa fa-building"></i> Exhibitors</a>
    <a href="<?= base_url('mobile/sponsors') ?>"><i class="fa fa-diamond"></i> Sponsors</a>
    <a href="<?= base_url('mobile/emergence-booth') ?>"><i class="fa fa-microphone"></i> Emergence Booth</a>
    <a href="<?= base_url('mobile/profile') ?>"><i class="fa fa-user"></i> My profile</a>
    <a class="epr-menu-signout" href="<?= base_url('mobile/logout') ?>"><i class="fa fa-sign-out"></i> Sign out</a>
</nav>

<script>
(() => {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('overlay');
    const close = () => { menu?.classList.remove('is-open'); overlay?.classList.remove('is-open'); };
    document.getElementById('openMobileMenu')?.addEventListener('click', () => { menu?.classList.add('is-open'); overlay?.classList.add('is-open'); });
    document.getElementById('closeMobileMenu')?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
})();
</script>
