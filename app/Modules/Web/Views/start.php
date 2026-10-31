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

/* 🔵 Past Conference Bubbles */
.lobby-events {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 40px;
    margin-top: 0;
    z-index: 1;
}

/* 📱 Responsive */
@media (max-width: 992px) {
    .lobby-events { gap: 25px; }
}
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?= asset_url('videos/start-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="lobby-container">

    <?php
    $past = $conferences ?? [];
    ?>

    <!-- 🏆 Past Conferences -->
    <div class="lobby-events">
        <?php if (!empty($past)): ?>
            <?php
            // Sort by year ascending
            usort($past, fn($a, $b) => (int)$a['year'] <=> (int)$b['year']);

            // Fixed bubble size
            $bubbleSize = 140; // adjust as desired

            foreach ($past as $conf):
                $image = !empty($conf['icon'])
                    ? base_url('uploads/conferences/' . $conf['icon'])
                    : asset_url('images/lobby/default.png');
                ?>
                <div class="lobby-bubble"
                     style="width:<?php echo $bubbleSize; ?>px; height:<?php echo $bubbleSize; ?>px;"
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
