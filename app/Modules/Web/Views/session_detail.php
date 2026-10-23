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
$dateText = !empty($event['event_date']) ? date('F j, Y', strtotime($event['event_date'])) : 'November 11, 2026';
$start = !empty($event['start_time']) ? date('h:i A', strtotime($event['start_time'])) : '10:00 AM';
$end = !empty($event['end_time']) ? date('h:i A', strtotime($event['end_time'])) : '11:00 AM';
$chatSessionId = (int) ($event['sessions_id'] ?? 0);
$chatAttendeeId = (int) session('attendee_id');
$leadSpeaker = $sessionSpeakers[0] ?? [];
$leadSpeakerName = $leadSpeaker['speaker_name'] ?? 'Kemi Bello';
$leadSpeakerRole = trim((string) ($leadSpeaker['speaker_title'] ?? 'Founder & CEO, Brightspark'));
$leadSpeakerPhoto = !empty($leadSpeaker['speaker_photo'])
    ? base_url('uploads/speakers/' . $leadSpeaker['speaker_photo'])
    : asset_url('images/user.png');
?>

<main data-screen-label="Live Session" style="position:absolute;inset:72px 0 0 0;display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,380px);gap:24px;padding:24px 32px;box-sizing:border-box">
    <section style="min-height:0;overflow-y:auto;overflow-x:hidden;display:flex;flex-direction:column;gap:18px;padding-right:6px">
        <div style="display:flex;flex-direction:column;gap:10px">
            <a href="<?php echo session('live-conference-id') ? base_url('attendees/agenda/' . session('live-conference-id')) : base_url('attendees/lobby'); ?>"
               class="rc-back-link">
                <i class="fa fa-long-arrow-left" aria-hidden="true"></i>Agenda · <?php echo esc($dateText); ?>
            </a>
            <span style="font-size:28px;font-weight:600;letter-spacing:-.02em;line-height:1.15;text-wrap:pretty"><?php echo esc($title); ?></span>
            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;font-size:13px;color:rgba(255,255,255,.75)">
                <span class="rc-mono" style="font-size:12px"><?php echo esc($start); ?> - <?php echo esc($end); ?> · <?php echo esc($timezone); ?></span>
                <span style="display:flex;align-items:center;gap:6px"><i class="fa fa-eye" aria-hidden="true"></i>1,284 watching</span>
            </div>
        </div>

        <div data-session-video data-video-src="<?php echo esc($canAccess ? $videoSrc : '', 'attr'); ?>" style="position:relative;width:100%;box-sizing:border-box;aspect-ratio:16/9;flex:none;border-radius:16px;overflow:hidden;background:radial-gradient(circle at 50% 42%,#5a1f5f 0%,#2a0f2e 48%,#12050f 100%);border:1px solid rgba(255,255,255,.12)">
            <img src="<?php echo $leadSpeakerPhoto; ?>" alt="<?php echo esc($leadSpeakerName); ?>" style="position:absolute;left:50%;top:36%;transform:translate(-50%,-50%);height:40%;aspect-ratio:1;border-radius:50%;object-fit:cover;box-shadow:0 0 80px rgba(216,25,142,.45)">
            <div style="position:absolute;top:16px;left:16px;display:flex;gap:8px;align-items:center">
                <span style="display:flex;align-items:center;gap:6px;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.16em;padding:5px 10px;border-radius:6px;background:#d8198e"><span style="width:6px;height:6px;border-radius:50%;background:#fff"></span>LIVE</span>
                <span class="rc-mono" style="font-size:11px;letter-spacing:.1em;padding:5px 10px;border-radius:6px;background:rgba(0,0,0,.45)">23:41</span>
            </div>
            <div style="position:absolute;left:16px;bottom:52px;max-width:calc(100% - 32px);box-sizing:border-box;display:flex;align-items:baseline;gap:8px;padding:6px 12px;border-radius:8px;background:rgba(18,5,15,.72);white-space:nowrap;overflow:hidden">
                <span style="font-size:14px;font-weight:600"><?php echo esc($leadSpeakerName); ?></span>
                <span style="font-size:12px;color:rgba(255,255,255,.75);overflow:hidden;text-overflow:ellipsis"><?php echo esc($leadSpeakerRole); ?></span>
            </div>
            <div data-session-video-controls style="position:absolute;left:0;right:0;bottom:0;padding:28px 18px 14px;background:linear-gradient(180deg,rgba(0,0,0,0),rgba(0,0,0,.72));display:flex;align-items:center;gap:16px;font-size:16px">
                <button type="button" data-session-video-play aria-label="<?php echo $canAccess ? 'Play session' : 'Session locked'; ?>" style="width:16px;padding:0;border:0;background:transparent;color:#fff;cursor:<?php echo $canAccess ? 'pointer' : 'default'; ?>"><i class="fa <?php echo $canAccess ? 'fa-play' : 'fa-lock'; ?>" aria-hidden="true"></i></button>
                <i class="fa fa-volume-up" aria-hidden="true"></i>
                <div style="flex:1;height:4px;border-radius:4px;background:rgba(255,255,255,.2);position:relative">
                    <div style="position:absolute;inset:0;border-radius:4px;background:#d8198e"></div>
                    <span style="position:absolute;right:-6px;top:-4px;width:12px;height:12px;border-radius:50%;background:#fff;box-shadow:0 0 10px #d8198e"></span>
                </div>
                <i class="fa fa-cc" aria-hidden="true"></i>
                <i class="fa fa-cog" aria-hidden="true"></i>
                <i class="fa fa-expand" aria-hidden="true"></i>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap">
                <button class="rc-session-like" type="button"><i class="fa fa-heart-o" style="color:#ff5fb4" aria-hidden="true"></i>Like Session</button>
                <div style="display:flex;align-items:center;gap:10px">
                    <span style="font-size:13px;color:rgba(255,255,255,.75)">Rate this session</span>
                    <div style="display:flex;gap:2px;color:rgba(255,255,255,.3);font-size:22px;line-height:1">
                        <span style="color:#efb11e">★</span><span style="color:#efb11e">★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <button class="rc-session-download" type="button"><i class="fa fa-download" aria-hidden="true"></i>Download Workbook</button>
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
            <span id="chat-empty" style="margin:auto;color:rgba(255,255,255,.58);font-size:13px">No messages yet. Start the conversation.</span>
        </div>
        <div style="display:flex;gap:8px;padding:12px;border-top:1px solid rgba(255,255,255,.08)">
            <div style="position:relative;flex:none">
                <button id="chatEmojiToggle" type="button" aria-label="Choose an emoji" aria-expanded="false" style="width:44px;height:44px;border:0;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;color:rgba(255,255,255,.75);background:transparent;cursor:pointer"><i class="fa fa-smile-o" aria-hidden="true"></i></button>
                <div id="chatEmojiPicker" hidden role="dialog" aria-label="Emoji picker" style="position:absolute;z-index:20;bottom:52px;left:0;width:216px;padding:8px;border:1px solid rgba(255,255,255,.16);border-radius:12px;background:#29112e;box-shadow:0 14px 32px rgba(0,0,0,.34);grid-template-columns:repeat(6,1fr);gap:3px">
                    <?php foreach (['😀', '😂', '😍', '🥳', '👍', '👏', '🔥', '🎉', '💡', '✅', '🙌', '💯', '🤝', '❤️', '🙏', '👀', '🚀', '✨'] as $emoji): ?>
                        <button type="button" data-chat-emoji="<?php echo esc($emoji, 'attr'); ?>" aria-label="Add <?php echo esc($emoji, 'attr'); ?>" style="border:0;border-radius:7px;background:transparent;padding:6px;cursor:pointer;font-size:19px;line-height:1"><?php echo $emoji; ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <input id="chatInput" class="rc-input" placeholder="Type a message..." style="flex:1;min-width:0;height:44px;background:#1d0a21!important">
            <button id="sendChat" class="rc-pill" type="button" style="height:44px;padding:0 18px;flex:none">Send</button>
        </div>
    </aside>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const sessionVideo = document.querySelector("[data-session-video]");
    const sessionPlay = document.querySelector("[data-session-video-play]");
    const input = document.getElementById("chatInput");
    const box = document.getElementById("chat-messages");
    const sendButton = document.getElementById("sendChat");
    const emojiToggle = document.getElementById("chatEmojiToggle");
    const emojiPicker = document.getElementById("chatEmojiPicker");
    const empty = document.getElementById("chat-empty");
    const sessionId = <?php echo json_encode($chatSessionId); ?>;
    const attendeeId = <?php echo json_encode($chatAttendeeId); ?>;
    const currentAttendeeName = <?php echo json_encode(session('firstname') ?: 'You'); ?>;
    const historyUrl = <?php echo json_encode(base_url('api/chat/' . $chatSessionId)); ?>;
    const sendUrl = <?php echo json_encode(base_url('api/chat/send/' . $chatSessionId)); ?>;
    const renderedMessageIds = new Set();
    let latestMessageId = 0;

    sessionPlay?.addEventListener("click", function() {
        const src = sessionVideo?.dataset.videoSrc || "";
        if (!src || sessionVideo.querySelector("iframe")) {
            return;
        }
        const iframe = document.createElement("iframe");
        iframe.src = src + (src.includes("?") ? "&" : "?") + "autoplay=1";
        iframe.allow = "autoplay; fullscreen; picture-in-picture";
        iframe.allowFullscreen = true;
        iframe.style.cssText = "position:absolute;inset:0;z-index:4;width:100%;height:100%;border:0";
        sessionVideo.appendChild(iframe);
    });

    function appendMessage(message) {
        const messageId = Number(message.id || 0);
        if (messageId && renderedMessageIds.has(messageId)) {
            return;
        }
        if (messageId) {
            renderedMessageIds.add(messageId);
            latestMessageId = Math.max(latestMessageId, messageId);
        }

        const isMine = Number(message.attendee_id || 0) === attendeeId;
        const row = document.createElement("div");
        row.style.cssText = "display:flex;align-items:flex-end;gap:8px" + (isMine ? ";justify-content:flex-end" : "");

        const bubble = document.createElement("div");
        bubble.style.cssText = "max-width:82%;padding:9px 13px;border-radius:" + (isMine ? "12px 12px 4px 12px" : "12px 12px 12px 4px") + ";background:" + (isMine ? "#d8198e" : "#742c76") + ";font-size:13px;line-height:1.45";
        const name = document.createElement("strong");
        name.textContent = isMine ? currentAttendeeName : (message.attendee_name || "Attendee");
        bubble.appendChild(name);
        bubble.appendChild(document.createElement("br"));
        bubble.appendChild(document.createTextNode(message.message || ""));
        row.appendChild(bubble);
        empty?.remove();
        box.appendChild(row);
        box.scrollTop = box.scrollHeight;
    }

    function loadMessages() {
        if (!sessionId) {
            return Promise.resolve();
        }

        const url = new URL(historyUrl, window.location.origin);
        if (latestMessageId) {
            url.searchParams.set("after", String(latestMessageId));
        }

        return fetch(url.toString(), { credentials: "same-origin" })
            .then(response => response.ok ? response.json() : Promise.reject(new Error("chat-history-failed")))
            .then(payload => {
                const messages = Array.isArray(payload?.data) ? payload.data : [];
                messages.forEach(appendMessage);
            })
            .catch(() => {});
    }

    function sendMessage() {
        const text = input.value.trim();
        if (!text || !sessionId || sendButton?.disabled) {
            return;
        }

        sendButton.disabled = true;
        sendButton.textContent = "Sending...";
        fetch(sendUrl, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message: text })
        })
            .then(response => response.ok ? response.json() : Promise.reject(new Error("chat-send-failed")))
            .then(() => {
                input.value = "";
                return loadMessages();
            })
            .catch(() => {
                sendButton.textContent = "Try again";
            })
            .finally(() => {
                sendButton.disabled = false;
                if (sendButton.textContent !== "Try again") {
                    sendButton.textContent = "Send";
                }
            });
    }

    function insertEmoji(emoji) {
        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? start;
        input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
        const cursor = start + emoji.length;
        input.focus();
        input.setSelectionRange(cursor, cursor);
    }

    sendButton?.addEventListener("click", sendMessage);
    emojiToggle?.addEventListener("click", () => {
        const isOpen = !emojiPicker.hidden;
        emojiPicker.hidden = isOpen;
        emojiPicker.style.display = isOpen ? "none" : "grid";
        emojiToggle.setAttribute("aria-expanded", String(!isOpen));
    });
    emojiPicker?.addEventListener("click", event => {
        const button = event.target.closest("[data-chat-emoji]");
        if (!button) return;
        insertEmoji(button.dataset.chatEmoji || "");
        emojiPicker.hidden = true;
        emojiPicker.style.display = "none";
        emojiToggle?.setAttribute("aria-expanded", "false");
    });
    document.addEventListener("click", event => {
        if (emojiPicker && !emojiPicker.hidden && !emojiPicker.contains(event.target) && !emojiToggle?.contains(event.target)) {
            emojiPicker.hidden = true;
            emojiPicker.style.display = "none";
            emojiToggle?.setAttribute("aria-expanded", "false");
        }
    });
    input?.addEventListener("keydown", event => {
        if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    });

    loadMessages();
    window.setInterval(loadMessages, 3000);
});
</script>
</body>
</html>
