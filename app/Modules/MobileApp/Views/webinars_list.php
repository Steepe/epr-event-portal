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
        background: radial-gradient(circle at center, #6a0080 0%, #150020 100%);
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
        background: linear-gradient(120deg, rgba(157,15,130,0.3), rgba(255,180,0,0.25));
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
        color: #ffd84d;
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
        color: #ffd84d;
        margin-bottom: 0.4rem;
    }

    .webinar-meta {
        font-size: 0.85rem;
        color: #eee;
        margin-bottom: 0.4rem;
    }

    .badge-status {
        background: rgba(255,216,77,0.15);
        color: #ffd84d;
        font-size: 0.7rem;
        padding: 3px 7px;
        border-radius: 8px;
        margin-right: 4px;
    }

    .btn-purple {
        background: linear-gradient(135deg, #9D0F82, #ffb400);
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

    /* 🎥 Modal */
    .modal-content {
        border-radius: 16px;
        background-color: #000;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .modal-header {
        border-bottom: 1px solid rgba(255,255,255,0.15);
    }

    .modal-title {
        color: #ffd84d;
        font-weight: 600;
    }

    .close {
        color: #fff;
        opacity: 0.8;
    }

    .close:hover { opacity: 1; }

    /* Responsive */
    @media (max-width: 600px) {
        h3 { font-size: 1.2rem; }
        .webinar-card { padding: 1rem; }
        .webinar-title { font-size: 1rem; }
    }
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/start-bg.mp4'); ?>" type="video/mp4">
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
                        <button class="btn-purple"
                                data-toggle="modal"
                                data-target="#vimeoModal<?= $webinar['event_id']; ?>">
                            <i class="fa fa-play-circle"></i> Watch Recording
                        </button>
                    <?php else: ?>
                        <button class="btn-disabled" disabled>Locked</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 🎥 Vimeo Modal -->
            <?php if ($isPast && $hasRecording): ?>
                <div class="modal fade" id="vimeoModal<?= $webinar['event_id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content position-relative">
                            <!-- ✖️ Close Button -->
                            <button type="button" class="close video-close-btn" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>

                            <div class="modal-body p-0 position-relative">
                                <div class="embed-responsive embed-responsive-16by9 video-frame-wrapper">
                                    <iframe class="embed-responsive-item"
                                            src="https://player.vimeo.com/video/<?= esc($vimeoID); ?>?autoplay=1&muted=0&dnt=1"
                                            allow="autoplay; fullscreen; picture-in-picture"
                                            allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
$('.modal').on('hidden.bs.modal', function () {
    const iframe = $(this).find('iframe');
    if (iframe.length) iframe.attr('src', iframe.attr('src')); // stop video on close
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
