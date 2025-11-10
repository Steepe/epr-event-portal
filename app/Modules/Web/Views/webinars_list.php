<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 20:30
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/start_topbar');

$timezone = $timezone ?? 'Africa/Lagos';
?>

<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #fafafa;
}

h4.text-epr {
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #9D0F82;
}

.webinar-card {
    background: #fff;
    border: 5px solid #9D0F82;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all .3s ease;
}
.webinar-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 18px rgba(157, 15, 130, 0.25);
}
.webinar-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}
.webinar-meta {
    font-size: 13px;
    color: #666;
}
.btn-epr-purple {
    background-color: #9D0F82;
    color: white;
    border-radius: 6px;
    font-size: 13px;
    padding: 6px 14px;
    border: none;
}
.btn-epr-purple:hover {
    background-color: #7a0e69;
}
.btn-light {
    color: #aaa;
    border-radius: 6px;
    font-size: 13px;
}
.badge-status {
    background-color: #f8e9f5;
    color: #9D0F82;
    font-size: 11px;
    padding: 3px 7px;
    border-radius: 6px;
}
.modal-content {
    border-radius: 12px;
    background-color: #000;
    color: #fff;
}
.modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.modal-title {
    color: #fff;
    font-weight: 600;
}
.close {
    color: #fff;
    opacity: 0.9;
}
.close:hover {
    opacity: 1;
}
</style>

<div class="container py-5">
    <h4 class="text-epr mb-4">EPR Webinars</h4>

    <?php if (empty($webinars)): ?>
        <p class="text-center text-muted mt-5">No webinars available at this time.</p>
    <?php else: ?>
        <?php foreach ($webinars as $webinar): ?>
            <?php
                $isOpen = (int)$webinar['is_open'] === 1;
                $isPast = (int)$webinar['is_past'] === 1;
                $hasRecording = !empty($webinar['vimeo_id']);
                $vimeoID = preg_replace('/\D/', '', $webinar['vimeo_id']); // ensure numeric
                $start = $webinar['start_time'] ? date('h:i A', strtotime($webinar['start_time'])) : '';
                $end   = $webinar['end_time'] ? date('h:i A', strtotime($webinar['end_time'])) : '';
                $date  = date('l, F j, Y', strtotime($webinar['event_date']));
            ?>
            <div class="webinar-card">
                <div class="d-flex justify-content-between flex-wrap">
                    <div>
                        <div class="webinar-title"><?= esc($webinar['event_name']); ?></div>
                        <div class="webinar-meta"><?= $date; ?><?= $start ? " | $start - $end" : ''; ?></div>
                        <?php if (!empty($webinar['tags'])): ?>
                            <div class="mt-1">
                                <?php foreach (explode(',', $webinar['tags']) as $tag): ?>
                                    <span class="badge-status me-1"><?= trim($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3 mt-md-0 text-md-end">
                        <?php if ($isOpen && !$isPast): ?>
                            <a href="<?= esc($webinar['zoom_link']); ?>" target="_blank" class="btn btn-epr-purple">
                                <i class="bi bi-camera-video"></i> Join Webinar
                            </a>
                        <?php elseif ($isPast && $hasRecording): ?>
                            <button class="btn btn-epr-purple"
                                    data-toggle="modal"
                                    data-target="#vimeoModal<?= $webinar['event_id']; ?>">
                                <i class="bi bi-play-circle"></i> Watch Recording
                            </button>
                        <?php else: ?>
                            <button class="btn btn-light" disabled>Locked</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- 🎥 Vimeo Modal -->
            <?php if ($isPast && $hasRecording): ?>
            <div class="modal fade" id="vimeoModal<?= $webinar['event_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content bg-dark text-white">
                        <div class="modal-header border-0">
                            <h6 class="modal-title"><?= esc($webinar['event_name']); ?></h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe
                                    class="embed-responsive-item"
                                    src="https://player.vimeo.com/video/<?= esc($vimeoID); ?>?autoplay=1&muted=1&dnt=1"
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

<?php echo module_view('Web', 'includes/scripts'); ?>

<!-- Ensure jQuery + Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$('.modal').on('hidden.bs.modal', function () {
    const iframe = $(this).find('iframe');
    if (iframe.length) iframe.attr('src', iframe.attr('src')); // reset video on close
});
</script>

</body>
</html>
