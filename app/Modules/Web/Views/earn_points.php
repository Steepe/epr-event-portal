<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main class="rc-page rc-page-center" data-screen-label="Earn Points">
    <div class="rc-container">
        <div class="rc-title-stack">
            <span class="rc-kicker">Conference Rewards</span>
            <span class="rc-page-title">Earn Points</span>
            <span class="rc-page-sub">Complete activities and keep your conference momentum visible.</span>
        </div>

        <?php if (empty($point_guides)): ?>
            <div class="rc-empty">No activities available yet. Check back soon.</div>
        <?php else: ?>
            <section class="rc-content-grid">
                <?php foreach ($point_guides as $guide): ?>
                    <?php $imageFile = !empty($guide['image']) ? asset_url('images/' . $guide['image']) : asset_url('images/eventslogo.png'); ?>
                    <article class="rc-person-card" style="padding:24px;display:flex;align-items:center;gap:20px">
                        <span style="width:82px;height:82px;border-radius:50%;background:rgba(239,177,30,.1);border:1px solid rgba(239,177,30,.24);display:flex;align-items:center;justify-content:center;flex:none">
                            <img src="<?php echo $imageFile; ?>" alt="<?php echo esc($guide['activity']); ?>" style="width:50px;height:50px;object-fit:contain">
                        </span>
                        <div style="display:flex;flex-direction:column;gap:8px;min-width:0;flex:1">
                            <span style="font-size:18px;font-weight:600"><?php echo esc($guide['activity']); ?></span>
                            <span style="font-size:13px;color:rgba(255,255,255,.7)">Reward value: <?php echo esc($guide['points']); ?> points</span>
                        </div>
                        <span class="rc-pill rc-grad-pill">Earn <?php echo esc($guide['points']); ?></span>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
