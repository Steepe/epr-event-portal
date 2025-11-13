<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 21:40
 */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?php echo isset($title) ? esc($title) . ' — EPR Mobile' : 'EPR Mobile | Emergence Conference Global'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta content="Global EPR Emergence Conference" name="description" />
    <meta content="Creyatif - 08033868618 business@creyatif.com" name="author" />
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo asset_url('images/favicon.png'); ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo asset_url('css/bootstrap.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset_url('css/eprglobal.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset_url('css/mobile.css'); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo asset_url('font-awesome/css/font-awesome.min.css'); ?>" type="text/css">

    <!-- Matomo Analytics -->
    <script>
        var _paq = window._paq = window._paq || [];
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u = "https://eprglobal.matomo.cloud/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.async = true; g.src = 'https://cdn.matomo.cloud/eprglobal.matomo.cloud/matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>

    <!-- Inline MobileApp styles -->
    <style>
        /* ==== General Mobile Layout ==== */
        body {
            background-color: #0b0b0b;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            padding-top: 60px;
        }

        /* ==== Header ==== */
        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.2rem;
        }

        .mobile-header h1 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.25rem;
            color: #EFB11E;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .mobile-header .menu-btn {
            background: none;
            border: none;
            outline: none;
            font-size: 1.5rem;
            color: #ffffff;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .mobile-header .menu-btn:hover {
            transform: scale(1.1);
        }

        /* ==== Slide-in Menu ==== */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 78%;
            max-width: 360px;
            height: 100%;
            background: rgba(15, 15, 15, 0.95);
            backdrop-filter: blur(15px);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: -3px 0 20px rgba(0, 0, 0, 0.5);
            z-index: 998;
            transition: right 0.4s ease;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }
        .mobile-menu.active {
            right: 0;
        }

        /* ==== Menu Header ==== */
        .menu-header {
            padding: 1rem 1.2rem;
            background: linear-gradient(90deg, #9D0F82, #EFB11E);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top-left-radius: 16px;
        }

        .menu-header h5 {
            color: #fff;
            font-weight: 600;
            margin: 0;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }
        .menu-header .menu-btn {
            color: #fff;
            font-size: 1.25rem;
            border: none;
            background: transparent;
        }

        /* ==== Menu Items ==== */
        .mobile-menu ul {
            list-style: none;
            margin: 0;
            padding: 1.5rem;
        }

        .mobile-menu ul li {
            margin-bottom: 1.2rem;
        }

        .mobile-menu ul li a {
            color: #f5f5f5;
            text-decoration: none;
            font-size: 1rem;
            display: flex;
            align-items: center;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .mobile-menu ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #EFB11E;
        }

        .mobile-menu ul li a i {
            width: 26px;
            text-align: center;
            color: #EFB11E;
            margin-right: 10px;
            transition: 0.3s ease;
        }

        /* ==== Overlay ==== */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(6px);
            z-index: 997;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }
        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ==== Animation for open/close ==== */
        .mobile-menu ul li a {
            transform: translateX(15px);
            opacity: 0;
        }
        .mobile-menu.active ul li a {
            animation: fadeInRight 0.4s ease forwards;
        }
        .mobile-menu.active ul li:nth-child(1) a { animation-delay: 0.05s; }
        .mobile-menu.active ul li:nth-child(2) a { animation-delay: 0.1s; }
        .mobile-menu.active ul li:nth-child(3) a { animation-delay: 0.15s; }
        .mobile-menu.active ul li:nth-child(4) a { animation-delay: 0.2s; }
        .mobile-menu.active ul li:nth-child(5) a { animation-delay: 0.25s; }

        @keyframes fadeInRight {
            0% { opacity: 0; transform: translateX(15px); }
            100% { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>

<body>

<header class="mobile-header">
    <h1></h1>
    <button class="menu-btn" id="openMobileMenu" aria-label="Open menu">
        <i class="fa fa-bars"></i>
    </button>
</header>

<div class="overlay" id="overlay"></div>

<nav class="mobile-menu" id="mobileMenu">
    <div class="menu-header">
        <h5 class="m-0 text-white fw-semibold">Menu</h5>
        <button class="menu-btn text-white" id="closeMobileMenu" aria-label="Close menu">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <!-- Optional Profile Section -->
    <div class="text-center py-3 border-bottom border-light">
        <img src="<?php echo session('profile_picture')
            ? base_url('uploads/attendees/' . session('profile_picture'))
            : asset_url('images/avatar.png'); ?>"
             class="rounded-circle mb-2" width="60" height="60" alt="Profile">
        <p class="mb-0 text-white fw-semibold">
            <?php echo esc(session('name') ?? 'Guest User'); ?>
        </p>
    </div>

    <ul class="mt-3">
        <li><a href="<?php echo base_url('mobile/home'); ?>"><i class="fa fa-home me-2"></i>Home</a></li>
        <li><a href="<?php echo base_url('mobile/lobby'); ?>"><i class="fa fa-bank me-2"></i>Lobby</a></li>
        <li><a href="<?php echo base_url('mobile/agenda/' . session()->get('live-conference-id')); ?>"><i class="fa fa-calendar me-2"></i>Agenda</a></li>
        <li><a href="<?php echo base_url('mobile/networking-center'); ?>"><i class="fa fa-building me-2"></i>Networking Centaer</a></li>
        <li><a href="<?php echo base_url('mobile/emergence-booth'); ?>"><i class="fa fa-microphone me-2"></i>Emergence Booth</a></li>
        <li><a href="<?php echo base_url('mobile/profile'); ?>"><i class="fa fa-user me-2"></i>Profile</a></li>
        <li><a href="<?php echo base_url('mobile/logout'); ?>" class="text-danger"><i class="fa fa-sign-out me-2"></i>Logout</a></li>
    </ul>
</nav>



<script>
    const openBtn = document.getElementById('openMobileMenu');
    const closeBtn = document.getElementById('closeMobileMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('overlay');

    openBtn?.addEventListener('click', () => {
        mobileMenu.classList.add('active');
        overlay.classList.add('active');
    });

    closeBtn?.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
    });

    overlay?.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        overlay.classList.remove('active');
    });
</script>

