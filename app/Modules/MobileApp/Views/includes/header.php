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
        body {
            background-color: #f9fafb;
            color: #1f2937;
            font-family: 'Inter', 'Open Sans', Arial, sans-serif;
            padding-top: 56px; /* Space for sticky header */
        }

        .mobile-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            background: #796a7a;
            border-bottom: 1px solid #796a7a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .mobile-header h1 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.2rem;
            color: #111827;
            margin: 0;
        }

        .mobile-header .menu-btn {
            background: none;
            border: none;
            outline: none;
            font-size: 1.25rem;
            color: #4f46e5;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 75%;
            height: 100%;
            background: #ffffff;
            z-index: 998;
            transition: right 0.3s ease;
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu .menu-header {
            padding: 1rem;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mobile-menu ul {
            list-style: none;
            padding: 1rem;
            margin: 0;
        }

        .mobile-menu ul li {
            margin-bottom: 1rem;
        }

        .mobile-menu ul li a {
            color: #1f2937;
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
        }

        .mobile-menu ul li a:hover {
            color: #4f46e5;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 997;
            display: none;
        }

        .overlay.active {
            display: block;
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
        <h5 class="m-0 text-dark fw-semibold">Menu</h5>
        <button class="menu-btn text-dark" id="closeMobileMenu" aria-label="Close menu">
            <i class="fa fa-times"></i>
        </button>
    </div>
    <ul>
        <li><a href="<?php echo site_url('mobile'); ?>"><i class="fa fa-home me-2"></i>Home</a></li>
        <li><a href="<?php echo site_url('mobile/sessions'); ?>"><i class="fa fa-calendar me-2"></i>Sessions</a></li>
        <li><a href="<?php echo site_url('mobile/messages'); ?>"><i class="fa fa-comments me-2"></i>Messages</a></li>
        <li><a href="<?php echo site_url('mobile/profile'); ?>"><i class="fa fa-user me-2"></i>Profile</a></li>
        <li><a href="<?php echo site_url('mobile/logout'); ?>" class="text-danger"><i class="fa fa-sign-out me-2"></i>Logout</a></li>
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

