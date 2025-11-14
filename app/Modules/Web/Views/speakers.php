<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 31/10/2025
 * Time: 07:16
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
body {
    background: url('<?php echo asset_url('images/brain-Events5-Portal-2025.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    overflow-x: hidden;
}

.speakers-container {
    padding: 100px 10%;
    min-height: 100vh;
    color: #fff;
}

.speakers-container h3 {
    font-weight: 700;
    letter-spacing: 1px;
    text-align: center;
}

/* ✅ Fixed 2-column grid */
.speakers-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 60px 80px;
    justify-items: center;
    align-items: start;
}

/* Speaker card */
.speaker-card {
    background: rgba(255, 255, 255, 0.08);
    border: 2px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    text-align: center;
    padding: 40px 25px;
    width: 100%;
    max-width: 480px;
    transition: transform 0.35s ease, box-shadow 0.35s ease;
    backdrop-filter: blur(5px);
}

.speaker-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(157, 15, 130, 0.4);
    background: rgba(255, 255, 255, 0.15);
}

.speaker-photo {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid transparent;
    padding: 4px;
    transition: box-shadow 0.3s ease;
}

.speaker-card:hover .speaker-photo {
    box-shadow: 0 0 25px rgba(239, 177, 30, 0.8);
}

.speaker-details {
    margin-top: 20px;
}

.speaker-details h5 {
    color: #fff;
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 18px;
}

.speaker-details p {
    color: #ccc;
    font-size: 14px;
    margin-bottom: 18px;
}

/* View profile button */
.view-profile-btn {
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 25px;
    padding: 8px 28px;
    font-size: 13px;
    transition: all .3s ease;
}

.view-profile-btn:hover {
    transform: scale(1.05);
}

/* =================== SPEAKER PROFILE MODAL =================== */
.speaker-profile-modal {
    border-radius: 20px;
    overflow: hidden;
    border: none;
    background: #fff;
    color: #333;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
}

.profile-header-bg {
    height: 180px;
    background: url('<?php echo asset_url('images/speaker-modal-bg.png'); ?>'),
    background-size: cover;
    background-position: center;
    position: relative;
}

.speaker-photo-container {
    position: relative;
    margin-top: -85px;
}

.speaker-photo-lg {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
    border: 6px solid #fff;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
}

.speaker-name {
    color: #9D0F82;
    font-weight: 700;
    font-size: 22px;
    margin-top: 15px;
}

.speaker-title {
    color: #555;
    font-size: 15px;
}

/* Tabs */
.profile-tabs {
    border: none;
    justify-content: center;
    margin-top: 25px;
}
.profile-tabs .nav-link {
    border: 2px solid #9D0F82;
    color: #9D0F82;
    font-weight: 600;
    border-radius: 8px 8px 0 0;
    margin: 0 5px;
    padding: 8px 25px;
    background: #fff;
    transition: all .3s ease;
}
.profile-tabs .nav-link.active {
    background-color: #9D0F82;
    color: #fff;
    border-color: #9D0F82;
}

/* About Section */
.about-content {
    background: #fff;
    border: 2px solid #9D0F82;
    border-radius: 12px;
    padding: 25px 30px;
    margin-top: 15px;
    font-size: 15px;
    line-height: 1.7;
    color: #444;
}

.text-epr {
    color: #9D0F82;
    font-weight: bold;
}

.profile-close-btn {
    font-size: 32px;
    color: #9D0F82;
    opacity: 1;
    position: absolute;
    right: 20px;
    top: 10px;
    z-index: 100;
}

/* Sessions list */
.session-list {
    padding-left: 25px;
}
.session-list li {
    margin-bottom: 5px;
}

/* Responsive */
@media (max-width: 992px) {
    .speakers-container { padding: 80px 6%; }
    .speakers-grid { grid-template-columns: 1fr; gap: 40px; }
    .speaker-photo-lg { width: 120px; height: 120px; }
    .speaker-name { font-size: 18px; }
    .speaker-title { font-size: 14px; }
}
</style>

<div class="speakers-container">
    <h4>SPEAKERS</h4>

    <div class="speakers-grid">
        <?php if (!empty($speakers)): ?>
            <?php foreach ($speakers as $speaker): ?>
                <?php
                    $image = base_url('uploads/speaker_pictures/' . $speaker['speaker_photo']);
                    $speakerId = $speaker['speaker_id'];
                ?>
                <div class="speaker-card">
                    <img src="<?php echo $image; ?>"
                         alt="<?php echo esc($speaker['speaker_name']); ?>"
                         class="speaker-photo"
                         onerror="this.src='<?php echo asset_url('images/user.png'); ?>';">

                    <div class="speaker-details">
                        <h5><?php echo esc($speaker['speaker_name']); ?></h5>
                        <p><?php echo esc($speaker['speaker_title']); ?><br>
                           <small><?php echo esc($speaker['speaker_company']); ?></small></p>

                        <button class="view-profile-btn epr-btn-two"
                                data-toggle="modal"
                                data-target="#speakerModal<?php echo $speakerId; ?>">
                            View Profile
                        </button>
                    </div>
                </div>

                <!-- 🗣️ Speaker Modal -->
                <div class="modal fade" id="speakerModal<?php echo $speakerId; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content speaker-profile-modal">
                            <div class="modal-header border-0 p-0">
                                <button type="button" class="close profile-close-btn" data-dismiss="modal" aria-label="Close">&times;</button>
                            </div>

                            <div class="modal-body text-center">
                                <!-- Gradient Header -->
                                <div class="profile-header-bg"></div>

                                <!-- Speaker Photo -->
                                <div class="speaker-photo-container">
                                    <img src="<?php echo $image; ?>"
                                         alt="<?php echo esc($speaker['speaker_name']); ?>"
                                         class="speaker-photo-lg"
                                         onerror="this.src='<?php echo asset_url('images/user.png'); ?>';">
                                </div>

                                <!-- Speaker Info -->
                                <h3 class="speaker-name"><?php echo esc($speaker['speaker_name']); ?></h3>
                                <p class="speaker-title mb-3"><?php echo esc($speaker['speaker_title']); ?>, <?php echo esc($speaker['speaker_company']); ?></p>

                                <!-- Tabs -->
                                <ul class="nav nav-tabs profile-tabs" id="profileTabs<?php echo $speakerId; ?>" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="about-tab-<?php echo $speakerId; ?>" data-toggle="tab" href="#about<?php echo $speakerId; ?>" role="tab">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="messages-tab-<?php echo $speakerId; ?>" data-toggle="tab" href="#messages<?php echo $speakerId; ?>" role="tab">Messages</a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4 text-left" id="profileTabsContent<?php echo $speakerId; ?>" style="position: relative; top: -26px;">
                                    <!-- About Tab -->
                                    <div class="tab-pane fade show active" id="about<?php echo $speakerId; ?>" role="tabpanel">
                                        <div class="about-content">
                                            <p><?php echo nl2br(esc($speaker['bio'] ?? 'No biography available.')); ?></p>
                                            <?php if (!empty($speaker['sessions'])): ?>
                                                <h6 class="mt-4 text-epr">Sessions:</h6>
                                                <ul class="session-list">
                                                    <?php foreach ($speaker['sessions'] as $session): ?>
                                                        <li><?php echo esc($session['sessions_name']); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Messages Tab -->
                                    <div class="tab-pane fade" id="messages<?php echo $speakerId; ?>" role="tabpanel">
                                        <div class="about-content text-center">
                                            <p class="text-muted">Messaging feature coming soon...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-white-50">No speakers found.</p>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
