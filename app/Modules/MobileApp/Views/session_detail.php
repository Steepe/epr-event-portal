<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:56
 */


 echo module_view('MobileApp', 'includes/header');

$event           = $session ?? [];
$sessionSpeakers = $speakers ?? [];
$timezone        = $timezone ?? 'Africa/Lagos';
$plan            = (int)(session('plan') ?? 1);

// 🧩 Access control (force integer)
$accessLevel = (int)($event['access_level'] ?? 1);
$canAccess   = $plan >= $accessLevel;

$vimeo_id  = trim($event['vimeo_id'] ?? '');
$videoSrc  = '';

if (!empty($vimeo_id)) {
    if (is_numeric($vimeo_id)) {
        $videoSrc = "https://player.vimeo.com/video/" . $vimeo_id;
    } elseif (str_contains($vimeo_id, 'event')) {
        $videoSrc = "https://vimeo.com/{$vimeo_id}/embed";
    } else {
        $videoSrc = "https://vimeo.com/event/{$vimeo_id}/embed";
    }
}

// 🕓 Session Timing
$startTime = isset($event['start_time']) ? date('h:i A', strtotime($event['start_time'])) : null;
$endTime   = isset($event['end_time']) ? date('h:i A', strtotime($event['end_time'])) : null;
$eventDate = isset($event['event_date']) ? date('F j, Y', strtotime($event['event_date'])) : null;
?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        color: #fff;
        background: radial-gradient(circle at center, #24002f 0%, #0a0010 100%);
        overflow-x: hidden;
    }

    .overlay-gradient {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: linear-gradient(120deg, rgba(157,15,130,0.3), rgba(255,180,0,0.25));
        z-index: -1;
    }

    .session-container {
        padding: 1.5rem;
        text-align: center;
        max-width: 600px;
        margin: 50px auto auto;
    }

    h4 {
        color: #f3bb1a;
        margin-bottom: 0.4rem;
        font-weight: 700;
        text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        font-size: 1.4rem;
    }

    .session-meta {
        font-size: 0.85rem;
        color: #ddd;
        margin-bottom: 1rem;
    }

    .session-meta span {
        display: block;
        line-height: 1.4;
    }

    /* 🎥 Video Box */
    .video-wrapper {
        position: relative;
        width: 100%;
        padding-top: 56.25%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        margin-bottom: 1.5rem;
    }

    iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: none;
        border-radius: 16px;
    }

    .locked-video {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255,216,77,0.3);
        border-radius: 16px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 500;
        text-shadow: 0 2px 6px rgba(0,0,0,0.3);
        margin-bottom: 1.5rem;
    }

    .locked-video i {
        font-size: 1.2rem;
        margin-right: 8px;
        color: #f3bb1a;
    }

    .feedback-row {
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }

    .like-icon i {
        color: #f3bb1a;
        font-size: 1.2rem;
        cursor: pointer;
        transition: color 0.3s;
    }

    .like-icon i.filled {
        color: #E91E63;
    }

    .rating-stars {
        direction: rtl;
        display: flex;
        gap: 4px;
    }

    .rating-stars input { display: none; }
    .rating-stars label {
        font-size: 1.1rem;
        color: #777;
        cursor: pointer;
        transition: color 0.3s;
    }

    .rating-stars input:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #f3bb1a;
    }

    .speaker-section {
        margin-top: 1.5rem;
        text-align: left;
    }

    .speaker-card {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 14px;
        padding: 10px;
        margin-bottom: 0.8rem;
    }

    .speaker-card img {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
        margin-right: 10px;
    }

    .speaker-info h6 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #f3bb1a;
        margin-bottom: 2px;
    }

    .speaker-info p {
        font-size: 0.8rem;
        margin: 0;
        color: #ddd;
    }

    .session-overview {
        background: rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 1rem;
        margin-top: 1.5rem;
        text-align: left;
    }

    .session-overview h6 {
        color: #f3bb1a;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .session-overview p {
        font-size: 0.9rem;
        color: #fff;
        line-height: 1.5;
    }

    .btn-download {
        display: inline-block;
        margin-top: 1rem;
        color: #fff;
        border: none;
        border-radius: 30px;
        padding: 10px 20px;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        transition: transform 0.2s ease;
        width: 200px;
    }

    .btn-download:hover {
        transform: scale(1.05);
    }

    @media (max-width: 480px) {
        h4 { font-size: 1.2rem; }
        .speaker-card img { width: 60px; height: 60px; }
    }
</style>

<div class="overlay-gradient"></div>

<div class="session-container">
    <!-- 🏷️ Title + Date/Time -->
    <h4><?php echo esc($event['sessions_name'] ?? 'Session'); ?></h4>

    <?php if ($eventDate || $startTime): ?>
        <div class="session-meta">
            <?php if ($eventDate): ?><span><?php echo esc($eventDate); ?></span><?php endif; ?>
            <?php if ($startTime && $endTime): ?><span><?php echo "{$startTime} - {$endTime} ({$timezone})"; ?></span><?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 🎥 Video -->
    <?php if ($canAccess && !empty($videoSrc)): ?>
        <div class="video-wrapper">
            <iframe src="<?php echo esc($videoSrc); ?>"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen></iframe>
        </div>
    <?php elseif (empty($videoSrc)): ?>
        <div class="locked-video">
            <i class="fa fa-info-circle"></i> This session does not have a video available yet.
        </div>
    <?php else: ?>
        <div class="locked-video">
            <i class="fa fa-lock"></i> Please upgrade your ticket to access this session.
        </div>
    <?php endif; ?>

    <!-- 💬 Feedback & Like -->
    <div class="feedback-row">
        <div class="like-icon" id="likeSession">
            <i class="fa fa-heart-o"></i> <span>Like</span>
        </div>
        <div class="rating-stars">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>">
                <label for="star<?php echo $i; ?>">★</label>
            <?php endfor; ?>
        </div>
    </div>

    <!-- 👥 Speakers -->
    <?php if (!empty($sessionSpeakers)): ?>
        <div class="speaker-section">
            <h6 style="color:#f3bb1a; margin-bottom:8px;">Speakers</h6>
            <?php foreach ($sessionSpeakers as $speaker): ?>
                <div class="speaker-card">
                    <img src="<?php echo base_url('uploads/speaker_pictures/' . ($speaker['speaker_photo'] ?? '')); ?>"
                         onerror="this.src='<?php echo asset_url('images/user.png'); ?>';"
                         alt="<?php echo esc($speaker['speaker_name']); ?>">
                    <div class="speaker-info">
                        <h6><?php echo esc($speaker['speaker_name']); ?></h6>
                        <p><?php echo esc(($speaker['speaker_title'] ?? '') . ', ' . ($speaker['speaker_company'] ?? '')); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 📝 Session Overview -->
    <div class="session-overview">
        <h6>Overview</h6>
        <p><?php echo nl2br(esc($event['description'] ?? 'No description available.')); ?></p>

        <?php if (!empty($event['workbook'])): ?>
            <a href="<?php echo base_url('uploads/workbooks/' . $event['workbook']); ?>" download class="btn-download epr-btn-four">
                Download Workbook
            </a>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
