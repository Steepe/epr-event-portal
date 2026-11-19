<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$conferenceTitle = $conference['title'] ?? 'Conference';
?>

<main class="rc-page rc-page-center" data-screen-label="Conference Detail">
    <div class="rc-container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">Conference Archive</span>
                <span class="rc-page-title"><?php echo esc($conferenceTitle); ?></span>
                <span class="rc-page-sub">Agenda sessions for this conference.</span>
            </div>
            <a href="<?php echo base_url('attendees/past_conferences'); ?>" class="rc-back-link">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK TO ARCHIVE
            </a>
        </div>

        <?php if (empty($sessions)): ?>
            <div class="rc-empty">No sessions found for this conference.</div>
        <?php else: ?>
            <div class="rc-agenda-list">
                <?php foreach ($sessions as $s): ?>
                    <?php
                    $start = !empty($s['start_time']) ? (new DateTime($s['event_date'] . ' ' . $s['start_time'], new DateTimeZone('UTC')))->format('g:i A') : '';
                    $end = !empty($s['end_time']) ? (new DateTime($s['event_date'] . ' ' . $s['end_time'], new DateTimeZone('UTC')))->format('g:i A') : '';
                    $date = !empty($s['event_date']) ? date('F j, Y', strtotime($s['event_date'])) : '';
                    ?>
                    <article class="rc-session-row">
                        <div class="rc-time-col" data-local-session-time data-event-date="<?php echo esc($s['event_date'], 'attr'); ?>" data-start-time="<?php echo esc($s['start_time'], 'attr'); ?>" data-end-time="<?php echo esc($s['end_time'], 'attr'); ?>">
                            <span data-local-session-range style="font-size:14px;color:#fff"><?php echo esc($start ?: 'TBA'); ?><?php echo $end ? ' – ' . esc($end) : ''; ?></span>
                            <span data-local-session-zone style="font-size:12px;color:rgba(255,255,255,.55)">GMT</span>
                        </div>
                        <div class="rc-timeline">
                            <span class="rc-dot done"></span>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:6px;min-width:0">
                            <span style="font-size:17px;font-weight:600;letter-spacing:-.01em"><?php echo esc($s['sessions_name']); ?></span>
                            <span style="font-size:13px;color:rgba(255,255,255,.7)">
                                <span data-local-session-date data-event-date="<?php echo esc($s['event_date'], 'attr'); ?>" data-start-time="<?php echo esc($s['start_time'], 'attr'); ?>"><?php echo esc($date); ?></span> · Speakers: <?php echo esc($s['speakers'] ?: 'TBA'); ?>
                            </span>
                        </div>
                        <a href="<?php echo base_url('attendees/sessions/' . $s['sessions_id']); ?>" class="rc-pill" style="text-decoration:none">View Session</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
