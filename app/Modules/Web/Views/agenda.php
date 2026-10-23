<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$userPremium = (int) ($premium ?? 0);
$timezone = $timezone ?? 'Africa/Lagos';
$dayIndex = 0;
?>

<main class="rc-page rc-page-center" data-screen-label="Agenda">
    <div class="rc-container" style="gap:24px;min-height:0">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">Accelerate 2026</span>
                <span class="rc-page-title">Agenda</span>
                <span class="rc-page-sub">All times in <?php echo esc($timezone); ?></span>
            </div>

            <?php if (!empty($sessionsByDate)): ?>
                <div style="display:flex;gap:8px;padding:5px;border-radius:999px;background:rgba(22,8,26,.6);border:1px solid rgba(255,255,255,.12)">
                    <?php foreach ($sessionsByDate as $date => $sessions): ?>
                        <?php $target = 'day-' . preg_replace('/[^0-9]/', '', $date); ?>
                        <button type="button"
                                class="agenda-day-toggle"
                                data-target="<?php echo $target; ?>"
                                style="border:0;padding:9px 20px;border-radius:999px;font:inherit;font-size:13px;font-weight:500;cursor:pointer;white-space:nowrap;background:<?php echo $dayIndex === 0 ? 'linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585)' : 'transparent'; ?>;color:<?php echo $dayIndex === 0 ? '#fff' : 'rgba(255,255,255,.75)'; ?>">
                            <?php echo date('M j', strtotime($date)); ?>
                        </button>
                        <?php $dayIndex++; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($sessionsByDate)): ?>
            <div class="rc-empty">No sessions found for this conference.</div>
        <?php else: ?>
            <div class="rc-agenda-list">
                <?php $dayIndex = 0; foreach ($sessionsByDate as $date => $sessions): ?>
                    <?php $target = 'day-' . preg_replace('/[^0-9]/', '', $date); ?>
                    <section id="<?php echo $target; ?>" class="agenda-day-panel" style="<?php echo $dayIndex === 0 ? '' : 'display:none'; ?>">
                        <?php foreach ($sessions as $s): ?>
                            <?php
                            $start = (new DateTime($s['event_date'] . ' ' . $s['start_time']))->format('h:i A');
                            $end = (new DateTime($s['event_date'] . ' ' . $s['end_time']))->format('h:i A');
                            $isPaidSession = (int) ($s['access_level'] ?? 1) === 2;
                            $canAccess = ! $isPaidSession || $userPremium === 1;
                            $speakerList = [];
                            if (!empty($s['speakers'])) {
                                $speakerList = array_map(static fn($sp) => esc($sp['name']), $s['speakers']);
                            }
                            $status = strtolower((string) ($s['status'] ?? 'next'));
                            $dotClass = $status === 'live' ? ' live' : ($status === 'done' ? ' done' : '');
                            ?>
                            <article class="rc-session-row" style="opacity:<?php echo $status === 'done' ? '0.7' : '1'; ?>">
                                <div class="rc-time-col">
                                    <span style="font-size:14px;color:#fff"><?php echo $start; ?></span>
                                    <span style="font-size:12px;color:rgba(255,255,255,.55)">- <?php echo $end; ?></span>
                                </div>

                                <div class="rc-timeline">
                                    <span class="rc-dot<?php echo $dotClass; ?>"></span>
                                </div>

                                <div style="display:flex;flex-direction:column;gap:6px;min-width:0">
                                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                                        <span style="font-size:17px;font-weight:600;letter-spacing:-.01em"><?php echo esc($s['sessions_name']); ?></span>
                                        <?php if ($status === 'live'): ?>
                                            <span style="display:flex;align-items:center;gap:6px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.16em;padding:3px 8px;border-radius:999px;background:#d8198e"><span style="width:6px;height:6px;border-radius:50%;background:#fff"></span>LIVE</span>
                                        <?php endif; ?>
                                        <?php if ($isPaidSession && ! $canAccess): ?>
                                            <span style="display:flex;align-items:center;gap:6px;font-size:11px;font-weight:500;color:#efb11e"><i class="fa fa-lock" aria-hidden="true"></i>Paid session</span>
                                        <?php endif; ?>
                                    </div>
                                    <span style="font-size:13px;color:rgba(255,255,255,.7)">Speakers: <?php echo !empty($speakerList) ? implode(', ', $speakerList) : 'TBA'; ?></span>
                                </div>

                                <?php if ($canAccess): ?>
                                    <a href="<?php echo base_url('attendees/sessions/' . $s['sessions_id']); ?>" class="rc-session-action">View Session</a>
                                <?php else: ?>
                                    <a href="<?php echo base_url('attendees/upgrade/' . (int) ($conference_id ?? 0)); ?>" class="rc-session-unlock"><i class="fa fa-unlock-alt" aria-hidden="true"></i>Unlock</a>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </section>
                    <?php $dayIndex++; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const toggles = Array.from(document.querySelectorAll(".agenda-day-toggle"));
    const panels = Array.from(document.querySelectorAll(".agenda-day-panel"));

    toggles.forEach(button => {
        button.addEventListener("click", () => {
            toggles.forEach(item => {
                item.style.background = "transparent";
                item.style.color = "rgba(255,255,255,.75)";
            });
            panels.forEach(panel => panel.style.display = "none");
            button.style.background = "linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585)";
            button.style.color = "#fff";
            document.getElementById(button.dataset.target).style.display = "";
        });
    });
});
</script>
</body>
</html>
