<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main data-screen-label="Webinars" style="position:absolute;inset:0">
    <div class="rc-corner-logo" aria-hidden="true">
        <span class="rc-kicker" style="font-size:11px">EPR Global Webinars &middot; Bi-weekly</span>
        <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="">
    </div>

    <div style="position:absolute;left:7vw;top:112px;display:flex;flex-direction:column;gap:8px;max-width:520px">
        <a href="<?php echo base_url('attendees/start'); ?>" class="rc-back-link">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK
        </a>
        <span style="font-size:40px;font-weight:700;letter-spacing:-.03em;line-height:1">Webinars</span>
        <span style="font-size:15px;line-height:1.55;color:rgba(255,255,255,.78);text-wrap:pretty">Short, practical sessions on cadence, metrics and accountability, with live Q&amp;A.</span>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
