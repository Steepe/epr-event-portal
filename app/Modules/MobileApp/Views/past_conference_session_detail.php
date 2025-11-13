<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 07:33
 */

echo module_view('MobileApp', 'includes/header_less_menu'); ?>

<style>
body {
    font-family: 'Inter', 'Poppins', sans-serif;
    color: #fff;
    background: radial-gradient(circle at center, #6a0080 0%, #150020 100%);
    overflow: hidden;
    margin: 0;
}

/* 🎬 Fullscreen Modal */
.video-modal {
    position: fixed;
    inset: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.96);
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto;
    z-index: 9990;
    padding-bottom: 80px;
}

/* ✖️ Close Button — always visible */
.close-btn {
    position: fixed;
    top: 18px; right: 18px;
    background: rgba(157,15,130,0.95);
    border: none;
    border-radius: 50%;
    width: 46px; height: 46px;
    color: #fff;
    font-size: 24px;
    cursor: pointer;
    z-index: 10050; /* ensures above iframe */
    box-shadow: 0 0 15px rgba(157,15,130,0.6);
    transition: all 0.2s ease;
}
.close-btn:hover {
    background: rgba(255,180,0,0.9);
    transform: scale(1.05);
}

/* 🎥 Video Container */
.video-wrapper {
    position: relative;
    width: 90%;
    max-width: 760px;
    aspect-ratio: 16 / 9;
    border-radius: 16px;
    overflow: hidden;
    margin-top: 80px;
    z-index: 9995;
    box-shadow: 0 0 30px rgba(0,0,0,0.6);
}
.video-wrapper iframe {
    position: relative;
    width: 100%; height: 100%;
    border: none;
    border-radius: 16px;
    z-index: 9994;
    pointer-events: auto;
}

/* 📋 Info Section */
.session-info {
    width: 90%;
    max-width: 760px;
    margin-top: 25px;
    text-align: left;
}
.session-info h4 {
    color: #A70B91;
    font-weight: 700;
    margin-bottom: 0.6rem;
}
.description {
    font-size: 0.9rem;
    color: #f3f3f3;
    line-height: 1.6;
    margin-top: 10px;
}

/* 👥 Speakers */
.speaker-card {
    display: flex;
    align-items: center;
    border-radius: 12px;
    padding: 10px;
    margin-top: 10px;
}
.speaker-card img {
    width: 60px; height: 60px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 10px;
}
.speaker-info h6 {
    color: #f764e2;
    font-size: 0.9rem;
    margin-bottom: 3px;
}
.speaker-info p {
    font-size: 0.8rem;
    opacity: 0.85;
    margin: 0;
}

/* 📱 Responsive */
@media (max-width: 480px) {
    .video-wrapper { width: 95%; margin-top: 70px; }
    .close-btn { width: 40px; height: 40px; font-size: 20px; top: 14px; right: 14px; }
}
</style>

<div class="video-modal" id="videoModal">
    <button class="close-btn" onclick="closeVideo()">&times;</button>

    <div class="video-wrapper">
        <iframe
            id="vimeoPlayer"
            src="https://player.vimeo.com/video/<?php echo esc($session['vimeo_id']); ?>?autoplay=1&muted=0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>

    <div class="session-info">
        <h4><?php echo esc($session['sessions_name']); ?></h4>

        <?php if (!empty($speakers)): ?>
            <?php foreach ($speakers as $sp): ?>
                <div class="speaker-card">
                    <div class="speaker-info">
                        <h6><?php echo esc($sp['speaker_name']); ?></h6>
                        <p><?php echo esc(($sp['speaker_title'] ?? '').', '.($sp['speaker_company'] ?? '')); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="description">
            <?php echo nl2br(esc($session['description'] ?? 'No description available.')); ?>
        </div>
    </div>
</div>

<script>
function closeVideo() {
    // Stop video instantly
    const iframe = document.getElementById('vimeoPlayer');
    iframe.src = '';

    // Small fade-out before navigating back
    const modal = document.getElementById('videoModal');
    modal.style.transition = 'opacity 0.4s ease';
    modal.style.opacity = '0';
    setTimeout(() => {
        window.location.href = document.referrer || '<?php echo base_url('mobile/home'); ?>';
    }, 400);
}

// Optional: Allow "back" button on device to also close video
window.addEventListener('popstate', () => closeVideo());
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
