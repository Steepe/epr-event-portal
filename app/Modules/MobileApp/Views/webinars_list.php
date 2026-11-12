<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/welcome'), 'activeTab' => 'me', 'title' => 'Webinars']) ?>
<?php $timezone = $timezone ?? 'Africa/Lagos'; ?>

<div class="m-screen">
    <div class="m-head m-head--8">
        <a class="m-backlink" href="<?= site_url('mobile/welcome') ?>" data-back="<?= site_url('mobile/welcome') ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK</a>
        <h1 class="m-title">Webinars</h1>
        <span class="m-sub" style="font-size:14px;line-height:1.55;color:rgba(255,255,255,.78)">Short, practical sessions on cadence, metrics and accountability, with live Q&amp;A.</span>
    </div>

    <?php foreach (($webinars ?? []) as $webinar): ?>
        <?php
        $isOpen  = (int) ($webinar['is_open'] ?? 0) === 1;
        $isPast  = (int) ($webinar['is_past'] ?? 0) === 1;
        $vimeo   = preg_replace('/\D/', '', (string) ($webinar['vimeo_id'] ?? ''));
        $zoom    = trim((string) ($webinar['zoom_link'] ?? ''));
        $kind    = ($isOpen && ! $isPast && $zoom !== '') ? 'open' : (($isPast && $vimeo !== '') ? 'past' : 'locked');
        $hasDate = !empty($webinar['event_date']) && strtotime((string) $webinar['event_date']) > 0;
        $when    = $hasDate ? date('l, F j, Y', strtotime($webinar['event_date'])) : 'Date to be announced';
        if ($hasDate && !empty($webinar['start_time'])) {
            $when .= ' · ' . date('h:i A', strtotime($webinar['start_time']));
            if (!empty($webinar['end_time'])) {
                $when .= ' - ' . date('h:i A', strtotime($webinar['end_time']));
            }
            $when .= ' ' . $timezone;
        }
        $tags = array_values(array_filter(array_map('trim', explode(',', (string) ($webinar['tags'] ?? '')))));
        ?>
        <article class="m-card m-card--20 m-webinar">
            <div class="m-webinar__top">
                <span class="m-webinar__icon"><i class="fa fa-video-camera" aria-hidden="true"></i></span>
                <span class="m-webinar__state"><?= $kind === 'open' ? 'LIVE ACCESS' : ($kind === 'past' ? 'REPLAY' : 'LOCKED') ?></span>
            </div>
            <div class="m-webinar__copy">
                <b><?= esc($webinar['event_name'] ?? 'Webinar') ?></b>
                <span><?= esc($when) ?></span>
            </div>
            <div class="m-webinar__tags"><?php foreach ($tags as $tag): ?><span><?= esc($tag) ?></span><?php endforeach; ?></div>
            <div class="m-webinar__foot">
                <?php if ($kind === 'open'): ?>
                    <a class="m-webinar__act" href="<?= esc($zoom, 'attr') ?>" target="_blank" rel="noopener"><i class="fa fa-sign-in" aria-hidden="true"></i>Join Webinar</a>
                <?php elseif ($kind === 'past'): ?>
                    <button class="m-webinar__act" type="button" data-webinar-video="https://player.vimeo.com/video/<?= $vimeo ?>" data-title="<?= esc($webinar['event_name'] ?? 'Webinar', 'attr') ?>"><i class="fa fa-play" aria-hidden="true"></i>Watch Recording</button>
                <?php else: ?>
                    <button class="m-webinar__act is-locked" type="button" data-webinar-locked><i class="fa fa-lock" aria-hidden="true"></i>Locked</button>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (empty($webinars)): ?>
        <div class="m-empty">No webinars available at this time.</div>
    <?php endif; ?>
</div>

<aside class="m-sheet" data-sheet="recording" role="dialog" aria-modal="true" aria-label="Webinar recording" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-rec">
        <div class="m-rec__head"><b id="recTitle"></b><button class="m-x" type="button" data-close-sheet aria-label="Close">×</button></div>
        <div class="m-video m-video--12" id="recVideo" data-video-title="Webinar recording">
            <img class="m-video__ghost" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="" style="width:40%;opacity:.2" data-video-hide>
            <button class="m-play m-play--56" type="button" id="recPlay" data-video-hide aria-label="Play recording"><i class="fa fa-play" aria-hidden="true"></i></button>
            <span class="m-video__tag m-video__tag--bl" data-video-hide>RECORDING</span>
        </div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sheet = document.querySelector('.m-sheet[data-sheet="recording"]');
    var holder = document.getElementById('recVideo');
    var source = '';

    function reset() {
        var frame = holder.querySelector('iframe'); if (frame) frame.remove();
        Array.prototype.forEach.call(holder.querySelectorAll('[data-video-hide]'), function (el) { el.hidden = false; });
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-webinar-locked]')) { EPR.toast('This webinar is not open yet.'); return; }
        var button = event.target.closest('[data-webinar-video]');
        if (!button) return;
        source = button.getAttribute('data-webinar-video');
        document.getElementById('recTitle').textContent = button.getAttribute('data-title');
        reset();
        EPR.openSheet('recording');
    });

    document.getElementById('recPlay').addEventListener('click', function () { EPR.playInto(holder, source); });
    sheet.addEventListener('sheet:close', reset);
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
