<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 30/10/2025
 * Time: 15:53
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
    /* 🌌 Background setup */
    body {
        background: url('<?php echo asset_url('images/brain-Events-Portal3-2025.png'); ?>') no-repeat center center fixed;
        background-size: cover;
        overflow-x: hidden;
        color: #fff;
    }

    /* ⚙️ Container setup */
    .networking-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 60px 0;
    }

    .networking-container h2 {
        color: #fff;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 80px;
        text-transform: uppercase;
        text-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }

    /* 🧩 Icon grid */
    .networking-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 70px;
        justify-items: center;
        align-items: center;
        width: 85%;
    }

    /* 🪩 Icon items */
    .networking-item {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        color: #fff;
        position: relative;
        text-align: center;
        transition: transform 0.4s ease, filter 0.4s ease;
    }

    .networking-item img {
        width: 110px;
        height: 110px;
        transition: all 0.4s ease;
    }

    .networking-item span {
        font-weight: 600;
        font-size: 16px;
        letter-spacing: 0.8px;
        margin-top: 18px;
        text-transform: uppercase;
        transition: color 0.4s ease, text-shadow 0.4s ease;
    }

    /* 🌟 Hover glow and scale */
    .networking-item:hover img {
        transform: scale(1.15);
        filter: brightness(1) drop-shadow(0 0 25px rgba(255, 255, 255, 0.9));
    }

    .networking-item:hover span {
        color: #EFB11E;
        text-shadow: 0 0 15px rgba(239, 177, 30, 0.8);
    }

    /* 💡 Optional colored hover glows for brand identity */
    .networking-item.attendees:hover img {
        filter: drop-shadow(0 0 25px #EFB11E);
    }
    .networking-item.speakers:hover img {
        filter: drop-shadow(0 0 25px #9D0F82);
    }
    .networking-item.points:hover img {
        filter: drop-shadow(0 0 25px #FF78D1);
    }
    .networking-item.leaderboard:hover img {
        filter: drop-shadow(0 0 25px #F4C542);
    }

    /* 🖥️ Responsive adjustments */
    @media (max-width: 768px) {
        .networking-container h2 {
            font-size: 20px;
            margin-bottom: 50px;
        }
        .networking-item img {
            width: 85px;
            height: 85px;
        }
        .networking-item span {
            font-size: 14px;
        }
    }
</style>

<h4 class="text-white" style="position: absolute; top: 280px; left: 200px;">Networking Center</h4>

<div class="networking-container">

    <div class="networking-grid">
        <a href="<?php echo base_url('attendees/attendees'); ?>" class="networking-item attendees">
            <img src="<?php echo asset_url('images/attendees-icon.png'); ?>" alt="Attendees">
            <span>Attendees</span>
        </a>

        <a href="<?php echo base_url('attendees/speakers'); ?>" class="networking-item speakers">
            <img src="<?php echo asset_url('images/speakers-icon.png'); ?>" alt="Speakers">
            <span>Speakers</span>
        </a>

        <a href="<?php echo base_url('attendees/earn-points'); ?>" class="networking-item points">
            <img src="<?php echo asset_url('images/earnpoints-icon.png'); ?>" alt="Earn Points">
            <span>Earn Points</span>
        </a>

        <a href="<?php echo base_url('attendees/leaderboard'); ?>" class="networking-item leaderboard">
            <img src="<?php echo asset_url('images/leaderboard-icon.png'); ?>" alt="Leaderboard">
            <span>Leaderboard</span>
        </a>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
