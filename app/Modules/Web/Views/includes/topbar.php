<?php
/**
 * Created by PhpStorm.
 * User: Oluwamayowa Steepe
 * Project: eprglobal
 * Date: 29/08/2021
 * Time: 02:42
 */


$attendee = isset($global_attendee_details) ? $global_attendee_details : [];

$unread_count = isset($unread_messages) ? (int) $unread_messages : 0;

$profile_picture = !empty($attendee['profile_picture_url'])
    ? $attendee['profile_picture_url']
    : asset_url('images/user.png');

$full_name = isset($attendee['firstname'], $attendee['lastname'])
    ? $attendee['firstname'] . ' ' . $attendee['lastname']
    : 'Attendee';

$plan = isset($attendee['plan']) ? (int) $attendee['plan'] : 1;
?>

<style>
    .navbar.attendee-topbar {
        background-color: #0d0d0d;
        padding: 0.6rem 1rem;
        border-bottom: 2px solid #9D0F82;
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

<body class="unleash-25-bg">
<nav class="navbar fixed-top navbar-expand-lg navbar-dark attendee-topbar">
    <a class="navbar-brand" href="<?php echo base_url();?>">
        <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="Event Logo">
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <?php
            $conference_id = session()->get('live-conference-id');
            ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo base_url('attendees/lobby'); ?>">LOBBY</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo base_url('attendees/agenda/'.$conference_id); ?>">AGENDA</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo base_url('attendees/networking_center'); ?>">NETWORKING CENTER</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo base_url('attendees/exhibitors'); ?>">EXHIBITORS</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo base_url('attendees/sponsors'); ?>">SPONSORS</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo base_url('attendees/emergence_booth'); ?>">EMERGENCE BOOTH</a></li>
        </ul>

        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item d-none d-lg-block mr-3">
                <img src="<?php echo asset_url('images/unleash-logo.png'); ?>" alt="Conference Logo" height="30">
            </li>

            <li class="nav-item position-relative mr-3">
                <a href="<?php echo base_url('attendee/p/messages'); ?>" class="text-white position-relative">
                    <i class="fa fa-envelope" aria-hidden="true" style="font-size: 20px;"></i>
                    <?php if ($unread_count > 0) : ?>
                        <span class="badge badge-notification"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center"
                   href="#" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?php echo $profile_picture; ?>" width="40" height="40"
                         class="rounded-circle mr-2" alt="Profile Picture">
                    <span class="d-none d-lg-inline"><?php echo htmlspecialchars($full_name); ?></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="<?php echo base_url('attendees/profile'); ?>">Profile</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?php echo base_url('attendees/logout'); ?>">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div class="w3-light-grey" style="margin-top: 60px;">
    <div id="myBar" class="epr-orange" style="height:5px;width:0;"></div>
</div>
</body>
