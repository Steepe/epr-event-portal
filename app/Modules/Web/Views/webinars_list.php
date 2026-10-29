<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$timezone = $timezone ?? 'Africa/Lagos';
?>

<main data-screen-label="Webinars" style="position:absolute;inset:0;overflow-y:auto;padding:112px 7vw 64px;box-sizing:border-box">
    <div class="rc-corner-logo" aria-hidden="true">
        <span class="rc-kicker" style="font-size:11px">EPR Global Webinars &middot; Bi-weekly</span>
        <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="">
    </div>

    <div style="display:flex;flex-direction:column;gap:8px;max-width:520px">
        <a href="<?php echo base_url('attendees/start'); ?>" class="rc-back-link">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK
        </a>
        <span style="font-size:40px;font-weight:700;letter-spacing:-.03em;line-height:1">Webinars</span>
        <span style="font-size:15px;line-height:1.55;color:rgba(255,255,255,.78);text-wrap:pretty">Short, practical sessions on cadence, metrics and accountability, with live Q&amp;A.</span>
    </div>

    <?php if (empty($webinars)): ?>
        <div class="rc-empty" style="margin-top:36px">No webinars available at this time.</div>
    <?php else: ?>
        <section class="rc-content-grid" style="margin-top:36px;padding-bottom:32px">
            <?php foreach ($webinars as $webinar): ?>
                <?php
                $isOpen = (int) ($webinar['is_open'] ?? 0) === 1;
                $isPast = (int) ($webinar['is_past'] ?? 0) === 1;
                $hasRecording = ! empty($webinar['vimeo_id']);
                $vimeoID = preg_replace('/\D/', '', (string) ($webinar['vimeo_id'] ?? ''));
                $start = ! empty($webinar['start_time']) ? date('h:i A', strtotime($webinar['start_time'])) : '';
                $end = ! empty($webinar['end_time']) ? date('h:i A', strtotime($webinar['end_time'])) : '';
                $date = ! empty($webinar['event_date']) ? date('l, F j, Y', strtotime($webinar['event_date'])) : 'Date to be announced';
                $tags = array_filter(array_map('trim', explode(',', (string) ($webinar['tags'] ?? ''))));
                ?>
                <article class="rc-booth-card" style="padding:24px;display:flex;flex-direction:column;gap:18px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:18px">
                        <span style="width:64px;height:64px;border-radius:20px;background:rgba(239,177,30,.14);color:#efb11e;display:flex;align-items:center;justify-content:center;font-size:27px">
                            <i class="fa fa-video-camera" aria-hidden="true"></i>
                        </span>
                        <span class="rc-kicker" style="font-size:10px;letter-spacing:.16em"><?php echo $isOpen && ! $isPast ? 'LIVE ACCESS' : ($isPast ? 'REPLAY' : 'LOCKED'); ?></span>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:8px">
                        <span style="font-size:20px;font-weight:700;letter-spacing:-.02em"><?php echo esc($webinar['event_name'] ?? ''); ?></span>
                        <span style="font-size:13px;color:rgba(255,255,255,.72)">
                            <?php echo esc($date); ?><?php echo $start ? ' · ' . esc($start . ' - ' . $end . ' ' . $timezone) : ''; ?>
                        </span>
                    </div>

                    <?php if (! empty($tags)): ?>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <?php foreach ($tags as $tag): ?>
                                <span style="font-size:11px;color:#efb11e;border:1px solid rgba(239,177,30,.38);border-radius:999px;padding:5px 9px"><?php echo esc($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top:auto;display:flex;justify-content:flex-end">
                        <?php if ($isOpen && ! $isPast && ! empty($webinar['zoom_link'])): ?>
                            <a href="<?php echo esc($webinar['zoom_link']); ?>" target="_blank" rel="noopener" class="rc-pill rc-grad-pill" style="text-decoration:none;height:42px">
                                <i class="fa fa-sign-in" aria-hidden="true"></i>Join Webinar
                            </a>
                        <?php elseif ($isPast && $hasRecording && $vimeoID !== ''): ?>
                            <button type="button" class="rc-pill rc-grad-pill" data-toggle="modal" data-target="#vimeoModal<?php echo (int) ($webinar['event_id'] ?? 0); ?>" style="border:0;height:42px">
                                <i class="fa fa-play" aria-hidden="true"></i>Watch Recording
                            </button>
                        <?php else: ?>
                            <span class="rc-outline-pill" style="color:#efb11e;border-color:rgba(239,177,30,.45)">
                                <i class="fa fa-lock" aria-hidden="true"></i>Locked
                            </span>
                        <?php endif; ?>
                    </div>
                </article>

                <?php if ($isPast && $hasRecording && $vimeoID !== ''): ?>
                    <div class="modal fade" id="vimeoModal<?php echo (int) ($webinar['event_id'] ?? 0); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content" style="background:#260029!important;border-radius:16px!important;border:1px solid rgba(255,255,255,.12)">
                                <div class="modal-header border-0">
                                    <h6 class="modal-title" style="color:#fff"><?php echo esc($webinar['event_name'] ?? ''); ?></h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body p-0">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe class="embed-responsive-item"
                                                src="https://player.vimeo.com/video/<?php echo esc($vimeoID); ?>?muted=1&amp;dnt=1"
                                                allow="autoplay; fullscreen; picture-in-picture"
                                                allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    $('.modal').on('hidden.bs.modal', function () {
        const iframe = $(this).find('iframe');
        if (iframe.length) {
            const src = iframe.attr('src');
            iframe.attr('src', '');
            iframe.attr('src', src);
        }
    });
});
</script>
</body>
</html>
