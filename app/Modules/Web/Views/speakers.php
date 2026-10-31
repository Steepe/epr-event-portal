<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), false],
    ['SPEAKERS', base_url('attendees/speakers'), true],
];
$speakers = $speakers ?? [];
$slots = ['NOV 11 · 12:30 PM', 'NOV 11 · 12:45 PM', 'NOV 11 · 02:45 PM', 'NOV 12 · 11:45 AM', 'NOV 12 · 01:15 PM', 'NOV 12 · 04:45 PM'];
$initials = static function (string $name): string {
    $cleanName = preg_replace('/^Dr\.\s*/i', '', trim($name));
    $parts = preg_split('/\s+/', $cleanName);
    return strtoupper(substr($parts[0] ?? 'S', 0, 1) . substr($parts[1] ?? '', 0, 1));
};
$speakerFirstName = static function (string $name): string {
    $cleanName = preg_replace('/^Dr\.\s*/i', '', trim($name));
    return explode(' ', $cleanName)[0] ?: 'this speaker';
};
?>

<main class="rc-page rc-page-center" data-screen-label="Speakers">
    <div class="rc-container" style="gap:24px">
        <header class="rc-net-sub-header">
            <div class="rc-net-heading">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i>NETWORKING CENTER</a>
                <span class="rc-page-title">Speakers</span>
                <span class="rc-page-sub"><?php echo count($speakers); ?> speakers · 2 live now</span>
            </div>
            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $active]): ?><a href="<?php echo $url; ?>" class="rc-net-tab <?php echo $active ? 'is-active' : ''; ?>"><?php echo esc($label); ?></a><?php endforeach; ?>
            </div>
        </header>

        <?php if (!empty($speakers)): ?>
            <section class="rc-speaker-grid">
                <?php foreach ($speakers as $speakerIndex => $speaker): ?>
                    <?php
                    $speakerId = (int) ($speaker['speaker_id'] ?? ($speakerIndex + 1));
                    $name = trim((string) ($speaker['speaker_name'] ?? 'Speaker'));
                    $speakerTitle = trim((string) ($speaker['speaker_title'] ?? 'Speaker'));
                    $company = trim((string) ($speaker['speaker_company'] ?? ''));
                    $role = $speakerTitle . ($company !== '' ? ' · ' . $company : '');
                    $photo = !empty($speaker['speaker_photo']) ? base_url('uploads/speakers/' . $speaker['speaker_photo']) : '';
                    $sessions = $speaker['sessions'] ?? [];
                    $sessionCount = max(1, count($sessions));
                    $nextSession = $sessions[0]['sessions_name'] ?? ($speakerIndex < 2 ? 'Designing Systems That Outlast Motivation' : 'Conference session');
                    $nextWhen = !empty($sessions[0]['event_date'])
                        ? strtoupper(date('M d', strtotime($sessions[0]['event_date']))) . ' · ' . date('h:i A', strtotime($sessions[0]['start_time'] ?? '10:00'))
                        : ($speakerIndex < 2 ? 'NOV 11 · 10:00 AM' : 'NOV 12 · 10:15 AM');
                    $isLive = $speakerIndex < 2;
                    $topicText = (string) ($speaker['topics'] ?? $speaker['speaker_topics'] ?? $speaker['expertise'] ?? 'Systems, Execution, Leadership');
                    $topics = array_values(array_filter(array_map('trim', preg_split('/[,|]/', $topicText))));
                    $bio = trim((string) ($speaker['bio'] ?? 'This speaker shares practical lessons on sustainable execution, leadership rhythms and the systems that help teams scale.'));
                    $firstName = $speakerFirstName($name);
                    ?>
                    <article class="rc-person-card rc-speaker-card" data-toggle="modal" data-target="#speakerModal<?php echo $speakerId; ?>" tabindex="0">
                        <div class="rc-speaker-media">
                            <?php if ($photo !== ''): ?>
                                <img src="<?php echo $photo; ?>" alt="<?php echo esc($name); ?>" onerror="this.hidden=true;this.nextElementSibling.hidden=false" class="rc-speaker-photo">
                                <span class="rc-speaker-initials" hidden><?php echo $initials($name); ?></span>
                            <?php else: ?>
                                <span class="rc-speaker-initials"><?php echo $initials($name); ?></span>
                            <?php endif; ?>
                            <?php if ($isLive): ?><span class="rc-speaker-live"><span></span>LIVE NOW</span><?php endif; ?>
                            <span class="rc-speaker-badge"><?php echo $sessionCount; ?> SESSION<?php echo $sessionCount === 1 ? '' : 'S'; ?></span>
                        </div>
                        <div class="rc-speaker-card-copy">
                            <div><strong><?php echo esc($name); ?></strong><span><?php echo esc($role); ?></span></div>
                            <div class="rc-speaker-next"><i class="fa fa-calendar-o" aria-hidden="true"></i><span><small><?php echo esc($nextWhen); ?></small><strong><?php echo esc($nextSession); ?></strong></span></div>
                        </div>
                    </article>

                    <div class="modal fade rc-speaker-modal" id="speakerModal<?php echo $speakerId; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <button type="button" class="rc-speaker-close close" data-dismiss="modal" aria-label="Close">&times;</button>
                                <aside class="rc-speaker-modal-profile">
                                    <?php if ($photo !== ''): ?><img src="<?php echo $photo; ?>" alt="<?php echo esc($name); ?>" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><span class="rc-speaker-modal-initials" hidden><?php echo $initials($name); ?></span><?php else: ?><span class="rc-speaker-modal-initials"><?php echo $initials($name); ?></span><?php endif; ?>
                                    <div class="rc-speaker-modal-name"><strong><?php echo esc($name); ?></strong><span><?php echo esc($speakerTitle); ?></span><?php if ($company !== ''): ?><span><?php echo esc($company); ?></span><?php endif; ?></div>
                                    <div class="rc-speaker-topics"><?php foreach (array_slice($topics, 0, 3) as $topic): ?><span><?php echo esc($topic); ?></span><?php endforeach; ?></div>
                                    <div class="rc-speaker-socials"><span><i class="fa fa-linkedin" aria-hidden="true"></i></span><span><i class="fa fa-twitter" aria-hidden="true"></i></span></div>
                                </aside>
                                <section class="rc-speaker-modal-main">
                                    <div class="rc-speaker-about"><span>ABOUT</span><p><?php echo esc($bio); ?></p></div>
                                    <div class="rc-speaker-sessions"><span>SESSIONS</span>
                                        <?php if (!empty($sessions)): ?>
                                            <?php foreach ($sessions as $sessionIndex => $session): ?>
                                                <?php
                                                $sessionWhen = !empty($session['event_date']) ? strtoupper(date('M d', strtotime($session['event_date']))) . ' · ' . date('h:i A', strtotime($session['start_time'] ?? '10:00')) : $nextWhen;
                                                $sessionName = $session['sessions_name'] ?? $nextSession;
                                                $sessionId = (int) ($session['sessions_id'] ?? $session['id'] ?? 0);
                                                ?>
                                                <div class="rc-speaker-session-row"><div><small><?php echo esc($sessionWhen); ?></small><strong><?php echo esc($sessionName); ?></strong></div><?php if ($isLive && $sessionIndex === 0 && $sessionId > 0): ?><a href="<?php echo base_url('attendees/sessions/' . $sessionId); ?>" class="rc-speaker-join"><span></span>Join live</a><?php else: ?><button type="button" class="rc-speaker-agenda"><i class="fa fa-plus" aria-hidden="true"></i><span>Add to agenda</span></button><?php endif; ?></div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="rc-speaker-session-row"><div><small><?php echo esc($nextWhen); ?></small><strong><?php echo esc($nextSession); ?></strong></div><?php if ($isLive): ?><a href="<?php echo base_url('attendees/lobby'); ?>" class="rc-speaker-join"><span></span>Join live</a><?php else: ?><button type="button" class="rc-speaker-agenda"><i class="fa fa-plus" aria-hidden="true"></i><span>Add to agenda</span></button><?php endif; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rc-speaker-booking" data-speaker-first="<?php echo esc($firstName, 'attr'); ?>">
                                        <div><strong>Book a 1:1 with <?php echo esc($firstName); ?></strong><span>15-minute video meetings during networking breaks.</span></div>
                                        <div class="rc-speaker-slots"><?php foreach ($slots as $slot): ?><button type="button" data-slot="<?php echo esc($slot, 'attr'); ?>"><?php echo esc($slot); ?></button><?php endforeach; ?></div>
                                        <button type="button" class="rc-speaker-request" disabled>Request meeting</button>
                                        <div class="rc-speaker-booked" hidden><i class="fa fa-check-circle" aria-hidden="true"></i><span><strong></strong><small>You’ll get the meeting link by email once <?php echo esc($firstName); ?> accepts.</small></span><button type="button">Change</button></div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="rc-empty">No speakers found.</div>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("click", event => {
    const slot = event.target.closest(".rc-speaker-slots button");
    if (slot) {
        const booking = slot.closest(".rc-speaker-booking");
        booking.querySelectorAll(".rc-speaker-slots button").forEach(button => button.classList.toggle("is-selected", button === slot));
        booking.querySelector(".rc-speaker-request").disabled = false;
        booking.dataset.slot = slot.dataset.slot;
        return;
    }
    const request = event.target.closest(".rc-speaker-request");
    if (request && !request.disabled) {
        const booking = request.closest(".rc-speaker-booking");
        booking.querySelector(".rc-speaker-slots").hidden = true;
        request.hidden = true;
        const confirmation = booking.querySelector(".rc-speaker-booked");
        confirmation.hidden = false;
        confirmation.querySelector("strong").textContent = "Request sent for " + booking.dataset.slot;
        return;
    }
    const change = event.target.closest(".rc-speaker-booked button");
    if (change) {
        const booking = change.closest(".rc-speaker-booking");
        booking.querySelector(".rc-speaker-slots").hidden = false;
        booking.querySelector(".rc-speaker-request").hidden = false;
        change.closest(".rc-speaker-booked").hidden = true;
        return;
    }
    const agenda = event.target.closest(".rc-speaker-agenda");
    if (agenda) {
        const added = agenda.classList.toggle("is-added");
        agenda.querySelector("i").className = "fa " + (added ? "fa-check" : "fa-plus");
        agenda.querySelector("span").textContent = added ? "Added" : "Add to agenda";
    }
});
document.addEventListener("keydown", event => {
    const card = event.target.closest(".rc-speaker-card");
    if (card && (event.key === "Enter" || event.key === " ")) { event.preventDefault(); card.click(); }
});
</script>
</body>
</html>
