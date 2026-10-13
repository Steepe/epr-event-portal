<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$plan = $plan ?? 1;
$timezone = $timezone ?? 'Africa/Lagos';
$title = $conf_title ?? 'Conference Archive';
$dayIndex = 0;
?>

<main class="rc-page rc-page-center" data-screen-label="Past Conference Agenda">
    <div class="rc-container" style="min-height:0">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">Past Conferences</span>
                <span class="rc-page-title"><?php echo esc($title); ?></span>
                <span class="rc-page-sub">Recorded sessions and archive access. Times shown in <?php echo esc($timezone); ?></span>
            </div>

            <a href="<?php echo base_url('attendees/past_conferences'); ?>" class="rc-outline-pill" style="text-decoration:none">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>Archive
            </a>
        </div>

        <?php if (empty($sessionsByDate)): ?>
            <div class="rc-empty">No sessions found for this conference.</div>
        <?php else: ?>
            <div style="display:flex;gap:8px;padding:5px;border-radius:999px;background:rgba(22,8,26,.6);border:1px solid rgba(255,255,255,.12);align-self:flex-start">
                <?php foreach ($sessionsByDate as $date => $sessions): ?>
                    <?php $target = 'past-day-' . preg_replace('/[^0-9]/', '', $date); ?>
                    <button type="button"
                            class="agenda-day-toggle"
                            data-target="<?php echo $target; ?>"
                            style="border:0;padding:9px 20px;border-radius:999px;font:inherit;font-size:13px;font-weight:500;cursor:pointer;white-space:nowrap;background:<?php echo $dayIndex === 0 ? 'linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585)' : 'transparent'; ?>;color:<?php echo $dayIndex === 0 ? '#fff' : 'rgba(255,255,255,.75)'; ?>">
                        <?php echo date('M j, Y', strtotime($date)); ?>
                    </button>
                    <?php $dayIndex++; ?>
                <?php endforeach; ?>
            </div>

            <div class="rc-agenda-list">
                <?php $dayIndex = 0; foreach ($sessionsByDate as $date => $sessions): ?>
                    <?php $target = 'past-day-' . preg_replace('/[^0-9]/', '', $date); ?>
                    <section id="<?php echo $target; ?>" class="agenda-day-panel" style="<?php echo $dayIndex === 0 ? '' : 'display:none'; ?>">
                        <?php foreach ($sessions as $s): ?>
                            <?php
                            $start = (new DateTime($s['event_date'] . ' ' . $s['start_time']))->format('h:i A');
                            $end = (new DateTime($s['event_date'] . ' ' . $s['end_time']))->format('h:i A');
                            $canAccess = $plan >= (int) ($s['access_level'] ?? 1);
                            $hasRecording = !empty($s['vimeo_id']);
                            $speakerText = $s['speakers'] ?? 'TBA';
                            if (is_array($speakerText)) {
                                $speakerText = implode(', ', array_map(static fn($speaker) => is_array($speaker) ? ($speaker['name'] ?? '') : $speaker, $speakerText));
                            }
                            ?>
                            <article class="rc-session-row" style="grid-template-columns:132px 28px minmax(0,1fr) auto">
                                <div class="rc-time-col">
                                    <span style="font-size:14px;color:#fff"><?php echo $start; ?></span>
                                    <span style="font-size:12px;color:rgba(255,255,255,.55)">- <?php echo $end; ?></span>
                                </div>

                                <div class="rc-timeline">
                                    <span class="rc-dot done"></span>
                                </div>

                                <div style="display:flex;flex-direction:column;gap:6px;min-width:0">
                                    <span style="font-size:17px;font-weight:600;letter-spacing:-.01em"><?php echo esc($s['sessions_name']); ?></span>
                                    <span style="font-size:13px;color:rgba(255,255,255,.7)">Speakers: <?php echo esc($speakerText ?: 'TBA'); ?></span>
                                </div>

                                <?php if ($canAccess && $hasRecording): ?>
                                    <button type="button" class="rc-pill" data-toggle="modal" data-target="#sessionModal<?php echo (int) $s['sessions_id']; ?>" style="border:0">
                                        Watch Recording
                                    </button>
                                <?php elseif ($canAccess): ?>
                                    <span class="rc-outline-pill">Coming Soon</span>
                                <?php else: ?>
                                    <span class="rc-outline-pill" style="color:#efb11e;border-color:rgba(239,177,30,.45)">
                                        <i class="fa fa-lock" aria-hidden="true"></i>Locked
                                    </span>
                                <?php endif; ?>
                            </article>

                            <?php if ($canAccess && $hasRecording): ?>
                                <div class="modal fade" id="sessionModal<?php echo (int) $s['sessions_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content" style="background:#260029!important;border-radius:16px!important;border:1px solid rgba(255,255,255,.12)">
                                            <div class="modal-header border-0">
                                                <h6 class="modal-title" style="color:#fff"><?php echo esc($s['sessions_name']); ?></h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff;opacity:1">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div class="embed-responsive embed-responsive-16by9">
                                                    <iframe class="embed-responsive-item"
                                                            src="https://player.vimeo.com/video/<?php echo esc($s['vimeo_id']); ?>?dnt=1"
                                                            frameborder="0"
                                                            allow="autoplay; fullscreen; picture-in-picture"
                                                            allowfullscreen
                                                            loading="lazy"></iframe>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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

    $(".modal").on("hidden.bs.modal", function () {
        const iframe = $(this).find("iframe");
        if (iframe.length) {
            const src = iframe.attr("src");
            iframe.attr("src", "");
            iframe.attr("src", src);
        }
    });
});
</script>
</body>
</html>
