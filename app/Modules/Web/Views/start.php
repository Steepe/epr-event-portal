<?php
$live = null;
$past = [];

if (!empty($conferences)) {
    foreach ($conferences as $conf) {
        if (($conf['status'] ?? '') === 'live') {
            $live = $conf;
            session()->set('live-conference-id', $conf['conference_id']);
        } else {
            $past[] = $conf;
        }
    }
}

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
?>

<main data-screen-label="Landing" style="position:absolute;inset:0">
    <div style="position:absolute;left:7vw;top:112px;display:flex;flex-direction:column;gap:8px">
        <span class="rc-kicker">Welcome back, <?php echo esc(session('firstname') ?: 'Ada'); ?></span>
        <span style="font-size:34px;font-weight:600;letter-spacing:-.02em;color:#fff">Where would you like to go?</span>
    </div>

    <a data-node="past"
       data-to="core"
       data-shape="rect"
       href="<?php echo base_url('attendees/past_conferences'); ?>"
       class="rc-start-module rc-start-module-side"
       style="left:17%;top:60%">
        <span class="rc-start-module-num">MODULE 01</span>
        <div style="display:flex">
            <img src="<?php echo asset_url('images/innovate-icon.png'); ?>" alt="" style="width:54px;height:54px;border-radius:50%;object-fit:cover;box-shadow:0 0 0 3px #2c1830">
            <img src="<?php echo asset_url('images/dominate-icon.png'); ?>" alt="" style="width:54px;height:54px;border-radius:50%;object-fit:cover;margin-left:-14px;box-shadow:0 0 0 3px #2c1830">
            <img src="<?php echo asset_url('images/unleash-icon.png'); ?>" alt="" style="width:54px;height:54px;border-radius:50%;object-fit:cover;margin-left:-14px;box-shadow:0 0 0 3px #2c1830;background:#fff">
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:14px">
            <span style="font-size:18px;font-weight:600;color:#fff">Past Conferences</span>
            <i class="fa fa-long-arrow-right" style="color:#efb11e" aria-hidden="true"></i>
        </div>
        <span class="rc-start-port is-right"></span>
    </a>

    <a data-node="core"
       data-to="webinars"
       data-shape="rect"
       href="<?php echo base_url('attendees/home'); ?>"
       class="rc-start-module rc-start-module-core"
       style="left:50%;top:56%">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px">
            <span class="rc-start-module-num" style="color:rgba(255,255,255,.7)">MODULE 02</span>
            <span class="rc-start-live-badge">
                <span></span>LIVE NOW
            </span>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px">
            <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>"
                 alt="Accelerate: Building sustainable systems for execution"
                 style="display:block;width:100%;max-width:300px;height:auto">
            <span class="rc-mono" style="font-size:15px;letter-spacing:.24em;color:#efb11e">2026</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;font-size:14px;font-weight:600;letter-spacing:.08em;color:#fff">
            <span>ENTER CONFERENCE</span>
            <i class="fa fa-long-arrow-right" style="color:#efb11e" aria-hidden="true"></i>
        </div>
        <span class="rc-start-port is-left"></span>
        <span class="rc-start-port is-right"></span>
    </a>

    <a data-node="webinars"
       data-shape="rect"
       href="<?php echo base_url('attendees/webinars'); ?>"
       class="rc-start-module rc-start-module-side"
       style="left:83%;top:60%">
        <span class="rc-start-module-num">MODULE 03</span>
        <img src="<?php echo asset_url('images/webinars.png'); ?>" alt="EPR Global Webinars" style="width:64px;height:64px;object-fit:contain">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:14px">
            <span style="font-size:18px;font-weight:600;color:#fff">Webinars</span>
            <i class="fa fa-long-arrow-right" style="color:#efb11e" aria-hidden="true"></i>
        </div>
        <span class="rc-start-port is-left"></span>
    </a>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
