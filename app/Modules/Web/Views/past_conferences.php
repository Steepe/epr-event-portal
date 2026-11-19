<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main data-screen-label="Past Conferences" style="position:absolute;inset:0;overflow-y:auto;padding:112px 7vw 64px;box-sizing:border-box">
    <div style="display:flex;flex-direction:column;gap:8px">
        <a href="<?php echo base_url('attendees/start'); ?>" class="rc-back-link">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK
        </a>
        <span style="font-size:40px;font-weight:700;letter-spacing:-.03em;line-height:1">Past Conferences</span>
    </div>

    <?php if (empty($conferences)): ?>
        <div class="rc-empty" style="margin-top:36px">No past conferences are available at this time.</div>
    <?php else: ?>
        <section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:30px;margin-top:48px;padding-bottom:36px;align-items:start">
            <?php foreach ($conferences as $conference): ?>
                <?php
                $icon = ! empty($conference['icon'])
                    ? base_url('uploads/conferences/' . rawurlencode(basename((string) $conference['icon'])))
                    : asset_url('images/lobby/default.png');
                $year = (int) ($conference['year'] ?? 0);
                if ($year === 0 && ! empty($conference['start_date'])) {
                    $year = (int) date('Y', strtotime($conference['start_date']));
                }
                ?>
                <a href="<?php echo base_url('attendees/pastConference/' . (int) $conference['conference_id']); ?>"
                   style="display:flex;flex-direction:column;align-items:center;gap:14px;color:#fff;text-decoration:none;text-align:center;transition:transform .3s ease">
                    <span style="display:flex;width:160px;height:160px;border-radius:50%;overflow:hidden;align-items:center;justify-content:center;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.24);box-shadow:0 0 32px rgba(239,177,30,.14)">
                        <img src="<?php echo esc($icon); ?>" alt="<?php echo esc($conference['title'] ?? 'Past conference'); ?>" style="width:100%;height:100%;object-fit:contain">
                    </span>
                    <span style="font-size:16px;font-weight:600"><?php echo esc($conference['title'] ?? 'Past Conference'); ?></span>
                    <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)"><?php echo esc($year ?: 'ARCHIVE'); ?></span>
                </a>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <div class="rc-corner-logo" aria-hidden="true">
        <span class="rc-kicker" style="font-size:11px">Past Conferences &middot; Emergence 2026</span>
        <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="">
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
