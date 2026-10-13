<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main class="rc-page rc-page-center" data-screen-label="Envision">
    <div class="rc-container" style="max-width:980px">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">Accelerate Practice</span>
                <span class="rc-page-title">Vision Birthing Exercise</span>
                <span class="rc-page-sub">A guided video exercise for clarifying execution goals.</span>
            </div>
            <a href="<?php echo base_url('attendees/start'); ?>" class="rc-outline-pill" style="text-decoration:none">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>Gateways
            </a>
        </div>

        <section class="rc-panel" style="padding:18px">
            <div style="position:relative;width:100%;aspect-ratio:16/9;border-radius:14px;overflow:hidden;background:#120512">
                <iframe src="https://player.vimeo.com/video/771780852?h=2ad1e8b0f7&color=eebf34&title=0&byline=0&portrait=0"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        style="position:absolute;inset:0;width:100%;height:100%;border:0"></iframe>
            </div>
        </section>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
