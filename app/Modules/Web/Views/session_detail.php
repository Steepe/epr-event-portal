<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 29/10/2025
 * Time: 21:01
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$event           = $session ?? [];
$sessionSpeakers = $speakers ?? [];
$sessionSponsor  = $sponsors[0] ?? null;
$timezone        = $timezone ?? 'Africa/Lagos';

$canAccess = (session('plan') ?? 1) >= ($event['access_level'] ?? 1);
$vimeo_id  = trim($event['vimeo_id'] ?? '');
$videoSrc  = ''; // default

// 🎥 Auto-detect correct Vimeo embed URL
if (!empty($vimeo_id)) {
    if (is_numeric($vimeo_id)) {
        // Standard video
        $videoSrc = "https://player.vimeo.com/video/" . $vimeo_id;
    } elseif (str_contains($vimeo_id, 'event')) {
        // Already includes 'event'
        $videoSrc = "https://vimeo.com/{$vimeo_id}/embed";
    } else {
        // Possibly an event ID only
        $videoSrc = "https://vimeo.com/event/{$vimeo_id}/embed";
    }
}
?>


<style>
    .container {
        max-width: 1400px !important; /* default is ~1140 */
    }

    .nav-pills .nav-link.active {
        color: #fff;
        background-color: #9D0F82;
        border-radius: 10px;
        width: 170px;
        text-align: center;
    }

    .nav-pills .nav-link {
        background-color: #D8198E;
        border-radius: 10px;
        color: #FFFFFF;
        width: 170px;
        border: none;
        text-align: center;
    }

    .wy-message-bubble { background-color: #9D0F82 !important; }
    .wy-content { color: #FFFFFF !important; }
    .wy-like-button, .wy-toast-action { background-color: #D8198E !important; color: #fff !important; }

    :root { --wy-theme-color: #D8198E; }

    .speaker-summary-bg { margin-bottom: 15px; }
    .speaker-summary img { border-radius: 10px; object-fit: cover; }
</style>

<style>
    .chat-bubble-wrapper {
        display: flex;
        margin-bottom: 14px;
        align-items: flex-end;
    }

    .chat-bubble-wrapper.me {
        justify-content: flex-end;
    }

    .chat-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        margin-right: 8px;
        object-fit: cover;
    }

    .chat-bubble {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.4;
        position: relative;
        background: #333;
        color: #fff;
    }

    /* Me (my messages) */
    .chat-bubble.me {
        background: #D8198E;
        margin-left: 40px;
        border-bottom-right-radius: 4px;
        width: 100%;
    }

    /* Them */
    .chat-bubble.them {
        background: #222;
        border-bottom-left-radius: 4px;
        width: 100%;
    }

    /* Name */
    .chat-name {
        font-weight: bold;
        margin-bottom: 4px;
        font-size: 13px;
    }

    /* Timestamp */
    .chat-time {
        font-size: 10px;
        color: #ccc;
        margin-top: 6px;
        text-align: right;
    }

    /* WhatsApp-style bubble tail */
    .chat-bubble.me::after {
        content: "";
        position: absolute;
        right: -8px;
        bottom: 0;
        width: 0;
        height: 0;
        border-left: 8px solid #D8198E;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
    }

    .chat-bubble.them::before {
        content: "";
        position: absolute;
        left: -8px;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 8px solid #222;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
    }
</style>

<div class="container">
    <div class="row mt-5 pt-50 w-100 mb-1">
        <h4 style="color: #9D0F82; font-weight: bold;">Session</h4>
    </div>

    <div class="row w-100">
        <div class="col-md-8">
            <h5 class="text-white"><?php echo  esc($event['sessions_name'] ?? '') ?></h5>

            <div class="view-box mb-3">
                    <div style="padding:56.25% 0 0 0;position:relative;">
                        <iframe src="<?php echo  esc($videoSrc) ?>"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen
                                style="position:absolute;top:0;left:0;width:100%;height:100%;">
                        </iframe>
                    </div>
            </div>

            <div class="row">
                <div class="col-md-6 text-left">
                    <span class="epr-text-purple like-session like-outline" style="font-size:16px; font-weight:bold; cursor:pointer;">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                    </span>
                    <span class="epr-text-purple like-session like-fill" style="font-size:16px; font-weight:bold; cursor:pointer; display:none;">
                        <i class="fa fa-heart" aria-hidden="true"></i>
                    </span>
                    <span class="epr-text-purple mt-1">&nbsp;Like Session</span>
                </div>
                <div class="col-md-6">
                    <form class="rating" style="float: right;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label>
                                <input type="radio" name="stars" value="<?php echo  $i ?>" />
                                <?php echo  str_repeat('<span class="icon">★</span>', $i) ?>
                            </label>
                        <?php endfor; ?>
                    </form>
                    <p class="text-white" style="float:right; margin-top:5px;">Rate this session</p>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <?php foreach ($sessionSpeakers as $speaker): ?>
                        <div class="speaker-summary-bg">
                            <div class="speaker-summary d-flex">
                                <div>
                                    <img src="<?php echo  base_url('uploads/speaker_pictures/' . $speaker['speaker_photo']) ?>"
                                         alt="<?php echo  esc($speaker['speaker_name']) ?>"
                                         onerror="this.src='<?php echo  asset_url('images/user.png') ?>';"
                                         style="width:120px;height:120px;">
                                </div>
                                <div class="text-center pt-2 pl-2">
                                    <h6><strong><?php echo  esc($speaker['speaker_name']) ?></strong></h6>
                                    <p style="font-size:12px;"><?php echo  esc($speaker['speaker_title'] . ', ' . $speaker['speaker_company']) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="col-md-6">
                    <h6 class="text-white"><strong>Session Overview</strong></h6>
                    <p class="text-white" style="font-size:14px;"><?php echo  nl2br(esc($event['description'] ?? '')) ?></p>

                    <?php if (!empty($event['workbook'])): ?>
                        <a href="<?php echo  base_url('uploads/workbooks/' . $event['workbook']) ?>" download>
                            <button class="btn btn-epr-pink mb-3">Download Workbook</button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <aside class="col-md-4 mt-4" id="chat_pane">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-toggle="pill" data-target="#chat-home">LIVE CHAT</button></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="chat-home">
                    <div id="chat-containerx" style="height:530px;">
                        <div id="chat-messages"
                             style="height:480px; overflow-y:auto; background:#111; padding:10px; border-radius:10px;">
                        </div>

                        <div class="mt-2 d-flex">
    <textarea id="chatInput" class="form-control" placeholder="Type a message..."
              style="background:#222; color:#fff; border-radius:10px; height:55px;"></textarea>
                            <button class="btn btn-epr-pink ml-2" onclick="sendMessage()"
                                    style="height:55px; border-radius:10px;">Send</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($sessionSponsor): ?>
                <div class="row w-100 mt-2 mb-2 pl-4">
                    <h6>Sponsored by</h6>&nbsp;&nbsp;
                    <span><img src="<?php echo  base_url('uploads/sponsors_logo/' . $sessionSponsor['logo']) ?>" width="100"></span>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php
echo module_view('Web', 'includes/scripts');
?>

<script src="<?php echo  asset_url('js/toast.min.js') ?>"></script>

<script>

    $(document).ready(function(){
        let liked = false;

        $('.like-session').on('click', function(){
            if (!liked) {
                liked = true;
                $('.like-fill').show();
                $('.like-outline').hide();

                $.get("<?php echo  base_url('attendees/attendees/save_earned_points') ?>", {
                    short_name: "favorite_session",
                    activity: "<?php echo  $event['sessions_id'] ?? '' ?>"
                });
            }
        });

        $(':radio').change(function() {
            const session_rate = this.value;
            $.get("<?php echo  base_url('attendees/attendees/save_earned_points') ?>", {
                short_name: "rate_session",
                activity: "<?php echo  $event['sessions_id'] ?? '' ?>"
            }, function(data){
                if (data === "false") {
                    alert('You have rated this session before.');
                } else {
                    $.get("<?php echo  base_url('attendees/attendees/save_session_rating') ?>", {
                        session_rate: session_rate,
                        session_id: "<?php echo  $event['sessions_id'] ?? '' ?>"
                    });
                }
            });
        });
    });


</script>

<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<!-- Emoji Picker Web Component -->
<script type="module" src="https://unpkg.com/emoji-picker-element@latest/index.js"></script>

<!-- Tribute.js (Mentions) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css">
<script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>

<script>
    /* ===========================================================
       CLEAN CHAT ENGINE — Supabase Realtime + Emoji + Mentions
       =========================================================== */

    const supabaseClient = supabase.createClient(
        "<?php echo getenv('supabase.url'); ?>",
        "<?php echo getenv('supabase.anon_key'); ?>"
    );

    const sessionId   = "<?php echo $event['sessions_id']; ?>";
    const attendeeId  = "<?php echo session('attendee_id'); ?>";
    const apiEndpoint = "/api/chat/send/" + sessionId;

    const chatBox   = document.getElementById("chat-messages");
    const chatInput = document.getElementById("chatInput");

    let profileCache = {};
    // mention lookup: id -> "Full Name"
    window._mentionLookup = {};

    /* ----------------------------
       Auto-scroll to bottom
    ----------------------------- */
    function scrollChatDown() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    /* ----------------------------
       Escape HTML (safe rendering)
    ----------------------------- */
    function sanitize(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/\n/g, "<br>");
    }

    /* ----------------------------
       Replace stored tokens @{user:ID} with styled full names
       Uses window._mentionLookup (populated when mentions load) as primary source.
       Falls back to profileCache if needed.
    ----------------------------- */
    function replaceTokensWithNames(text) {
        if (!text) return text;
        return String(text).replace(/@\{user:(\d+)\}/g, (match, id) => {
            const lookup = window._mentionLookup || {};
            if (lookup[id]) {
                return `<span style="color:#D8198E;font-weight:600;">@${sanitize(lookup[id])}</span>`;
            }
            const p = profileCache[id];
            if (p) {
                return `<span style="color:#D8198E;font-weight:600;">@${sanitize((p.firstname||'') + ' ' + (p.lastname||''))}</span>`;
            }
            return match; // leave token if no name available
        });
    }

    /* ----------------------------
       Load user profile (cached)
    ----------------------------- */
    async function getUserProfile(userId) {
        if (profileCache[userId]) return profileCache[userId];

        try {
            let res = await fetch("/api/users/" + userId, {
                headers: { "X-API-KEY": "<?php echo env('api.securityKey'); ?>" }
            });

            let json = await res.json();
            if (json.status === "success") {
                profileCache[userId] = json.data;
                return json.data;
            }
        } catch (e) {
            console.warn("Profile load failed", e);
        }

        return profileCache[userId] = {
            firstname: "User",
            lastname: userId,
            profile_picture: ""
        };
    }

    function replaceMentionTokens(msg) {
        return msg.replace(/@\{user:(\d+)\}/g, function(_, uid) {
            const user = profileCache[uid];
            if (!user) return "@Unknown";

            const name = `${user.firstname} ${user.lastname}`.trim();
            return `<span class="mention">@${name}</span>`;
        });
    }

    /* ----------------------------
       Render a chat bubble (now replaces tokens with names)
    ----------------------------- */
    async function appendMessage(data) {
        const user = await getUserProfile(data.attendee_id);
        const isMe = Number(data.attendee_id) === Number(attendeeId);

        const fullName = `${user.firstname ?? ''} ${user.lastname ?? ''}`.trim();
        const avatar = user.profile_picture
            ? "<?php echo base_url('uploads/profile_pictures'); ?>/" + user.profile_picture
            : "<?php echo asset_url('images/user.png'); ?>";

        // Convert stored mentions @{user:ID} → @Full Name
        const renderedMessage = replaceTokensWithNames(sanitize(data.message));

        const bubble = `
        <div class="chat-bubble-wrapper ${isMe ? 'me' : 'them'}">
            ${!isMe ? `<img src="${avatar}" class="chat-avatar" onerror="this.src='<?php echo asset_url('images/user.png'); ?>'">` : ""}
            <div class="chat-bubble ${isMe ? 'me' : 'them'}">
                <div class="chat-name">${fullName}</div>
                ${renderedMessage}
                <div class="chat-time">
                    ${new Date(data.created_at).toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' })}
                </div>
            </div>
        </div>
    `;

        chatBox.insertAdjacentHTML("beforeend", bubble);
        scrollChatDown();
    }

    /* ----------------------------
       Load chat history
    ----------------------------- */
    async function loadChatHistory() {
        const url = "<?php echo getenv('supabase.url'); ?>/rest/v1/tbl_session_chats"
            + "?session_id=eq.<?php echo $event['sessions_id']; ?>&order=id.asc";

        const response = await fetch(url, {
            headers: {
                "apikey": "<?php echo getenv('supabase.anon_key'); ?>",
                "Authorization": "Bearer <?php echo getenv('supabase.anon_key'); ?>"
            }
        });

        const messages = await response.json();

        for (let msg of messages) {
            await appendMessage(msg);
        }
    }

    /* ----------------------------
       SEND MESSAGE (converts displayed @Name -> @{user:ID})
    ----------------------------- */
    async function sendMessage() {
        let msg = chatInput.value.trim();
        if (!msg.length) return;

        // Replace @Full Name with @{user:ID}
        for (let uid in profileCache) {
            let u = profileCache[uid];
            let fullname = `${u.firstname} ${u.lastname}`.trim();

            if (fullname.length > 0) {
                const regex = new RegExp("@" + fullname.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), "g");
                msg = msg.replace(regex, `@{user:${uid}}`);
            }
        }

        await fetch(apiEndpoint, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                attendee_id: attendeeId,
                message: msg
            })
        });

        chatInput.value = "";
    }

    window.sendMessage = sendMessage;

    /* ----------------------------
       ENTER to Send (Desktop)
    ----------------------------- */
    chatInput.addEventListener("keydown", function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    /* ----------------------------
       REALTIME new messages
    ----------------------------- */
    supabaseClient
        .channel("session-" + sessionId)
        .on("postgres_changes", {
            event: "INSERT",
            schema: "public",
            table: "tbl_session_chats",
            filter: "session_id=eq." + sessionId
        }, payload => {
            appendMessage(payload.new);
        })
        .subscribe();

    /* ----------------------------
       MENTIONS (@username)
       - builds window._mentionLookup
       - shows @Full Name in textarea immediately after selection
    ----------------------------- */
    async function initMentions() {
        try {
            const res = await fetch('/api/attendees/all', {
                headers: { 'X-API-KEY': '<?php echo env("api.securityKey"); ?>' }
            });

            const json = await res.json();

            const values = json.data.map(a => ({
                key: `${a.firstname} ${a.lastname}`,   // name shown in dropdown
                id: a.id,                              // keep ID for replacement
            }));

            const tribute = new Tribute({
                trigger: "@",
                lookup: "key",
                values: values,
                selectTemplate: item => item.original.key,   // insert full name visibly
                menuItemTemplate: item => item.string
            });

            tribute.attach(chatInput);

        } catch (err) {
            console.warn("Mentions failed", err);
        }
    }

    /* ----------------------------
       EMOJI PICKER
    ----------------------------- */
    function initEmojiPicker() {
        const emojiButton = document.createElement("button");
        emojiButton.innerHTML = "😊";
        emojiButton.className = "btn btn-sm";
        emojiButton.style.marginLeft = "8px";

        chatInput.parentElement.appendChild(emojiButton);

        const picker = document.createElement("emoji-picker");
        picker.style.position = "absolute";
        picker.style.zIndex = "9999";
        picker.style.bottom = "60px";
        picker.style.right = "0";
        picker.style.display = "none";

        chatInput.parentElement.appendChild(picker);

        emojiButton.addEventListener("click", () => {
            picker.style.display = picker.style.display === "none" ? "block" : "none";
        });

        picker.addEventListener("emoji-click", event => {
            const emoji = event.detail.unicode;
            const start = chatInput.selectionStart;
            const end   = chatInput.selectionEnd;
            chatInput.value =
                chatInput.value.substring(0, start) +
                emoji +
                chatInput.value.substring(end);
            chatInput.selectionStart = chatInput.selectionEnd = start + emoji.length;
            picker.style.display = "none";
        });
    }

    /* ----------------------------
       INIT CHAT
    ----------------------------- */
    (async function initChat() {
        await loadChatHistory();
        await initMentions();
        initEmojiPicker();
        scrollChatDown();
    })();
</script>


</body>
</html>
