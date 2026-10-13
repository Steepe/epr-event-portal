<?php
echo module_view('Web', 'includes/header_home');
echo module_view('Web', 'includes/home_topbar');

$portalLocked = (bool) ($portal_locked ?? false);
$checkoutEnabled = (bool) ($checkout_enabled ?? false);
$ticketPrice = $ticket_price ?? null;
$ticketCurrency = $ticket_currency ?? 'USD';
$conferenceTitle = $conference['title'] ?? 'Accelerate';
?>

<?php if ($portalLocked): ?>
    <div style="position:absolute;top:84px;left:50%;transform:translateX(-50%);z-index:15;width:min(1000px,calc(100% - 64px));box-sizing:border-box;display:flex;align-items:center;gap:16px;padding:10px 12px 10px 18px;border-radius:14px;background:rgba(239,177,30,.12);border:1px solid rgba(239,177,30,.45);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px)">
        <i class="fa fa-lock" style="color:#efb11e;font-size:16px" aria-hidden="true"></i>
        <div style="flex:1;font-size:13px;line-height:1.45">
            <strong style="font-weight:600">Access restricted.</strong>
            This is a paid event. Please complete your registration payment to unlock all sessions.
            <?php if ($ticketPrice !== null): ?>
                <span style="color:#efb11e;font-weight:600;margin-left:6px;white-space:nowrap">Ticket: <?php echo esc($ticketCurrency); ?> <?php echo esc(number_format((float) $ticketPrice, 2)); ?></span>
            <?php endif; ?>
        </div>
        <?php if ($checkoutEnabled): ?>
            <a href="<?php echo site_url('attendees/checkout'); ?>" style="padding:9px 22px;border-radius:15px;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);color:#fff;font-size:13px;font-weight:600;letter-spacing:.04em;white-space:nowrap">Pay now</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<main data-screen-label="Home" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:28px;padding:150px 0 48px;box-sizing:border-box">
    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center">
        <span class="rc-kicker">Emergence Conference &middot; 2026</span>
        <span style="font-size:36px;font-weight:600;letter-spacing:-.02em">Welcome to <?php echo esc($conferenceTitle); ?></span>
    </div>

    <div style="position:relative;width:min(900px,58vw,calc((100vh - 360px) * 1.7778));aspect-ratio:16 / 9;border-radius:16px;overflow:hidden;background:#0b030d;border:1px solid rgba(255,255,255,.14);box-shadow:0 30px 80px rgba(0,0,0,.45),0 0 0 8px rgba(255,255,255,.03)">
        <iframe src="https://iframe.mediadelivery.net/embed/405384/60589a7b-3fe7-40fb-a2ae-3c024f23e01e?autoplay=true&loop=false&muted=false&preload=true&responsive=true"
                loading="lazy"
                allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                allowfullscreen="true"
                style="position:absolute;inset:0;width:100%;height:100%;border:0"></iframe>
        <span style="position:absolute;left:20px;bottom:16px;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.16em;color:rgba(255,255,255,.6)">WELCOME VIDEO</span>
    </div>

    <?php if (! $portalLocked): ?>
        <a href="<?php echo base_url('attendees/lobby'); ?>" class="rc-btn" style="display:flex;align-items:center;gap:12px;height:52px;padding:0 34px;font-size:16px;letter-spacing:.1em;text-decoration:none">
            Enter Lobby <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
        </a>
    <?php endif; ?>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
