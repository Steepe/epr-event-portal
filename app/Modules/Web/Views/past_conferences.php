<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main data-screen-label="Past Conferences" style="position:absolute;inset:0">
    <div style="position:absolute;left:7vw;top:112px;display:flex;flex-direction:column;gap:8px">
        <a href="<?php echo base_url('attendees/start'); ?>" class="rc-kicker" style="cursor:pointer;display:flex;gap:8px;align-items:center">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>Back
        </a>
        <span style="font-size:40px;font-weight:700;letter-spacing:-.03em;line-height:1">Past Conferences</span>
    </div>

    <a data-node="innovate" data-to="dominate"
       href="<?php echo base_url('attendees/start'); ?>"
       style="position:absolute;left:22%;top:56%;transform:translate(-50%,-50%);width:170px;height:170px;border-radius:50%;cursor:pointer;transition:transform .3s ease">
        <img src="<?php echo asset_url('images/innovate-icon.png'); ?>" alt="Innovate 2023" style="width:100%;height:100%;object-fit:contain">
    </a>

    <a data-node="dominate" data-to="unleash"
       href="<?php echo base_url('attendees/start'); ?>"
       style="position:absolute;left:50%;top:56%;transform:translate(-50%,-50%);width:170px;height:170px;border-radius:50%;cursor:pointer;transition:transform .3s ease">
        <img src="<?php echo asset_url('images/dominate-icon.png'); ?>" alt="Dominate 2024" style="width:100%;height:100%;object-fit:contain">
    </a>

    <a data-node="unleash"
       href="<?php echo base_url('attendees/start'); ?>"
       style="position:absolute;left:78%;top:56%;transform:translate(-50%,-50%);width:190px;height:190px;border-radius:50%;cursor:pointer;transition:transform .3s ease">
        <img src="<?php echo asset_url('images/unleash-icon.png'); ?>" alt="Unleash 2025" style="width:100%;height:100%;object-fit:contain;transform:scale(1.35)">
    </a>

    <div style="position:absolute;left:22%;top:calc(56% + 110px);transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:4px">
        <span style="font-size:16px;font-weight:600">Innovate</span>
        <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">2023</span>
    </div>

    <div style="position:absolute;left:50%;top:calc(56% + 110px);transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:4px">
        <span style="font-size:16px;font-weight:600">Dominate</span>
        <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">2024</span>
    </div>

    <div style="position:absolute;left:78%;top:calc(56% + 110px);transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:4px">
        <span style="font-size:16px;font-weight:600">Unleash</span>
        <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">2025</span>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
