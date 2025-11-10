<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: eprglobal
 * Date: 29/08/2021
 * Time: 02:42
 */
?>

<style>
    .navbar.attendee-topbar {
        background-color: #9D0F82;
        padding: 0.6rem 1rem;
        border-bottom: 2px solid #EFB11E;
    }

    .navbar-brand img {
        height: 45px;
    }

    .navbar-nav .nav-link {
        color: #ffffff !important;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        margin: 0 5px;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #EFB11E !important;
    }

    .badge-notification {
        background-color: #9D0F82;
        color: #fff;
        font-size: 10px;
        position: absolute;
        top: 0;
        right: -5px;
        border-radius: 10px;
        padding: 2px 5px;
    }

    .dropdown-item:hover {
        background-color: #9D0F82;
        color: #fff;
    }
</style>

<body class="other-bg">
<nav class="navbar fixed-top navbar-expand-lg navbar-dark attendee-topbar">
    <a class="navbar-brand" href="<?php echo base_url('attendees/start'); ?>">
        <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="Event Logo">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo base_url('attendees/start'); ?>" style="font-size: 20px;">START</a>
            </li>
        </ul>
    </div>

</nav>

<div class="w3-light-grey" style="margin-top: 60px;">
    <div id="myBar" class="epr-orange" style="height:5px;width:0;"></div>
</div>
</body>
