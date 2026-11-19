<?php
echo module_view('Web', 'includes/header_home');
echo module_view('Web', 'includes/home_topbar');

$portalLocked = (bool) ($portal_locked ?? false);
$checkoutEnabled = (bool) ($checkout_enabled ?? false);
$ticketPrice = $ticket_price ?? null;
$ticketCurrency = $ticket_currency ?? 'USD';
$conferenceTitle = $conference['title'] ?? 'Accelerate';
$welcomeVideoUrl = $welcome_video_url ?? '';
?>

<?php if ($portalLocked): ?>
    <div class="rc-payment-notice" data-payment-notice>
        <i class="fa fa-lock" style="color:#efb11e;font-size:16px" aria-hidden="true"></i>
        <div class="rc-payment-copy">
            <strong style="font-weight:600">Access restricted.</strong>
            This is a paid event. Please complete your registration payment to unlock all sessions.
            <span class="rc-payment-ticket">Ticket: NGN 25,000</span>
        </div>
        <a href="<?php echo site_url('attendees/checkout'); ?>" class="rc-payment-action">Pay now</a>
        <button type="button" class="rc-payment-close" aria-label="Close payment notice" data-close-payment-notice>&times;</button>
    </div>
<?php endif; ?>

<main data-screen-label="Home" style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:28px;padding:150px 0 48px;box-sizing:border-box">
    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;text-align:center">
        <span class="rc-kicker">Emergence Conference &middot; 2026</span>
        <span style="font-size:36px;font-weight:600;letter-spacing:-.02em">Welcome to Accelerate</span>
    </div>

    <div data-home-video data-video-src="<?php echo esc($welcomeVideoUrl, 'attr'); ?>" style="position:relative;width:min(900px,58vw,calc((100vh - 360px) * 1.7778));aspect-ratio:16 / 9;border-radius:16px;overflow:hidden;background:#0b030d;border:1px solid rgba(255,255,255,.14);box-shadow:0 30px 80px rgba(0,0,0,.45),0 0 0 8px rgba(255,255,255,.03)">
        <div data-home-video-cover style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
            <button type="button" data-home-video-play aria-label="Play welcome video" style="width:84px;height:84px;padding:0 0 0 6px;box-sizing:border-box;border:0;border-radius:50%;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);color:#fff;display:flex;align-items:center;justify-content:center;font-size:30px;cursor:pointer;box-shadow:0 0 40px rgba(216,25,142,.6)">
                <i class="fa fa-play" aria-hidden="true"></i>
            </button>
        </div>
        <span style="position:absolute;left:20px;bottom:16px;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.16em;color:rgba(255,255,255,.6)">WELCOME VIDEO</span>
    </div>

    <a href="<?php echo base_url('attendees/lobby'); ?>" class="rc-btn" style="display:flex;align-items:center;gap:12px;height:52px;padding:0 34px;font-size:16px;letter-spacing:.1em;text-decoration:none">
        Enter Lobby <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
    </a>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.querySelector("[data-home-video-play]")?.addEventListener("click", function() {
    const video = document.querySelector("[data-home-video]");
    const cover = document.querySelector("[data-home-video-cover]");
    const source = video?.dataset.videoSrc;
    if (!video || !cover || !source) return;

    const sourceUrl = new URL(source);
    sourceUrl.searchParams.set("autoplay", "true");
    sourceUrl.searchParams.set("loop", "false");
    sourceUrl.searchParams.set("muted", "false");
    sourceUrl.searchParams.set("preload", "true");
    sourceUrl.searchParams.set("responsive", "true");
    const iframe = document.createElement("iframe");
    iframe.src = sourceUrl.toString();
    iframe.allow = "accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;";
    iframe.allowFullscreen = true;
    iframe.style.cssText = "position:absolute;inset:0;width:100%;height:100%;border:0";
    cover.remove();
    video.prepend(iframe);
});

document.addEventListener("click", function(event) {
    const close = event.target.closest("[data-close-payment-notice]");
    if (close) {
        close.closest("[data-payment-notice]")?.classList.add("is-hidden");
    }
});
</script>
</body>
</html>
