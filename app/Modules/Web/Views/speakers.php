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

.speakers-container h4 {
    color: #fff;
    font-weight: bold;
    margin-bottom: 60px;
}

/* Grid layout */
.speakers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 60px;
    justify-items: center;
}

/* Speaker card */
.speaker-card {
    display: flex;
    align-items: center;
    gap: 25px;
    background: transparent;
    transition: transform 0.4s ease;
}
.speaker-card:hover {
    transform: scale(1.03);
}

/* Speaker image */
.speaker-photo {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid transparent;
    background: radial-gradient(circle at 30% 30%, #9D0F82, #EFB11E);
    padding: 4px;
    transition: box-shadow 0.4s ease;
}
.speaker-card:hover .speaker-photo {
    box-shadow: 0 0 25px rgba(255, 255, 255, 0.4);
}

/* Speaker details */
.speaker-details h5 {
    color: #fff;
    font-weight: 600;
    margin: 0 0 5px 0;
}
.speaker-details p {
    color: #ddd;
    font-size: 14px;
    margin-bottom: 10px;
}

/* View profile button */
.view-profile-btn {
    background: linear-gradient(90deg, #9D0F82, #EFB11E);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 30px;
    padding: 8px 25px;
    transition: all .3s ease;
}
.view-profile-btn:hover {
    background: linear-gradient(90deg, #EFB11E, #9D0F82);
    transform: scale(1.05);
}

/* Profile modal */
.modal-content {
    border-radius: 15px;
    background: #fff;
    color: #333;
    padding: 25px;
}
.modal-header {
    border: none;
}
.modal-header .close {
    font-size: 30px;
    color: #9D0F82;
    opacity: 1;
}
.modal-body h5 {
    color: #9D0F82;
    font-weight: 700;
}
.modal-body p {
    color: #444;
}
.session-list li {
    margin-bottom: 6px;
    list-style-type: disc;
    margin-left: 20px;
}
</style>

<div class="speakers-container">
    <h4>SPEAKERS</h4>

    <div class="speakers-grid">
        <?php if (!empty($speakers)): ?>
            <?php foreach ($speakers as $speaker): ?>
                <?php
                    $image = base_url('uploads/speaker_pictures/' . $speaker['speaker_photo']);
                    $speakerId = $speaker['id'];
                ?>
                <div class="speaker-card">
                    <img src="<?php echo $image; ?>"
                         alt="<?php echo esc($speaker['speaker_name']); ?>"
                         class="speaker-photo"
                         onerror="this.src='<?php echo asset_url('images/user.png'); ?>';">

                    <div class="speaker-details">
                        <h5><?php echo esc($speaker['speaker_name']); ?></h5>
                        <p><?php echo esc($speaker['speaker_title'] . ', ' . $speaker['speaker_company']); ?></p>
                        <button class="view-profile-btn" data-toggle="modal" data-target="#speakerModal<?php echo $speakerId; ?>">
                            View Profile
                        </button>
                    </div>
                </div>

                <!-- 🗣️ Speaker Modal -->
                <div class="modal fade" id="speakerModal<?php echo $speakerId; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo esc($speaker['speaker_name']); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <img src="<?php echo $image; ?>"
                                             alt="<?php echo esc($speaker['speaker_name']); ?>"
                                             class="rounded-circle"
                                             style="width:150px;height:150px;object-fit:cover;">
                                    </div>
                                    <div class="col-md-8">
                                        <h5><?php echo esc($speaker['speaker_title']); ?></h5>
                                        <p><strong><?php echo esc($speaker['speaker_company']); ?></strong></p>
                                        <p><?php echo esc($speaker['bio'] ?? 'No biography available.'); ?></p>
                                        <?php if (!empty($speaker['sessions'])): ?>
                                            <h6 class="mt-3" style="color:#9D0F82;font-weight:bold;">Sessions:</h6>
                                            <ul class="session-list">
                                                <?php foreach ($speaker['sessions'] as $session): ?>
                                                    <li><?php echo esc($session['sessions_name']); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No speakers found.</p>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
