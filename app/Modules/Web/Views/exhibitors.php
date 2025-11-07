<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 01/11/2025
 * Time: 18:58
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<style>
    body {
        background: url('<?php echo asset_url('images/brain-Events7-Portal-2025.png'); ?>') no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
    }

    /* ===== Container & Title ===== */
    .exhibitors-container {
        padding: 120px 8%;
        min-height: 100vh;
    }

    .exhibitors-container h2 {
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        margin-bottom: 60px;
        letter-spacing: 1px;
    }

    /* ===== Grid Layout – 2 per row on desktop ===== */
    .exhibitors-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 70px;
        justify-items: center;
        align-items: start;
        width: 90%;
        margin: 0 auto;
    }

    @media (max-width: 992px) {
        .exhibitors-grid {
            grid-template-columns: 1fr; /* single column for smaller screens */
        }
    }

    /* ===== Exhibitor Card ===== */
    .exhibitor-card {
        position: relative;
        width: 100%;
        max-width: 700px; /* wider cards */
        border-radius: 20px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .exhibitor-card:hover {
        transform: scale(1.03);
        box-shadow: 0 0 35px rgba(239, 177, 30, 0.5);
    }

    /* ===== Embedded Video ===== */
    .exhibitor-card iframe {
        width: 100%;
        height: 400px; /* larger, cinematic feel */
        border: none;
        border-radius: 20px 20px 0 0;
    }

    /* ===== Overlayed Company Name ===== */
    .exhibitor-overlay {
        position: absolute;
        top: 0;
        left: 0;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.7), transparent);
        width: 100%;
        padding: 12px 20px;
        text-align: left;
        z-index: 2;
    }

    .exhibitor-overlay span {
        color: #FFFFFF;
        font-weight: 500;
        margin: 0;
        font-size: 16px;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
    }

    /* ===== Enter Booth Button ===== */
/*
    .enter-booth-btn {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: linear-gradient(90deg, #9D0F82, #EFB11E);
        color: #fff;
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        z-index: 2;
    }
*/

    .enter-booth-btn{
        background-image: url(<?php echo asset_url('images/button-bg.png')?>);
        background-repeat: repeat-y;
        width: 185px;
        color: #ffffff;
        border-radius: 15px;
        font-size: 15px;
        position: relative;
        bottom: 15px;
        left: 40%;
        border: none;
        padding: 8px 20px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .enter-booth-btn:hover {
        background: linear-gradient(90deg, #EFB11E, #9D0F82);
        transform: scale(1.05);
    }

    /* ===== Modal Styling ===== */
    .modal-content {
        border-radius: 15px;
        border: none;
        background: #fff;
        color: #333;
        box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-body h5 {
        font-weight: 700;
        color: #9D0F82;
    }

    .message-form input,
    .message-form textarea {
        border: 2px solid #9D0F82;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 14px;
    }

    .message-form input:focus,
    .message-form textarea:focus {
        border-color: #EFB11E;
        box-shadow: 0 0 5px rgba(239, 177, 30, 0.4);
        outline: none;
    }


    /* ===== Responsive Video Ratio Fix ===== */
    @media (max-width: 768px) {
        .exhibitor-card iframe {
            height: 300px;
        }

        .exhibitor-overlay h5 {
            font-size: 16px;
        }

        .enter-booth-btn {
            padding: 6px 16px;
            font-size: 12px;
        }
    }
</style>

<div class="exhibitors-container">
    <h2>EXHIBITORS</h2>

    <div class="exhibitors-grid">
        <?php if (!empty($exhibitors)): ?>
            <?php foreach ($exhibitors as $ex): ?>
                <div class="exhibitor-card">
                    <?php if (!empty($ex['vimeo_id'])): ?>
                        <iframe src="https://player.vimeo.com/video/<?php echo $ex['vimeo_id']; ?>?dnt=1&background=1&autoplay=1&loop=1&muted=1"
                                allow="autoplay; fullscreen"></iframe>
                    <?php else: ?>
                        <img src="<?php echo base_url('uploads/exhibitors_logos/'.$ex['logo']); ?>"
                             alt="<?php echo esc($ex['company_name']); ?>" style="width:100%;border-radius:20px 20px 0 0;">
                    <?php endif; ?>

                    <div class="exhibitor-overlay">
                        <span><?php echo esc($ex['company_name']); ?></span>
                    </div>

                    <!-- Button to trigger modal -->
                    <button class="enter-booth-btn" data-toggle="modal" data-target="#boothModal<?php echo $ex['id']; ?>">
                        Enter Booth
                    </button>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="boothModal<?php echo $ex['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">

                            <div class="modal-body p-4">
                                <div class="row align-items-center">
                                    <!-- Left: Video -->
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <?php if (!empty($ex['vimeo_id'])): ?>
                                            <iframe src="https://player.vimeo.com/video/<?php echo $ex['vimeo_id']; ?>"
                                                    width="100%" height="350" frameborder="0"
                                                    allow="autoplay; fullscreen" allowfullscreen></iframe>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Right: Contact info & message form -->
                                    <div class="col-md-6">
                                        <h5 class="mb-3 text-dark font-weight-bold"><?php echo esc($ex['company_name']); ?></h5>

                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?php echo asset_url('images/user.png'); ?>" alt="Avatar"
                                                 class="rounded-circle mr-3" style="width:70px;height:70px;">
                                            <div>
                                                <strong class="text-dark"><?php echo esc($ex['contact_person']); ?></strong><br>
                                                <span class="text-secondary"><?php echo esc($ex['email']); ?></span><br>
                                                <span class="text-secondary"><?php echo esc($ex['website']); ?></span>
                                            </div>
                                        </div>

                                        <form class="message-form">
                                            <input type="text" name="subject" class="form-control mb-2"
                                                   placeholder="Enter Subject" required>
                                            <textarea name="message" class="form-control mb-3"
                                                      rows="4" placeholder="Enter message here..." required></textarea>

                                            <input type="hidden" name="exhibitor_id" value="<?php echo $ex['id']; ?>">
                                            <input type="hidden" name="attendee_id_from" value="<?php echo session('attendee_id'); ?>">

                                            <button type="submit" class="btn btn-primary btn-block"
                                                    style="background:#9D0F82;border:none;">Send</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No exhibitors found.</p>
        <?php endif; ?>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
