<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/past-conferences'), 'activeTab' => 'me', 'title' => $conference['title'] ?? 'Past Conference']) ?>
<?php
$sessionsByDate = $sessionsByDate ?? [];
$isPremium      = (int) ($premium ?? 0) === 1;
$utc            = new DateTimeZone('UTC');
$confTitle      = trim((string) ($conference['title'] ?? 'Past Conference'));
$confYear       = (int) ($conference['year'] ?? 0);
if ($confYear > 0 && ! str_contains($confTitle, (string) $confYear)) {
    $confTitle .= ' ' . $confYear;
}
$dayNumber = 0;
$firstDate = array_key_first($sessionsByDate);
?>

<div class="m-screen m-screen--20">
    <div class="m-head">
        <a class="m-backlink" href="<?= site_url('mobile/past-conferences') ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK TO ARCHIVE</a>
        <span class="m-kicker">Past Conferences</span>
        <h1 class="m-title"><?= esc($confTitle) ?></h1>
        <span class="m-sub">Recorded sessions and archive access. All session times are shown in your local time.</span>
    </div>

    <?php if ($sessionsByDate): ?>
        <div class="m-seg m-seg--days" data-seg role="tablist" aria-label="Conference days">
            <?php foreach ($sessionsByDate as $date => $_): $dayNumber++; ?>
                <button type="button" role="tab" class="<?= $date === $firstDate ? 'is-active' : '' ?>" aria-selected="<?= $date === $firstDate ? 'true' : 'false' ?>" data-seg-target="pastday-<?= esc(str_replace('-', '', $date), 'attr') ?>">Day <?= $dayNumber ?></button>
            <?php endforeach; ?>
        </div>

        <div class="m-card m-card--clip">
            <?php foreach ($sessionsByDate as $date => $list): ?>
                <div class="m-agenda__day" id="pastday-<?= esc(str_replace('-', '', $date), 'attr') ?>"<?= $date === $firstDate ? '' : ' hidden' ?>>
                    <?php foreach ($list as $item): ?>
                        <?php
                        $start    = new DateTime($item['event_date'] . ' ' . $item['start_time'], $utc);
                        $end      = new DateTime($item['event_date'] . ' ' . $item['end_time'], $utc);
                        $vimeo    = preg_replace('/\D/', '', (string) ($item['vimeo_id'] ?? ''));
                        $access   = (int) ($item['access_level'] ?? 1) !== 2 || $isPremium;
                        $kind     = ! $access ? 'locked' : ($vimeo !== '' ? 'rec' : 'soon');
                        $speakers = !empty($item['speakers']) ? implode(', ', array_filter(array_column($item['speakers'], 'speaker_name'))) : '';
                        ?>
                        <a class="m-agenda__row" href="<?= $kind === 'locked' ? site_url('mobile/upgrade') : site_url('mobile/session/' . (int) $item['sessions_id']) ?>"
                           data-past="<?= $kind ?>" data-title="<?= esc($item['sessions_name'], 'attr') ?>" data-video="<?= $kind === 'rec' ? 'https://player.vimeo.com/video/' . $vimeo . '?dnt=1' : '' ?>">
                            <div class="m-agenda__time" data-local-time data-date="<?= esc($item['event_date'], 'attr') ?>" data-start="<?= esc($item['start_time'], 'attr') ?>" data-end="<?= esc($item['end_time'], 'attr') ?>">
                                <b data-lt-start><?= esc($start->format('g:i A')) ?></b>
                                <span data-lt-end><?= esc($end->format('g:i A')) ?></span>
                                <small data-lt-zone>GMT</small>
                            </div>
                            <div class="m-agenda__rail"><span class="m-agenda__dot m-agenda__dot--solid"></span></div>
                            <div class="m-agenda__body">
                                <span class="m-agenda__name"><?= esc($item['sessions_name']) ?></span>
                                <span class="m-agenda__speakers">Speakers: <?= esc($speakers !== '' ? $speakers : 'TBA') ?></span>
                                <?php if ($kind === 'rec'): ?>
                                    <span class="m-action m-action--rec"><i class="fa fa-play" aria-hidden="true"></i>Watch Recording</span>
                                <?php elseif ($kind === 'soon'): ?>
                                    <span class="m-action"><i class="fa fa-clock-o" aria-hidden="true"></i>Coming Soon</span>
                                <?php else: ?>
                                    <span class="m-action m-action--soft"><i class="fa fa-lock" aria-hidden="true"></i>Locked</span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="m-empty">No sessions found for this conference.</div>
    <?php endif; ?>
</div>

<aside class="m-sheet" data-sheet="recording" role="dialog" aria-modal="true" aria-label="Session recording" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-rec">
        <div class="m-rec__head"><b id="recTitle"></b><button class="m-x" type="button" data-close-sheet aria-label="Close">×</button></div>
        <div class="m-video m-video--12" id="recVideo" data-video-title="Session recording">
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
        var row = event.target.closest('[data-past]');
        if (!row) return;
        var kind = row.getAttribute('data-past');
        if (kind === 'locked') return;
        event.preventDefault();
        if (kind === 'soon') { EPR.toast('This recording is coming soon.'); return; }
        source = row.getAttribute('data-video');
        document.getElementById('recTitle').textContent = row.getAttribute('data-title');
        reset();
        EPR.openSheet('recording');
    });

    document.getElementById('recPlay').addEventListener('click', function () { EPR.playInto(holder, source); });
    sheet.addEventListener('sheet:close', reset);
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
