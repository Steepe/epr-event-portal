<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 07:33
 */

 echo module_view('MobileApp', 'includes/header'); ?>

<style>
body {
    font-family: 'Inter', 'Poppins', sans-serif;
    color: #fff;
    overflow-x: hidden;
}
#bgVideo {
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover; filter: blur(10px) brightness(0.5);
    z-index: -2;
}
#videoOverlay {
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: -1;
}
.container {
    padding: 40px 1.5rem 120px;
    text-align: center;
    position: relative;
    z-index: 2;
}
h3 {
    color: #ffffff;
    font-weight: 700;
    margin-bottom: 1.5rem;
}
.session-card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 14px;
    padding: 1rem;
    margin-bottom: 1rem;
    transition: transform 0.25s ease;
}
.session-card:hover { transform: translateY(-3px); }
.session-title { color: #ffffff; font-weight: 600; font-size: 1rem; }
.session-meta { font-size: 0.8rem; opacity: 0.8; margin-bottom: 6px; }
.btn-watch {
    border: none; color: #fff;
    border-radius: 30px;
    padding: 8px 18px;
    font-weight: 600;
    font-size: 0.85rem;
}
.btn-watch:hover { transform: scale(1.05); }
</style>

<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/mobile-brain-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="container">
    <h3><?php echo esc($conference['title'] ?? 'Past Conference'); ?></h3>

    <?php if (empty($sessionsByDate)): ?>
        <p>No sessions found for this conference.</p>
    <?php else: ?>
        <?php foreach ($sessionsByDate as $date => $sessions): ?>
            <h5 style="color:#fff; font-weight:500; margin-top:25px;"><?php echo date('F j, Y', strtotime($date)); ?></h5>
            <?php foreach ($sessions as $s): ?>
                <div class="session-card">
                    <div class="session-title"><?php echo esc($s['sessions_name']); ?></div>
                    <div class="session-meta">
                        <?php echo date('h:i A', strtotime($s['start_time'])).' - '.date('h:i A', strtotime($s['end_time'])); ?>
                    </div>
                    <button class="btn-watch epr-btn-two"
                            onclick="openSession('<?php echo base_url('mobile/pastConference/session/'.$s['sessions_id']); ?>')">
                        Watch Session
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function openSession(url) {
    window.location = url;
}
</script>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
