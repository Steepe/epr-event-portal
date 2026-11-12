<?php
/**
 * Mobile app shell.
 *
 * $shell     'bare'   – no top bar or tabs (login, register, payment result)
 *            'plain'  – transparent top bar, no tabs (start, welcome)
 *            'tabbed' – top bar + bottom tab bar (everything else)
 * $back      true or a fallback URL to show the back arrow
 * $activeTab lobby | agenda | network | exhibitors | me
 */
$shell     = $shell ?? 'tabbed';
$back      = $back ?? false;
$activeTab = $activeTab ?? '';

$mobileUserId = (int) (session('user_id') ?: 0);
$mobileAvatar = session('profile_picture')
    ? '/uploads/attendee_pictures/' . rawurlencode((string) session('profile_picture'))
    : mobile_asset_url('images/user.png');
$mobileUnread = $shell === 'bare' ? 0 : \App\Modules\MobileApp\Libraries\MobileNotifications::unreadCount();

$mobileFlash     = session()->getFlashdata('error') ?: session()->getFlashdata('success');
$mobileFlashType = session()->getFlashdata('error') ? 'error' : 'success';
$mobileBackUrl   = is_string($back) ? $back : site_url('mobile/lobby');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="Global EPR Emergence Conference">
    <meta name="theme-color" content="#261528">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?= isset($title) ? esc($title) . ' — EPR Mobile' : 'EPR Mobile | Emergence Conference Global' ?></title>
    <link rel="shortcut icon" href="<?= mobile_asset_url('images/favicon.png') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= mobile_asset_url('font-awesome/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?= mobile_asset_url('css/mobile.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/mobile.css') ?: 1 ?>">
    <script>
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = "https://eprglobal.matomo.cloud/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.async = true; g.src = 'https://cdn.matomo.cloud/eprglobal.matomo.cloud/matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
</head>
<body class="m-app" data-user="<?= $mobileUserId ?>" data-api="<?= esc(rtrim(site_url('api'), '/'), 'attr') ?>"<?php if ($mobileFlash): ?> data-flash="<?= esc($mobileFlash, 'attr') ?>" data-flash-type="<?= $mobileFlashType ?>"<?php endif; ?>>
<div class="m-bg" aria-hidden="true"><canvas id="mStage"></canvas></div>
<div class="m-shell m-shell--<?= esc($shell, 'attr') ?>">
<?php if ($shell !== 'bare'): ?>
    <header class="m-topbar">
        <?php if ($back): ?>
            <button class="m-topbar__back" type="button" data-back="<?= esc($mobileBackUrl, 'attr') ?>" aria-label="Back"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></button>
        <?php endif; ?>
        <a class="m-topbar__logo" href="<?= site_url('mobile/welcome') ?>" aria-label="EPR Events start"><img src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events"></a>
        <div class="m-topbar__actions">
            <button class="m-topbar__bell" type="button" data-open-sheet="notif" aria-label="Notifications">
                <i class="fa fa-bell" aria-hidden="true"></i>
                <span class="m-topbar__badge" id="mBellBadge"<?= $mobileUnread ? '' : ' hidden' ?>><?= $mobileUnread ?></span>
            </button>
            <a href="<?= site_url('mobile/profile') ?>" aria-label="My profile"><img class="m-topbar__avatar" src="<?= esc($mobileAvatar, 'attr') ?>" alt="" onerror="this.onerror=null;this.src='<?= mobile_asset_url('images/user.png') ?>'"></a>
        </div>
    </header>
<?php endif; ?>
    <main class="m-main">
