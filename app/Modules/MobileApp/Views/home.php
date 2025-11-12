<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 04:45
 */


echo module_view('MobileApp', 'includes/header');
?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        overflow-x: hidden;
        color: #fff;
        background: radial-gradient(circle at center, #6a0080 0%, #150020 100%);
        position: relative;
    }

    /* 🎥 Video Background */
    #bgVideo {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(8px) brightness(0.5);
        z-index: -2;
    }

    /* 🌈 Overlay Gradient */
    #videoOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, rgba(157,15,130,0.3), rgba(255,180,0,0.25));
        z-index: -1;
    }

    /* 🌀 Lobby Layout */
    .mobile-lobby {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        text-align: center;
        padding: 80px 1.5rem 100px;
        position: relative;
        z-index: 2;
    }

    .lobby-header {
        margin-bottom: 1.5rem;
    }

    .lobby-header h3 {
        font-weight: 600;
        color: #fff;
        font-size: 1.3rem;
    }

    .lobby-header p {
        font-size: 0.9rem;
        opacity: 0.85;
        margin-bottom: 0;
    }

    /* 🔘 Bubble Defaults */
    .lobby-bubble {
        border-radius: 50%;
        cursor: pointer;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform .3s ease, box-shadow .3s ease;
        filter: drop-shadow(0 0 10px rgba(255,255,255,0.15));
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
    }

    .lobby-bubble img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .lobby-bubble:active {
        transform: scale(0.95);
    }

    /* 🌟 Live Big Bubble */
    .lobby-center-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 40px 0;
    }

    .lobby-center {
        position: relative;
        z-index: 5;
        animation: pulseGlow 3.5s infinite ease-in-out;
        cursor: pointer;
    }

    .lobby-center img {
        width: 320px;
        height: 320px;
        border-radius: 50%;
        object-fit: contain;
        transition: transform .3s ease;
    }

    .lobby-center img:active {
        transform: scale(0.98);
    }

    @keyframes pulseGlow {
        0%, 100% { transform: scale(1); filter: drop-shadow(0 0 25px rgba(255,210,80,0.8)); }
        50% { transform: scale(1.08); filter: drop-shadow(0 0 40px rgba(255,230,100,1)); }
    }

    /* 🔝 Top Functional Bubbles */
    .lobby-top {
        display: flex;
        justify-content: space-around;
        align-items: center;
        width: 100%;
        margin-bottom: 1.5rem;
        z-index: 3;
    }

    .lobby-top .lobby-bubble {
        width: 120px;
        height: 120px;
    }

    /* 🏆 Past Conferences Grid */
    .lobby-past {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        width: 100%;
        justify-items: center;
        margin-top: 30px;
    }

    .lobby-past .lobby-bubble {
        width: 120px;
        height: 120px;
    }

    /* 📱 Responsive scaling */
    @media (max-width: 600px) {
        .lobby-center img {
            width: 260px;
            height: 260px;
        }
        .lobby-top .lobby-bubble,
        .lobby-past .lobby-bubble {
            width: 110px;
            height: 110px;
        }
    }
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/start-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="mobile-lobby">
    <div class="lobby-header">
        <h3>Welcome, <?php echo esc(session('user_email')); ?></h3>
        <p>Tap a bubble to explore conferences and experiences</p>
    </div>

    <!-- 🔝 Quick Access -->
    <div class="lobby-top">
        <div class="lobby-bubble"
             onclick="window.location='<?php echo base_url('mobile/webinars'); ?>'">
            <img src="<?php echo asset_url('images/webinars.png'); ?>" alt="Webinars">
        </div>
        <div class="lobby-bubble"
             onclick="window.location='<?php echo base_url('mobile/envision'); ?>'">
            <img src="<?php echo asset_url('images/envision.png'); ?>" alt="Envision">
        </div>
    </div>

    <?php
    $live = null;
    $past = [];

    if (!empty($conferences)) {
        foreach ($conferences as $conf) {
            if ($conf['status'] === 'live') $live = $conf;
            else $past[] = $conf;
        }
    }
    ?>

    <!-- 🌟 Live Big Bubble -->
    <?php if (!empty($live)): ?>
        <?php session()->set('live-conference-id', $live['conference_id']); ?>
        <div class="lobby-center-wrapper">
            <div class="lobby-center"
                 onclick="window.location='<?php echo base_url('mobile/welcome'); ?>'">
                <img src="<?php echo base_url('uploads/conferences/' . $live['icon']); ?>"
                     alt="<?php echo esc($live['title']); ?>">
            </div>
        </div>
    <?php endif; ?>

    <!-- 🏆 Past Conferences -->
    <?php if (!empty($past)): ?>
        <div class="lobby-past">
            <?php
            usort($past, fn($a, $b) => (int)$a['year'] <=> (int)$b['year']);
            foreach ($past as $conf):
                $image = !empty($conf['icon'])
                    ? base_url('uploads/conferences/' . $conf['icon'])
                    : asset_url('images/lobby/default.png');
            ?>
                <div class="lobby-bubble"
                     onclick="window.location='<?php echo base_url('mobile/pastConference/' . $conf['conference_id']); ?>'">
                    <img src="<?php echo $image; ?>" alt="<?php echo esc($conf['title']); ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
