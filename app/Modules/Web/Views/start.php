<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 23:34
 */


echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/lobby_topbar');
?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    overflow-x: hidden;
    color: #fff;
    background: radial-gradient(circle at center, #6a0080 0%, #150020 100%);
}

/* 🎥 Video Background */
#bgVideo {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    filter: blur(6px) brightness(0.6);
    z-index: -2;
}

/* 🌈 Overlay Gradient */
#videoOverlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: linear-gradient(120deg, rgba(157,15,130,0.3), rgba(255,180,0,0.2));
    z-index: -1;
}

/* 🌀 Main Lobby Container */
.lobby-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
    padding: 100px 0 60px;
}

/* 🟣 Bubble Defaults */
.lobby-bubble {
    border-radius: 50%;
    cursor: pointer;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: transform .3s ease, box-shadow .3s ease;
    filter: drop-shadow(0 0 10px rgba(255,255,255,0.2));
}
.lobby-bubble:hover {
    transform: scale(1.08);
    box-shadow: 0 0 25px rgba(255,255,255,0.3);
}
.lobby-bubble img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

/* 💫 Center Live Bubble */
.lobby-center {
    position: relative;
    bottom: 180px;
    z-index: 2;
    animation: pulseGlow 3s infinite ease-in-out;
}
.lobby-center img {
    width: 380px;
    height: auto;
}
@keyframes pulseGlow {
    0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px rgba(255,200,50,0.8)); }
    50% { transform: scale(1.07); filter: drop-shadow(0 0 35px rgba(255,210,90,1)); }
}

/* 🎯 Static Top Bubbles */
.bubble-webinars {
    position: absolute;
    top: 13.5%; left: 15%;
    width: 150px;
    height: 150px;
}
.bubble-envision {
    position: absolute;
    top: 13.5%;
    right: 15%;
    width: 150px;
    height: 150px;
}

/* 🔵 Past Conference Bubbles */
.lobby-events {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
    margin-top: 70px;
    z-index: 1;
}

/* 📱 Responsive */
@media (max-width: 992px) {
    .bubble-webinars, .bubble-envision {
        position: static;
        width: 120px; height: 120px;
        margin-bottom: 30px;
    }
    .lobby-center img { width: 240px; }
    .lobby-events { gap: 25px; }
}
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?= asset_url('videos/start-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="lobby-container">

    <!-- 🟣 Static Bubbles -->
    <div class="lobby-bubble bubble-webinars"
         onclick="window.location='<?php echo base_url('attendees/webinars'); ?>'">
        <img src="<?php echo asset_url('images/webinars.png'); ?>" alt="EPR Global Webinars">
    </div>

    <div class="lobby-bubble bubble-envision"
         onclick="window.location='<?php echo base_url('attendees/envision'); ?>'">
        <img src="<?php echo asset_url('images/envision.png'); ?>" alt="Envision">
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

    <!-- 🌟 Live Conference -->
    <?php if (!empty($live)): ?>
        <?php session()->set('live-conference-id', $live['conference_id']); ?>
        <div class="lobby-center" style="cursor: pointer;">
            <img src="<?php echo base_url('uploads/conferences/' . $live['icon']); ?>"
                 alt="<?php echo esc($live['title']); ?>"
                 onclick="window.location='<?php echo base_url('attendees/home'); ?>'">
        </div>
    <?php endif; ?>

    <!-- 🏆 Past Conferences -->
    <div class="lobby-events">
        <?php if (!empty($past)): ?>
            <?php
            usort($past, fn($a, $b) => (int)$a['year'] <=> (int)$b['year']);
            $sizeMin = 100;
            $sizeStep = 20;
            foreach ($past as $i => $conf):
                $size = $sizeMin + ($i * $sizeStep);
                $image = !empty($conf['icon'])
                    ? base_url('uploads/conferences/' . $conf['icon'])
                    : asset_url('images/lobby/default.png');
            ?>
                <div class="lobby-bubble"
                     <!--style="width:<?php /*echo $size; */?>px; height:<?php /*echo $size; */?>px;"-->
                     style="width:160px; height:160px;"
                     onclick="window.location='<?php echo base_url('attendees/pastConference/' . $conf['conference_id']); ?>'">
                    <img src="<?php echo $image; ?>" alt="<?php echo esc($conf['title']); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
