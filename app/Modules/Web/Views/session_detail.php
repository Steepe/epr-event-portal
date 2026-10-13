<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$event = $session ?? [];
$sessionSpeakers = $speakers ?? [];
$sessionSponsor = $sponsors[0] ?? null;
$timezone = $timezone ?? 'Africa/Lagos';
$canAccess = (session('plan') ?? 1) >= ($event['access_level'] ?? 1);
$vimeoId = trim($event['vimeo_id'] ?? '');
$videoSrc = '';

if (!empty($vimeoId)) {
    if (is_numeric($vimeoId)) {
        $videoSrc = 'https://player.vimeo.com/video/' . $vimeoId;
    } elseif (str_contains($vimeoId, 'event')) {
        $videoSrc = 'https://vimeo.com/' . $vimeoId . '/embed';
    } else {
        $videoSrc = 'https://vimeo.com/event/' . $vimeoId . '/embed';
    }
}

$title = $event['sessions_name'] ?? 'Designing Systems That Outlast Motivation';
$dateText = !empty($event['event_date']) ? date('F j, Y', strtotime($event['event_date'])) : 'November 21, 2026';
$start = !empty($event['start_time']) ? date('h:i A', strtotime($event['start_time'])) : '02:00 PM';
$end = !empty($event['end_time']) ? date('h:i A', strtotime($event['end_time'])) : '11:59 PM';
?>

