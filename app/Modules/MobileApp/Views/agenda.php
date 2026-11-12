<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => false, 'activeTab' => 'agenda', 'title' => 'Agenda', 'screenLabel' => 'Agenda']) ?>
<?php
$sessionsByDate = [];
foreach (($sessions ?? []) as $item) {
    $sessionsByDate[$item['event_date']][] = $item;
}
$isPremium       = (int) ($premium ?? 0) === 1;
$conferenceLabel = trim(($conference['title'] ?? 'Accelerate') . ' ' . ($conference['year'] ?? ''));
$utc             = new DateTimeZone('UTC');
$now             = new DateTime('now', $utc);
$today           = $now->format('Y-m-d');
$activeDate      = isset($sessionsByDate[$today]) ? $today : array_key_first($sessionsByDate);
?>

<div class="m-screen m-screen--20">
    <div class="m-head">
        <span class="m-kicker"><?= esc($conferenceLabel) ?></span>
        <h1 class="m-title">Agenda</h1>
        <span class="m-sub">All session times are shown in your local time.</span>
    </div>

    <?php if ($sessionsByDate): ?>
        <div class="m-seg m-seg--days" data-seg role="tablist" aria-label="Agenda days">
            <?php foreach ($sessionsByDate as $date => $_): ?>
                <button type="button" role="tab" class="<?= $date === $activeDate ? 'is-active' : '' ?>" aria-selected="<?= $date === $activeDate ? 'true' : 'false' ?>" data-seg-target="day-<?= esc(str_replace('-', '', $date), 'attr') ?>"><?= esc(date('M j', strtotime($date))) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="m-card m-card--clip">
            <?php foreach ($sessionsByDate as $date => $list): ?>
                <div class="m-agenda__day" id="day-<?= esc(str_replace('-', '', $date), 'attr') ?>"<?= $date === $activeDate ? '' : ' hidden' ?>>
                    <?php foreach ($list as $item): ?>
                        <?php
                        $start    = new DateTime($item['event_date'] . ' ' . $item['start_time'], $utc);
                        $end      = new DateTime($item['event_date'] . ' ' . $item['end_time'], $utc);
                        $live     = $now >= $start && $now < $end;
                        $done     = $now >= $end;
                        $locked   = (int) ($item['access_level'] ?? 1) === 2 && ! $isPremium;
                        $speakers = !empty($item['speakers']) ? implode(', ', array_filter(array_column($item['speakers'], 'speaker_name'))) : '';
                        $url      = $locked ? site_url('mobile/upgrade') : site_url('mobile/session/' . $item['sessions_id']);
                        ?>
                        <a class="m-agenda__row <?= $live ? 'is-live' : ($done ? 'is-done' : '') ?>" href="<?= $url ?>">
                            <div class="m-agenda__time" data-local-time data-date="<?= esc($item['event_date'], 'attr') ?>" data-start="<?= esc($item['start_time'], 'attr') ?>" data-end="<?= esc($item['end_time'], 'attr') ?>">
                                <b data-lt-start><?= esc($start->format('g:i A')) ?></b>
                                <span data-lt-end><?= esc($end->format('g:i A')) ?></span>
                                <small data-lt-zone>GMT</small>
                            </div>
                            <div class="m-agenda__rail"><span class="m-agenda__dot"></span></div>
                            <div class="m-agenda__body">
                                <div class="m-agenda__flags"><?php if ($live): ?><span class="m-live">LIVE</span><?php endif; ?><?php if ($locked): ?><span class="m-paid"><i class="fa fa-lock" aria-hidden="true"></i>Paid session</span><?php endif; ?></div>
                                <span class="m-agenda__name"><?= esc($item['sessions_name']) ?></span>
                                <span class="m-agenda__speakers">Speakers: <?= esc($speakers !== '' ? $speakers : 'TBA') ?></span>
                                <?php if ($locked): ?>
                                    <span class="m-action m-action--locked"><i class="fa fa-unlock-alt" aria-hidden="true"></i>Unlock</span>
                                <?php elseif ($live): ?>
                                    <span class="m-action m-action--live"><i class="fa fa-play" aria-hidden="true"></i>Join live</span>
                                <?php else: ?>
                                    <span class="m-action"><i class="fa fa-long-arrow-right" aria-hidden="true"></i>View Session</span>
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

<?= module_view('MobileApp', 'includes/footer') ?>
