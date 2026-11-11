<?php echo module_view('MobileApp', 'includes/header_home'); ?>

<?php
    $conferenceTitle = trim((string) ($conference['title'] ?? 'Accelerate')) ?: 'Accelerate';
    $conferenceYear = esc((string) ($conference['year'] ?? '2026'));
    $videoSource = mobile_asset_url('videos/start-bg.mp4');
?>

<style>
    body.epr-mobile-auth { padding: 0 !important; }
    .mobile-welcome { position:relative; display:flex; min-height:100svh; box-sizing:border-box; align-items:center; justify-content:center; overflow:hidden; padding:28px 20px 44px; color:#fff; background:radial-gradient(circle at 15% 12%,rgba(239,177,30,.16),transparent 30%),radial-gradient(circle at 88% 22%,rgba(216,25,142,.22),transparent 34%),radial-gradient(ellipse 90% 80% at 45% 55%,#62365c 0%,#422846 48%,#261528 100%); }
    .mobile-welcome::before { position:absolute; inset:0; content:''; opacity:.52; pointer-events:none; background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px); background-size:42px 42px; mask-image:linear-gradient(to bottom,rgba(0,0,0,.75),transparent 76%); }
    .mobile-welcome__content { position:relative; z-index:1; display:flex; width:100%; max-width:390px; flex-direction:column; align-items:center; gap:26px; text-align:center; }
    .mobile-welcome__eyebrow { color:#efb11e; font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:600; letter-spacing:.22em; text-transform:uppercase; }
    .mobile-welcome__title { margin:0; font-size:28px; font-weight:600; letter-spacing:-.02em; line-height:1.15; }
    .mobile-welcome__video { position:relative; width:100%; aspect-ratio:16 / 9; overflow:hidden; border:1px solid rgba(255,255,255,.14); border-radius:16px; background:#0b030d; box-shadow:0 30px 80px rgba(0,0,0,.45),0 0 0 6px rgba(255,255,255,.03); }
    .mobile-welcome__video video { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0; transition:opacity .2s ease; }
    .mobile-welcome__video.is-playing video { opacity:.72; }
    .mobile-welcome__play { position:absolute; z-index:1; top:50%; left:50%; display:flex; width:68px; height:68px; align-items:center; justify-content:center; padding-left:4px; border:0; border-radius:50%; transform:translate(-50%,-50%); background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585); box-shadow:0 0 40px rgba(216,25,142,.6); color:#fff; font-size:24px; cursor:pointer; }
    .mobile-welcome__video.is-playing .mobile-welcome__play { opacity:0; pointer-events:none; }
    .mobile-welcome__label { position:absolute; z-index:1; bottom:14px; left:16px; color:rgba(255,255,255,.6); font-family:'JetBrains Mono',monospace; font-size:10px; letter-spacing:.16em; }
    .mobile-welcome__button { display:flex; height:52px; align-items:center; gap:12px; padding:0 32px; border:0; border-radius:15px; background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585); color:#fff; font-size:15px; font-weight:600; letter-spacing:.1em; text-decoration:none; text-transform:uppercase; }
    .mobile-welcome__button:focus-visible, .mobile-welcome__play:focus-visible { outline:2px solid #efb11e; outline-offset:3px; }
</style>

<main class="mobile-welcome">
    <section class="mobile-welcome__content" aria-labelledby="welcome-title">
        <div>
            <div class="mobile-welcome__eyebrow">Emergence Conference · <?= $conferenceYear ?></div>
            <h1 class="mobile-welcome__title" id="welcome-title">Welcome to <?= esc($conferenceTitle) ?></h1>
        </div>
        <div class="mobile-welcome__video" data-welcome-video>
            <video preload="metadata" playsinline data-welcome-player>
                <source src="<?= esc($videoSource, 'attr') ?>" type="video/mp4">
            </video>
            <button class="mobile-welcome__play" type="button" aria-label="Play welcome video" data-welcome-play><i class="fa fa-play" aria-hidden="true"></i></button>
            <span class="mobile-welcome__label">WELCOME TO <?= esc(strtoupper($conferenceTitle)) ?></span>
        </div>
        <a class="mobile-welcome__button" href="<?= site_url('mobile/lobby') ?>">Enter Lobby <i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const frame = document.querySelector('[data-welcome-video]');
    const video = document.querySelector('[data-welcome-player]');
    document.querySelector('[data-welcome-play]')?.addEventListener('click', async () => {
        try { await video.play(); frame.classList.add('is-playing'); } catch (_) {}
    });
    video?.addEventListener('pause', () => frame.classList.remove('is-playing'));
});
</script>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
