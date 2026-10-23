<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main data-screen-label="Networking Center" style="position:absolute;inset:0">
    <div style="position:absolute;left:7vw;top:120px;display:flex;flex-direction:column;gap:8px">
        <span class="rc-kicker">Accelerate 2026</span>
        <span style="font-size:28px;font-weight:700;letter-spacing:-.02em;line-height:1">Networking Center</span>
    </div>

    <a data-node="attendees" data-to="speakers"
       href="<?php echo base_url('attendees/attendees'); ?>"
       style="position:absolute;left:24%;top:56%;transform:translate(-50%,-50%);width:150px;height:150px;border-radius:50%;cursor:pointer;transition:transform .35s ease,filter .35s ease">
        <img src="<?php echo asset_url('images/attendees-icon.png'); ?>" alt="Attendees" style="width:100%;height:100%">
    </a>

    <a data-node="speakers" data-to="communities"
       href="<?php echo base_url('attendees/speakers'); ?>"
       style="position:absolute;left:50%;top:56%;transform:translate(-50%,-50%);width:150px;height:150px;border-radius:50%;cursor:pointer;transition:transform .35s ease,filter .35s ease">
        <img src="<?php echo asset_url('images/speakers-icon.png'); ?>" alt="Speakers" style="width:100%;height:100%">
    </a>

    <a data-node="communities"
       href="<?php echo base_url('attendees/communities'); ?>"
       style="position:absolute;left:76%;top:56%;transform:translate(-50%,-50%);width:150px;height:150px;border-radius:50%;cursor:pointer;transition:transform .35s ease,filter .35s ease">
        <img src="<?php echo asset_url('images/community-icon.png'); ?>" alt="Communities" style="width:100%;height:100%">
    </a>

    <span style="position:absolute;left:24%;top:calc(56% + 100px);transform:translateX(-50%);font-weight:600;font-size:16px;letter-spacing:.08em">ATTENDEES</span>
    <span style="position:absolute;left:50%;top:calc(56% + 100px);transform:translateX(-50%);font-weight:600;font-size:16px;letter-spacing:.08em">SPEAKERS</span>
    <span style="position:absolute;left:76%;top:calc(56% + 100px);transform:translateX(-50%);font-weight:600;font-size:16px;letter-spacing:.08em">COMMUNITIES</span>

    <div class="rc-corner-logo" aria-hidden="true">
        <span class="rc-kicker" style="font-size:11px">Networking &middot; Emergence 2026</span>
        <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="">
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
