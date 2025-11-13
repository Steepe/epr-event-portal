<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 07:24
 */

 echo module_view('MobileApp', 'includes/header_less_menu'); ?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', 'Poppins', sans-serif;
    color: #fff;
    background: radial-gradient(circle at center, #6a0080 0%, #150020 100%);
    overflow-x: hidden;
}
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
#videoOverlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: -1;
}
.container {
    position: relative;
    z-index: 2;
    padding: 100px 1.5rem 120px;
    text-align: center;
}
h3 {
    color: #f3bb1a;
    font-weight: 700;
    margin-bottom: 1.5rem;
}
.session-card {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 14px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 0 20px rgba(255,255,255,0.1);
    transition: transform .25s ease;
}
.session-card:hover {
    transform: translateY(-3px);
}
.session-title {
    color: #f3bb1a;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 5px;
}
.session-meta {
    font-size: 0.8rem;
    opacity: 0.85;
}
.btn-purple {
    color: #fff;
    border: none;
    border-radius: 30px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 0.85rem;
    margin-top: 10px;
    text-decoration: none;
    display: inline-block;
}
.btn-purple:hover {
    transform: scale(1.05);
}
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/start-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="container">
    <h3><?php echo esc($conference['title'] ?? 'Past Conference Sessions'); ?></h3>

    <?php if (empty($sessionsByDate)): ?>
        <p style="opacity: 0.85;">No sessions available for this conference.</p>
    <?php else: ?>
        <?php foreach ($sessionsByDate as $date => $sessions): ?>
            <h5 style="color:#fff; font-weight:500; margin-top:25px;"><?php echo date('F j, Y', strtotime($date)); ?></h5>
            <?php foreach ($sessions as $s): ?>
                <div class="session-card">
                    <div class="session-title"><?php echo esc($s['sessions_name']); ?></div>
                    <div class="session-meta">
                        <?php
                            $start = date('h:i A', strtotime($s['start_time']));
                            $end   = date('h:i A', strtotime($s['end_time']));
                        ?>
                        <span><?php echo $start . ' - ' . $end; ?></span>
                    </div>
                    <a href="<?php echo base_url('mobile/session/' . $s['sessions_id']); ?>" class="btn-purple">
                        View Session
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
