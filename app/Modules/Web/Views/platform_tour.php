<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tourCues = $tour_cues ?? [];
?>

<style>
    .platform-tour { position:fixed; z-index:1000; inset:0; box-sizing:border-box; overflow:hidden; background:#110715; color:#fff; }
    .platform-tour__actions { display:flex; align-items:center; gap:8px; padding:7px; border:1px solid rgba(239,177,30,.55); border-radius:16px; background:rgba(22,5,28,.9); box-shadow:0 12px 32px rgba(0,0,0,.35),0 0 22px rgba(216,25,142,.16); backdrop-filter:blur(12px); }
    .platform-tour__exit,.platform-tour__control { min-height:44px; border:1px solid rgba(255,255,255,.28); border-radius:10px; color:#fff; padding:0 16px; background:rgba(35,7,42,.82); text-decoration:none; font:700 12px/1 'JetBrains Mono',monospace; letter-spacing:.11em; white-space:nowrap; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; }
    .platform-tour__control { border-color:rgba(239,177,30,.85); background:linear-gradient(120deg,#8d077f,#d8198e); box-shadow:0 0 18px rgba(216,25,142,.28); }
    .platform-tour__seek { min-width:48px; padding:0 9px; border-color:rgba(255,255,255,.3); background:rgba(35,7,42,.82); box-shadow:none; font-size:10px; }
    .platform-tour__timer { min-width:132px; min-height:44px; box-sizing:border-box; padding:0 13px; border:1px solid rgba(239,177,30,.5); border-radius:10px; background:rgba(6,2,9,.72); color:#fff; font:700 14px/44px 'JetBrains Mono',monospace; letter-spacing:.04em; text-align:center; white-space:nowrap; }
    .platform-tour__stage { position:relative; height:100vh; min-height:0; }
    .platform-tour__guide { position:absolute; z-index:4; bottom:12px; left:clamp(14px,3vw,48px); display:flex; width:min(29vw,370px); height:min(67vh,640px); align-items:flex-end; justify-content:center; overflow:visible; pointer-events:none; }
    .platform-tour__guide::after { content:''; position:absolute; bottom:2%; width:78%; height:10%; border-radius:50%; background:radial-gradient(ellipse,rgba(216,25,142,.42),transparent 68%); filter:blur(12px); }
    .platform-tour__video { position:relative; z-index:1; display:block; width:100%; max-height:100%; object-fit:contain; }
    .platform-tour__sound { position:absolute; z-index:6; bottom:70px; left:50%; transform:translateX(-50%); border:1px solid rgba(255,255,255,.35); border-radius:999px; background:rgba(35,7,42,.9); color:#fff; padding:9px 13px; cursor:pointer; font-size:11px; white-space:nowrap; pointer-events:auto; }
    .platform-tour__guide-controls { position:absolute; z-index:6; bottom:0; left:calc(50% + 24px); width:480px; flex-wrap:nowrap; transform:translateX(-50%); pointer-events:auto; }
    .platform-tour__screen { position:absolute; inset:0; display:flex; min-width:0; height:auto; flex-direction:column; overflow:hidden; border:0; border-radius:0; background:#110715; box-shadow:none; animation:tourPop .5s ease both; }
    .platform-tour__browserbar { display:flex; min-height:46px; padding:0 16px; align-items:center; gap:7px; border-bottom:1px solid rgba(255,255,255,.12); background:rgba(37,13,43,.92); }
    .platform-tour__dot { width:8px; height:8px; border-radius:50%; background:#efb11e; opacity:.85; }
    .platform-tour__label { margin-left:8px; color:rgba(255,255,255,.68); font:600 11px/1 'JetBrains Mono',monospace; letter-spacing:.12em; }
    .platform-tour__frame { width:100%; height:100%; border:0; pointer-events:none; background:#16091a; transform-origin:top left; transition:transform .35s ease,width .35s ease,height .35s ease; }
    .platform-tour__bell-highlight { position:absolute; z-index:3; top:64px; right:90px; width:54px; height:54px; display:grid; place-items:center; border:2px solid #efb11e; border-radius:50%; background:rgba(216,25,142,.24); color:#fff; box-shadow:0 0 0 7px rgba(239,177,30,.16),0 0 30px rgba(216,25,142,.9); pointer-events:none; animation:tourBellPulse 1.05s ease-in-out infinite; }
    .platform-tour__bell-highlight i { font-size:22px; text-shadow:0 0 12px #fff; }
    .platform-tour__bell-highlight::after { content:'NOTIFICATIONS'; position:absolute; top:61px; left:50%; transform:translateX(-50%); padding:5px 8px; border:1px solid rgba(239,177,30,.7); border-radius:999px; background:rgba(21,5,25,.9); color:#fff; font:700 9px/1 'JetBrains Mono',monospace; letter-spacing:.08em; white-space:nowrap; }
    .platform-tour__caption { position:absolute; right:clamp(28px,5vw,86px); bottom:38px; z-index:5; width:min(600px,48vw); padding:16px 20px; border:1px solid rgba(239,177,30,.38); border-radius:14px; background:rgba(21,5,25,.9); box-shadow:0 14px 44px rgba(0,0,0,.35); backdrop-filter:blur(12px); }
    .platform-tour__caption strong { display:block; color:#efb11e; font:600 11px/1 'JetBrains Mono',monospace; letter-spacing:.16em; text-transform:uppercase; }
    .platform-tour__caption span { display:block; margin-top:7px; color:#fff; font-size:14px; line-height:1.45; }
    .platform-tour__progress { position:absolute; right:clamp(28px,5vw,86px); bottom:14px; z-index:5; width:min(600px,48vw); height:4px; overflow:hidden; border-radius:999px; background:rgba(255,255,255,.16); }
    .platform-tour__progress > i { display:block; width:0; height:100%; border-radius:inherit; background:linear-gradient(90deg,#efb11e,#d8198e); transition:width .2s linear; }
    @keyframes tourPop { from { opacity:0; transform:translateY(20px) scale(.97); } to { opacity:1; transform:none; } }
    @keyframes tourBellPulse { 50% { transform:scale(1.12); box-shadow:0 0 0 12px rgba(239,177,30,.1),0 0 40px rgba(216,25,142,1); } }
    @media (max-width:820px) { .platform-tour__actions { gap:6px; flex-wrap:wrap; justify-content:center; } .platform-tour__timer { min-width:116px; } .platform-tour__control,.platform-tour__exit { padding:0 12px; } .platform-tour__seek { padding:0 9px; } .platform-tour__stage { height:100svh; } .platform-tour__guide { left:10px; bottom:8px; width:min(47vw,280px); height:min(47vh,410px); } .platform-tour__guide-controls { left:0; bottom:0; width:min(92vw,500px); transform:none; } .platform-tour__sound { bottom:124px; } .platform-tour__caption { right:16px; bottom:44px; width:min(66vw,420px); padding:12px 14px; } .platform-tour__progress { right:16px; bottom:24px; width:min(66vw,420px); } }
</style>

<main class="platform-tour" data-screen-label="Platform Tour">
    <div class="platform-tour__stage">
        <section class="platform-tour__guide" aria-label="Hakima, your digital conference guide">
            <video class="platform-tour__video" data-tour-video muted playsinline preload="auto">
                <source src="<?php echo esc($hakima_video_url, 'attr'); ?>" type="video/webm">
            </video>
            <button class="platform-tour__sound" type="button" data-tour-sound>Turn sound on</button>
            <div class="platform-tour__actions platform-tour__guide-controls">
                <button class="platform-tour__control platform-tour__seek" type="button" data-tour-seek="-10" aria-label="Go back 10 seconds">−10s</button>
                <output class="platform-tour__timer" data-tour-time>0:00 / 0:00</output>
                <button class="platform-tour__control platform-tour__seek" type="button" data-tour-seek="10" aria-label="Go forward 10 seconds">+10s</button>
                <button class="platform-tour__control" type="button" data-tour-pause>PAUSE</button>
                <a class="platform-tour__exit" href="<?php echo site_url('attendees/lobby'); ?>">SKIP TOUR</a>
            </div>
        </section>
        <section class="platform-tour__screen" data-tour-screen>
            <div class="platform-tour__browserbar"><i class="platform-tour__dot"></i><i class="platform-tour__dot"></i><i class="platform-tour__dot"></i><span class="platform-tour__label" data-tour-title>WELCOME</span></div>
            <iframe class="platform-tour__frame" data-tour-frame title="Portal tour preview" tabindex="-1" aria-hidden="true"></iframe>
            <div class="platform-tour__bell-highlight" data-tour-bell-highlight hidden aria-hidden="true"><i class="fa fa-bell"></i></div>
        </section>
    </div>
    <aside class="platform-tour__caption" aria-live="polite"><strong data-tour-caption-title>Welcome</strong><span data-tour-caption>Welcome to EPR Global’s Accelerate 2026.</span></aside>
    <div class="platform-tour__progress" aria-label="Tour progress"><i data-tour-progress></i></div>
</main>

</div></div>
<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
(() => {
    const cues = <?php echo json_encode($tourCues, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const video = document.querySelector('[data-tour-video]');
    const frame = document.querySelector('[data-tour-frame]');
    const title = document.querySelector('[data-tour-title]');
    const captionTitle = document.querySelector('[data-tour-caption-title]');
    const caption = document.querySelector('[data-tour-caption]');
    const progress = document.querySelector('[data-tour-progress]');
    const sound = document.querySelector('[data-tour-sound]');
    const pause = document.querySelector('[data-tour-pause]');
    const timer = document.querySelector('[data-tour-time]');
    const seekButtons = Array.from(document.querySelectorAll('[data-tour-seek]'));
    const bellHighlight = document.querySelector('[data-tour-bell-highlight]');
    let activeUrl = '';

    function formatTime(seconds) {
        const value = Math.max(0, Math.floor(Number(seconds) || 0));
        return `${Math.floor(value / 60)}:${String(value % 60).padStart(2, '0')}`;
    }

    function showCue(cue) {
        if (!cue) return;
        title.textContent = cue.title.toUpperCase();
        captionTitle.textContent = cue.title;
        caption.textContent = cue.caption;
        bellHighlight.hidden = cue.highlight !== 'notifications';
        if (cue.url !== activeUrl) {
            activeUrl = cue.url;
            frame.src = cue.url;
            frame.closest('[data-tour-screen]').style.animation = 'none';
            requestAnimationFrame(() => { frame.closest('[data-tour-screen]').style.animation = ''; });
        }
        const zoom = Number(cue.zoom) || 1;
        frame.style.width = `${100 / zoom}%`;
        frame.style.height = `${100 / zoom}%`;
        frame.style.transform = `scale(${zoom})`;
    }

    function updateTour() {
        const time = video.currentTime || 0;
        const cue = cues.filter((item) => time >= item.at).at(-1) || cues[0];
        showCue(cue);
        if (video.duration && Number.isFinite(video.duration)) progress.style.width = `${Math.min(100, time / video.duration * 100)}%`;
        timer.textContent = `${formatTime(time)} / ${formatTime(video.duration)}`;
    }

    function updatePauseLabel() {
        pause.textContent = video.paused ? 'PLAY' : 'PAUSE';
    }

    async function enableSound() {
        video.muted = false;
        video.volume = 1;
        try {
            await video.play();
            sound.remove();
        } catch (error) {
            video.muted = true;
            sound.hidden = false;
            video.play().catch(() => {});
        }
    }

    showCue(cues[0]);
    video.addEventListener('timeupdate', updateTour);
    video.addEventListener('loadedmetadata', updateTour);
    video.addEventListener('play', updatePauseLabel);
    video.addEventListener('pause', updatePauseLabel);
    video.addEventListener('loadedmetadata', updatePauseLabel);
    pause.addEventListener('click', () => { if (video.paused) video.play(); else video.pause(); });
    seekButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const seconds = Number(button.dataset.tourSeek) || 0;
            const maximum = Number.isFinite(video.duration) ? video.duration : Infinity;
            video.currentTime = Math.max(0, Math.min(maximum, video.currentTime + seconds));
            updateTour();
        });
    });
    sound.addEventListener('click', enableSound);
    document.addEventListener('pointerdown', enableSound, { once: true });
    enableSound();
})();
</script>
</body></html>
