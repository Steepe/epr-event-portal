<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:07
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        margin: 0;
        padding: 0;
        background-image: url('<?php echo asset_url('images/mobile-brain-light.png'); ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Inter', 'Poppins', sans-serif;
        color: #fff;
        overflow-x: hidden;
        position: relative;
        min-height: 100vh;
        background-color: ;
    }

    /* 🌈 Overlay Gradient */
    #bgOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    /* 🧭 Container */
    .networking-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 90px 1.5rem 100px;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .networking-container h3 {
        color: #A70B91;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 40px;
        font-size: 1.3rem;
        text-shadow: 0 0 10px rgba(255, 216, 77, 0.6);
    }

    /* 🪩 Grid Layout */
    .networking-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
        width: 100%;
        max-width: 500px;
        justify-items: center;
    }

    /* 🧩 Individual Icon Items */
    .networking-item {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        color: #fff;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 20px 10px;
        width: 100%;
        max-width: 180px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 0 10px rgba(255,255,255,0.08);
    }

    .networking-item img {
        width: 70px;
        height: 70px;
        object-fit: contain;
        margin-bottom: 10px;
        transition: transform 0.25s ease;
    }

    .networking-item span {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #A70B91;
        letter-spacing: 0.6px;
    }

    /* 🌟 Tap/Active Effect */
    .networking-item:active {
        transform: scale(0.96);
    }

    .networking-item:hover img {
        transform: scale(1.08);
    }

    /* 💡 Glow Color Variants */
    .attendees:hover {
        box-shadow: 0 0 18px rgba(239, 177, 30, 0.4);
    }
    .speakers:hover {
        box-shadow: 0 0 18px rgba(157, 15, 130, 0.4);
    }
    .communities:hover {
        box-shadow: 0 0 18px rgba(255, 120, 209, 0.4);
    }
    .points:hover {
        box-shadow: 0 0 18px rgba(255, 200, 60, 0.4);
    }
    .leaderboard:hover {
        box-shadow: 0 0 18px rgba(255, 255, 100, 0.4);
    }

    /* 📱 Responsive Adjustments */
    @media (max-width: 480px) {
        .networking-container h3 {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .networking-item img {
            width: 60px;
            height: 60px;
        }
        .networking-item span {
            font-size: 0.8rem;
        }
        .networking-grid {
            gap: 25px;
        }
    }
</style>

<!--  Background Overlay -->
<div id="bgOverlay"></div>

<!-- 🧠 Networking Center -->
<div class="networking-container">
    <h3>Networking Center</h3>

    <div class="networking-grid">
        <a href="<?php echo base_url('mobile/attendees'); ?>" class="networking-item attendees">
            <img src="<?php echo asset_url('images/attendees-icon.png'); ?>" alt="Attendees">
            <span>Attendees</span>
        </a>

        <a href="<?php echo base_url('mobile/speakers'); ?>" class="networking-item speakers">
            <img src="<?php echo asset_url('images/speakers-icon.png'); ?>" alt="Speakers">
            <span>Speakers</span>
        </a>

        <a href="https://staging.eprglobalmembers.com/signin" class="networking-item communities">
            <img src="<?php echo asset_url('images/community-icon.png'); ?>" alt="Communities">
            <span>Communities</span>
        </a>

        <a href="<?php echo base_url('mobile/points'); ?>" class="networking-item points">
            <img src="<?php echo asset_url('images/earnpoints-icon.png'); ?>" alt="Earn Points">
            <span>Earn Points</span>
        </a>

        <a href="<?php echo base_url('mobile/leaderboard'); ?>" class="networking-item leaderboard">
            <img src="<?php echo asset_url('images/leaderboard-icon.png'); ?>" alt="Leaderboard">
            <span>Leaderboard</span>
        </a>
    </div>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