<main data-screen-label="Live Session" style="position:absolute;inset:72px 0 0 0;display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:24px;padding:24px 32px;box-sizing:border-box">
    <section style="min-height:0;overflow-y:auto;overflow-x:hidden;display:flex;flex-direction:column;gap:18px;padding-right:6px">
        <div style="display:flex;flex-direction:column;gap:10px">
            <a href="<?php echo session('live-conference-id') ? base_url('attendees/agenda/' . session('live-conference-id')) : base_url('attendees/lobby'); ?>"
               class="rc-kicker" style="align-self:flex-start;display:flex;gap:8px;align-items:center;font-size:11px">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>Agenda · <?php echo esc($dateText); ?>
            </a>
            <span style="font-size:28px;font-weight:600;letter-spacing:-.02em;line-height:1.15"><?php echo esc($title); ?></span>
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;font-size:13px;color:rgba(255,255,255,.75)">
                <span class="rc-mono" style="font-size:12px"><?php echo esc($start); ?> - <?php echo esc($end); ?> · <?php echo esc($timezone); ?></span>
                <span style="display:flex;align-items:center;gap:6px"><i class="fa fa-eye" aria-hidden="true"></i>1,284 watching</span>
            </div>
        </div>

        <div style="position:relative;width:100%;box-sizing:border-box;aspect-ratio:16/9;flex:none;border-radius:16px;overflow:hidden;background:radial-gradient(circle at 50% 42%,#5a1f5f 0%,#2a0f2e 48%,#12050f 100%);border:1px solid rgba(255,255,255,.12)">
            <?php if ($canAccess && !empty($videoSrc)): ?>
                <iframe src="<?php echo esc($videoSrc); ?>"
                        allow="autoplay; fullscreen; picture-in-picture"
                        allowfullscreen
                        style="position:absolute;inset:0;width:100%;height:100%;border:0"></iframe>
            <?php else: ?>
                <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="Accelerate" style="position:absolute;left:50%;top:38%;transform:translate(-50%,-50%);width:min(430px,60%);height:auto">
                <span style="position:absolute;width:84px;height:84px;left:50%;top:50%;transform:translate(-50%,-50%);border-radius:50%;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);display:flex;align-items:center;justify-content:center;font-size:30px;padding-left:6px;box-sizing:border-box;box-shadow:0 0 40px rgba(216,25,142,.6)">
                    <i class="fa <?php echo $canAccess ? 'fa-play' : 'fa-lock'; ?>" aria-hidden="true"></i>
                </span>
            <?php endif; ?>
            <div style="position:absolute;top:16px;left:16px;display:flex;gap:8px;align-items:center">
                <span style="display:flex;align-items:center;gap:6px;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.16em;padding:5px 10px;border-radius:6px;background:#d8198e"><span style="width:6px;height:6px;border-radius:50%;background:#fff"></span>LIVE</span>
                <span class="rc-mono" style="font-size:11px;letter-spacing:.1em;padding:5px 10px;border-radius:6px;background:rgba(0,0,0,.45)">23:41</span>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap">
                <button class="rc-outline-pill" type="button"><i class="fa fa-heart-o" style="color:#ff5fb4" aria-hidden="true"></i>Like Session</button>
                <div style="display:flex;align-items:center;gap:10px">
                    <span style="font-size:13px;color:rgba(255,255,255,.75)">Rate this session</span>
                    <div style="display:flex;gap:2px;color:rgba(255,255,255,.3);font-size:22px;line-height:1">
                        <span style="color:#efb11e">★</span><span style="color:#efb11e">★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <button class="rc-pill" type="button"><i class="fa fa-download" aria-hidden="true"></i>Download Workbook</button>
                <?php if ($sessionSponsor): ?>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span style="font-size:12px;color:rgba(255,255,255,.65)">Sponsored by</span>
                        <span style="height:38px;width:96px;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:center">
                            <img src="<?php echo base_url('uploads/sponsors_logo/' . $sessionSponsor['logo']); ?>" alt="Sponsor" style="max-width:84%;max-height:78%;object-fit:contain">
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.2fr);gap:18px">
            <div class="rc-panel" style="padding:20px;display:flex;flex-direction:column;gap:14px">
                <span class="rc-kicker" style="font-size:11px;color:rgba(255,255,255,.6)">Speakers</span>
                <?php if (!empty($sessionSpeakers)): ?>
                    <?php foreach ($sessionSpeakers as $speaker): ?>
                        <?php $speakerPhoto = !empty($speaker['speaker_photo']) ? base_url('uploads/speakers/' . $speaker['speaker_photo']) : asset_url('images/user.png'); ?>
                        <div style="display:flex;align-items:center;gap:14px">
                            <img src="<?php echo $speakerPhoto; ?>" alt="" style="width:60px;height:60px;border-radius:50%;object-fit:cover">
                            <div style="display:flex;flex-direction:column;gap:2px">
                                <span style="font-size:15px;font-weight:600"><?php echo esc($speaker['speaker_name'] ?? 'Speaker'); ?></span>
                                <span style="font-size:12px;color:rgba(255,255,255,.7)"><?php echo esc($speaker['speaker_title'] ?? ''); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span style="color:rgba(255,255,255,.7)">Speakers TBA</span>
                <?php endif; ?>
            </div>

            <div class="rc-panel" style="padding:20px;display:flex;flex-direction:column;gap:12px">
                <span class="rc-kicker" style="font-size:11px;color:rgba(255,255,255,.6)">Session overview</span>
                <p style="margin:0;font-size:14px;line-height:1.65;color:rgba(255,255,255,.86)">
                    <?php echo esc($event['description'] ?? 'Motivation fades; systems compound. This session breaks down how high-performing teams design operating rhythms, feedback loops and simple rules that keep execution moving.'); ?>
                </p>
            </div>
        </div>
    </section>

    <aside class="rc-panel" style="min-height:0;display:flex;flex-direction:column;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.08)">
            <span style="font-size:16px;font-weight:600">Live chat</span>
            <span class="rc-mono" style="display:flex;align-items:center;gap:8px;font-size:11px;letter-spacing:.12em;color:rgba(255,255,255,.7)"><span style="width:7px;height:7px;border-radius:50%;background:#efb11e;box-shadow:0 0 8px #efb11e"></span>1,284 ONLINE</span>
        </div>
        <div id="chat-messages" style="flex:1;min-height:0;overflow-y:auto;overflow-x:hidden;padding:16px;display:flex;flex-direction:column;gap:12px">
            <div style="display:flex;align-items:flex-end;gap:8px"><img src="<?php echo asset_url('images/user.png'); ?>" alt="" style="width:30px;height:30px;border-radius:50%"><div style="max-width:82%;padding:9px 13px;border-radius:12px 12px 12px 4px;background:#742c76;font-size:13px;line-height:1.45"><strong>Chidi Nwosu</strong><br>Audio is crystal clear.</div></div>
            <div style="display:flex;align-items:flex-end;gap:8px;justify-content:flex-end"><div style="max-width:82%;padding:9px 13px;border-radius:12px 12px 4px 12px;background:#d8198e;font-size:13px;line-height:1.45"><strong><?php echo esc(session('firstname') ?: 'You'); ?></strong><br>Taking notes for our Monday ops review.</div></div>
        </div>
        <div style="display:flex;gap:8px;padding:12px;border-top:1px solid rgba(255,255,255,.08)">
            <span style="width:44px;height:44px;flex:none;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:rgba(255,255,255,.75)"><i class="fa fa-smile-o" aria-hidden="true"></i></span>
            <input id="chatInput" class="rc-input" placeholder="Type a message..." style="flex:1;min-width:0;height:44px;background:#1d0a21!important">
            <button id="sendChat" class="rc-pill" type="button" style="height:44px">Send</button>
        </div>
    </aside>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("chatInput");
    const box = document.getElementById("chat-messages");
    document.getElementById("sendChat")?.addEventListener("click", () => {
        const text = input.value.trim();
        if (!text) return;
        const row = document.createElement("div");
        row.style.cssText = "display:flex;align-items:flex-end;gap:8px;justify-content:flex-end";
        row.innerHTML = `<div style="max-width:82%;padding:9px 13px;border-radius:12px 12px 4px 12px;background:#d8198e;font-size:13px;line-height:1.45"><strong><?php echo esc(session('firstname') ?: 'You'); ?></strong><br>${text.replace(/</g, "&lt;")}</div>`;
        box.appendChild(row);
        input.value = "";
        box.scrollTop = box.scrollHeight;
    });
});
</script>
</body>
</html>
