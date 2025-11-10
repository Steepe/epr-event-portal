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
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    overflow-x: hidden;
}

/* 🎥 Fullscreen Video Background */
#bgVideo {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: 50% 45%; /* 👈 adjust this */
    z-index: -1;
}

#videoOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(40, 0, 60, 0.3); /* purple tint overlay */
    z-index: 0; /* sits above the video but below content */
}


.lobby-container {
    min-height: 100vh;
    padding: 120px 5% 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
}

/* Centerpiece (LIVE conference) */
.lobby-center {
    position: relative;
    text-align: center;
    z-index: 2;
}
.lobby-center img {
    width: 400px;
    height: auto;
    animation: pulseGlow 3s infinite ease-in-out;
    filter: drop-shadow(0 0 25px rgba(255,255,255,0.4));
}

@keyframes pulseGlow {
    0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px rgba(239,177,30,0.6)); }
    50% { transform: scale(1.07); filter: drop-shadow(0 0 40px rgba(239,177,30,0.9)); }
}

/* Bubbles container */
.lobby-events {
    position: relative;
    width: 100%;
    max-width: 1100px;
    margin-top: 60px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
}

/* Each bubble */
.lobby-bubble {
    text-align: center;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.lobby-bubble img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 50%;
    transition: transform .3s ease;
}
.lobby-bubble:hover img {
    transform: scale(1.05);
}
.lobby-bubble span {
    display: block;
    margin-top: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #fff;
}
.lobby-bubble:hover {
    transform: scale(1.08);
    box-shadow: 0 0 25px rgba(239,177,30,0.7);
}

/* Static bubbles */
.bubble-webinars { position: absolute; top: 15%; left: 15%; width: 180px; }
.bubble-envision { position: absolute; top: 45%; left: 25%; width: 100px; }

/* Responsive */
@media (max-width: 768px) {
    .bubble-webinars { top: 10%; left: 50%; transform: translateX(-50%); width: 120px; }
    .bubble-envision { top: 45%; left: 25%; transform: translateX(-50%); width: 80px; }
    .lobby-center img { width: 200px; }
}
</style>


<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?= asset_url('videos/start-bg.mp4'); ?>" type="video/mp4">
    Your browser does not support the video tag.
</video>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const video = document.getElementById("bgVideo");
        if (video) {
            video.playbackRate = 0.5; // 👈 0.5 = half speed, 1 = normal speed
        }
    });
</script>

<div id="videoOverlay"></div>



<div class="lobby-container">
    <!-- Static bubbles -->
    <div class="lobby-bubble bubble-webinars" onclick="window.location='<?php echo base_url('attendees/webinars'); ?>'">
        <img src="<?php echo asset_url('images/webinars.png'); ?>" alt="EPR Global Webinars">
    </div>
    <div class="lobby-bubble bubble-envision" onclick="window.location='<?php echo base_url('attendees/envision'); ?>'">
        <img src="<?php echo asset_url('images/envision.png'); ?>" alt="Envision">
    </div>

    <?php
    $live = null;
    $past = [];

    // Separate live and past conferences
    if (!empty($conferences)) {
        foreach ($conferences as $conf) {
            if ($conf['status'] === 'live') $live = $conf;
            else $past[] = $conf;
        }
    }
    ?>

    <!-- Live Conference (centerpiece) -->
    <?php if (!empty($live)): ?>
        <?php
            session()->set('live-conference-id', $live['conference_id']);
        ?>
        <div class="lobby-center">
            <img src="<?php echo base_url('uploads/conferences/' . $live['icon']); ?>" alt="<?php echo esc($live['title']); ?>"
                 onclick="window.location='<?php echo base_url('attendees/home'); ?>'">
        </div>
    <?php endif; ?>

    <!-- Past conferences arranged below -->
    <div class="lobby-events">
        <?php if (!empty($past)): ?>
            <?php
            usort($past, function($a, $b) {
                return (int)$a['year'] <=> (int)$b['year'];
            });
            $sizeMin = 100; // smallest bubble size
            $sizeStep = 20; // how much larger each one gets
            foreach ($past as $i => $conf):
                $size = $sizeMin + ($i * $sizeStep);
                $image = !empty($conf['icon'])
                    ? base_url('uploads/conferences/' . $conf['icon'])
                    : asset_url('images/lobby/default.png');
                ?>
                <div class="lobby-bubble" style="width:<?php echo $size; ?>px; height:<?php echo $size; ?>px;"
                     onclick="window.location='<?php echo base_url('attendees/pastConference/' . $conf['conference_id']); ?>'">
                    <img src="<?php echo $image; ?>" alt="<?php echo esc($conf['title']); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
