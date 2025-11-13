<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 06:36
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        overflow-x: hidden;
        color: #fff;
        position: relative;
    }

    /* 🎥 Background Video */
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
        z-index: -1;
    }

    /* 📱 Page Container */
    .webinar-wrapper {
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

    h3 {
        font-weight: 600;
        font-size: 1.4rem;
        color: #ffffff;
        margin-bottom: 0.8rem;
    }

    p.subtitle {
        font-size: 0.9rem;
        opacity: 0.85;
        margin-bottom: 2rem;
    }

    /* 🎬 Webinar Cards */
    .webinar-card {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 20px;
        padding: 1.2rem;
        margin-bottom: 1.2rem;
        box-shadow: 0 0 15px rgba(255,255,255,0.1);
        width: 100%;
        max-width: 700px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .webinar-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0 25px rgba(255, 216, 77, 0.4);
    }

    .webinar-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 0.4rem;
    }

    .webinar-meta {
        font-size: 0.85rem;
        color: #eee;
        margin-bottom: 0.4rem;
    }

    .badge-status {
        background: rgba(255,216,77,0.15);
        color: #f3bb1a;
        font-size: 0.7rem;
        padding: 3px 7px;
        border-radius: 8px;
        margin-right: 4px;
    }

    .btn-purple {
        color: #fff;
        border: none;
        border-radius: 30px;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-block;
        margin-top: 0.5rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        width: 180px;
    }

    .btn-purple:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(255, 180, 0, 0.4);
    }

    .btn-disabled {
        background: rgba(255, 255, 255, 0.2);
        color: #bbb;
        border-radius: 30px;
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
        border: none;
    }

    /* 🎬 Custom Overlay for Vimeo Player */
    .video-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.9);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .video-box {
        position: relative;
        width: 90%;
        max-width: 700px;
        aspect-ratio: 16 / 9;
        background: #000;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 40px rgba(255,255,255,0.15);
    }

    .video-box iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    .close-overlay {
        position: absolute;
        top: -45px;
        right: 0;
        background: none;
        color: #fff;
        font-size: 36px;
        border: none;
        cursor: pointer;
        z-index: 10;
        opacity: 0.85;
        transition: opacity .2s ease;
    }

    .close-overlay:hover {
        opacity: 1;
    }

    @media (max-width: 600px) {
        h3 { font-size: 1.2rem; }
        .webinar-card { padding: 1rem; }
        .webinar-title { font-size: 1rem; }

        .video-box {
            width: 95%;
            height: 56vw;
            border-radius: 0;
        }
        .close-overlay {
            top: 10px;
            right: 12px;
            font-size: 32px;
        }
    }
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/mobile-brain-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="webinar-wrapper">
    <h3>EPR Webinars</h3>
    <p class="subtitle">Join live webinars or watch past recordings</p>

    <?php if (empty($webinars)): ?>
        <p style="opacity: 0.8;">No webinars available at this time.</p>
    <?php else: ?>
        <?php foreach ($webinars as $webinar): ?>
            <?php
                $isOpen = (int)$webinar['is_open'] === 1;
                $isPast = (int)$webinar['is_past'] === 1;
                $hasRecording = !empty($webinar['vimeo_id']);
                $vimeoID = preg_replace('/\D/', '', $webinar['vimeo_id']);
                $start = $webinar['start_time'] ? date('h:i A', strtotime($webinar['start_time'])) : '';
                $end   = $webinar['end_time'] ? date('h:i A', strtotime($webinar['end_time'])) : '';
                $date  = date('l, F j, Y', strtotime($webinar['event_date']));
            ?>
            <div class="webinar-card">
                <div class="webinar-title"><?= esc($webinar['event_name']); ?></div>
                <div class="webinar-meta"><?= $date; ?><?= $start ? " | $start - $end" : ''; ?></div>

                <?php if (!empty($webinar['tags'])): ?>
                    <div class="mt-1">
                        <?php foreach (explode(',', $webinar['tags']) as $tag): ?>
                            <span class="badge-status"><?= trim($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                    <?php if ($isOpen && !$isPast): ?>
                        <a href="<?= esc($webinar['zoom_link']); ?>" target="_blank" class="btn-purple">
                            <i class="fa fa-video-camera"></i> Join Webinar
                        </a>
                    <?php elseif ($isPast && $hasRecording): ?>
                        <button class="btn-purple open-video epr-btn-four"
                                data-vimeo="https://player.vimeo.com/video/<?= esc($vimeoID); ?>?autoplay=1">
                            <i class="fa fa-play-circle"></i> Watch Recording
                        </button>
                    <?php else: ?>
                        <button class="btn-disabled" disabled>Locked</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- 🎥 Custom Fullscreen Video Overlay -->
<div id="videoOverlayContainer" class="video-overlay">
    <div class="video-box">
        <button class="close-overlay" id="closeVideoOverlay">&times;</button>
        <iframe id="videoFrame" src="" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const overlay = document.getElementById('videoOverlayContainer');
    const iframe = document.getElementById('videoFrame');
    const closeBtn = document.getElementById('closeVideoOverlay');

    document.querySelectorAll('.open-video').forEach(btn => {
        btn.addEventListener('click', function() {
            const src = this.dataset.vimeo;
            iframe.src = src;
            overlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    function closeOverlay() {
        iframe.src = ''; // stop playback
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    closeBtn.addEventListener('click', closeOverlay);

    // Close on tapping outside video
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeOverlay();
    });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
