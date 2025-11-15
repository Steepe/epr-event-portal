<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:56
 */

echo module_view('MobileApp', 'includes/header');

$event           = $session ?? [];
$sessionSpeakers = $speakers ?? [];
$timezone        = $timezone ?? 'Africa/Lagos';
$plan            = (int)(session('plan') ?? 1);

// 🧩 Access control (force integer)
$accessLevel = (int)($event['access_level'] ?? 1);
$canAccess   = $plan >= $accessLevel;

$vimeo_id  = trim($event['vimeo_id'] ?? '');
$videoSrc  = '';

if (!empty($vimeo_id)) {
    if (is_numeric($vimeo_id)) {
        $videoSrc = "https://player.vimeo.com/video/" . $vimeo_id;
    } elseif (str_contains($vimeo_id, 'event')) {
        $videoSrc = "https://vimeo.com/{$vimeo_id}/embed";
    } else {
        $videoSrc = "https://vimeo.com/event/{$vimeo_id}/embed";
    }
}

// 🕓 Session Timing
$startTime = isset($event['start_time']) ? date('h:i A', strtotime($event['start_time'])) : null;
$endTime   = isset($event['end_time']) ? date('h:i A', strtotime($event['end_time'])) : null;
$eventDate = isset($event['event_date']) ? date('F j, Y', strtotime($event['event_date'])) : null;
?>

<style>
    body {
        background-image: url('<?php echo asset_url('images/mobile-brain-light.png'); ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed; /* 🔥 Keeps background fixed during scroll */
        font-family: 'Inter', 'Poppins', sans-serif;
        min-height: 100vh;
        color: #A70B91;
        overflow-x: hidden;
        overflow-y: auto; /* 🔥 Ensure only content scrolls */
    }

    .overlay-gradient {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: -1;
    }

    .session-container {
        padding: 1.5rem;
        text-align: center;
        max-width: 600px;
        margin: 10px auto auto;
    }

    h4 {
        color: #A70B91;
        margin-bottom: 0.4rem;
        font-weight: 700;
        font-size: 1.4rem;
    }

    .session-meta {
        font-size: 0.85rem;
        color: #5c065e;
        margin-bottom: 1rem;
    }

    .session-meta span {
        display: block;
        line-height: 1.4;
    }

    /* 🎥 Video Box */
    .video-wrapper {
        position: relative;
        width: 100%;
        padding-top: 56.25%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        margin-bottom: 1.5rem;
    }

    iframe {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        border: none;
        border-radius: 16px;
    }

    .locked-video {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(255,216,77,0.3);
        border-radius: 16px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #5c065e;
        font-size: 0.95rem;
        font-weight: 500;
        text-shadow: 0 2px 6px rgba(0,0,0,0.3);
        margin-bottom: 1.5rem;
    }

    .locked-video i {
        font-size: 1.2rem;
        margin-right: 8px;
        color: #A70B91;
    }

    .feedback-row {
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }

    .like-icon i {
        color: #A70B91;
        font-size: 1.2rem;
        cursor: pointer;
        transition: color 0.3s;
    }

    .like-icon i.filled {
        color: #E91E63;
    }

    .rating-stars {
        direction: rtl;
        display: flex;
        gap: 4px;
    }

    .rating-stars input { display: none; }
    .rating-stars label {
        font-size: 1.1rem;
        color: #777;
        cursor: pointer;
        transition: color 0.3s;
    }

    .rating-stars input:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #A70B91;
    }

    .speaker-section {
        margin-top: 1.5rem;
        text-align: left;
    }

    .speaker-card {
        display: flex;
        align-items: center;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 14px;
        padding: 10px;
        margin-bottom: 0.8rem;
    }

    .speaker-card img {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
        margin-right: 10px;
    }

    .speaker-info h6 {
        font-size: 0.95rem;
        font-weight: 600;
        color: #A70B91;
        margin-bottom: 2px;
    }

    .speaker-info p {
        font-size: 0.8rem;
        margin: 0;
        color: #5c065e;
    }

    .session-overview {
        background: rgba(255,255,255,0.08);
        border-radius: 14px;
        padding: 1rem;
        margin-top: 1.5rem;
        text-align: left;
    }

    .session-overview h6 {
        color: #A70B91;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .session-overview p {
        font-size: 0.9rem;
        color: #5c065e;
        line-height: 1.5;
    }

    .btn-download {
        display: inline-block;
        margin-top: 1rem;
        color: #fff;
        border: none;
        border-radius: 30px;
        padding: 10px 10px;
        font-weight: 600;
        text-decoration: none;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        transition: transform 0.2s ease;
        width: 170px;
    }

    .btn-download:hover {
        transform: scale(1.05);
    }

    @media (max-width: 480px) {
        h4 { font-size: 1.2rem; }
        .speaker-card img { width: 60px; height: 60px; }
    }

    /* ===========================================================
       BOTTOM SLIDING CHAT DRAWER
    =========================================================== */
    .chat-drawer {
        position: fixed;
        bottom: -100%;
        left: 0;
        width: 100%;
        height: 70%;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(15px);
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -4px 25px rgba(0,0,0,0.35);
        transition: bottom 0.35s ease;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        padding: 0;
    }

    .chat-drawer.active {
        bottom: 0;
    }

    .chat-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 999998;
        display: none;
    }

    .chat-overlay.active {
        display: block;
    }

    /* ===========================================================
       CHAT HEADER
    =========================================================== */
    .chat-header {
        padding: 14px 18px;
        background: rgba(0,0,0,0.25);
        backdrop-filter: blur(8px);
        color: #5c065e;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-close-btn {
        background: none;
        border: none;
        font-size: 26px;
        color: #5c065e;
        cursor: pointer;
        margin-top: -4px;
    }

    /* ===========================================================
       OPTION A – MINIMAL GLASS CHAT UI
    =========================================================== */
    /* ===========================================================
       PREMIUM CHAT BUBBLES (Mobile Native)
    =========================================================== */

    .chat-bubble-wrapper {
        display: flex;
        align-items: flex-end;
        margin-bottom: 14px;
    }

    .chat-bubble-wrapper.me {
        justify-content: flex-end;
    }

    .chat-bubble-wrapper.them {
        justify-content: flex-start;
    }

    .chat-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 8px;
        border: 2px solid rgba(255,255,255,0.4);
    }

    .chat-bubble-wrapper.me .chat-avatar {
        display: none;
    }

    .chat-bubble {
        position: relative;
        padding: 10px 14px;
        border-radius: 16px;
        max-width: 78%;
        line-height: 1.45;
    }

    .chat-bubble.me {
        background: linear-gradient(135deg, #A70B91, #d215bf);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .chat-bubble.them {
        background: rgba(255,255,255,0.90);
        color: #222;
        border-bottom-left-radius: 4px;
    }

    /* Name above bubble */
    .chat-name {
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 4px;
        opacity: 0.9;
        color: #ffe7fb;
    }

    /* Incoming show name in theme color */
    .chat-bubble-wrapper.them .chat-name {
        color: #A70B91;
    }

    /* Time styling */
    .chat-time {
        font-size: 0.70rem;
        margin-top: 6px;
        opacity: 0.7;
        text-align: right;
    }

    .chat-drawer {
        position: fixed;
        bottom: -100%;
        left: 0;
        width: 100%;
        height: 72%;
        background: rgba(25, 0, 20, 0.55);
        backdrop-filter: blur(22px) saturate(180%);
        border-radius: 22px 22px 0 0;
        box-shadow: 0 -4px 28px rgba(0,0,0,0.4);
        transition: bottom 0.32s cubic-bezier(0.24,0.61,0.35,1);
        z-index: 999999;
        display: flex;
        flex-direction: column;
    }

    .chat-header {
        padding: 16px 20px;
        background: rgba(255,255,255,0.10);
        backdrop-filter: blur(10px);
        color: #fff;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 22px 22px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        letter-spacing: .3px;
    }

    .chat-close-btn {
        background: rgba(255,255,255,0.18);
        border: none;
        font-size: 22px;
        padding: 4px 10px;
        border-radius: 12px;
        color: #fff;
        cursor: pointer;
    }

    .chat-input-row {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
        padding: 10px 12px;
        border-radius: 16px;
        display: flex;
        gap: 8px;
        margin-top: 12px;
        align-items: center;
    }

    .chat-input {
        flex: 1;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.25);
        padding: 12px;
        background: rgba(255,255,255,0.18);
        color: #fff;
        height: 48px;
        max-height: 130px;
    }

    .chat-send-btn {
        background: linear-gradient(135deg, #A70B91, #d215bf);
        border-radius: 14px;
        border: none;
        padding: 12px 16px;
        color: #fff;
        font-weight: 700;
        font-size: .9rem;
        display: flex;
        align-items: center;
    }

    /* FIX SCROLLING — FORCE CHAT AREA TO BE SCROLLABLE */
    .chat-glass {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* IMPORTANT */
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto !important;
        padding: 10px;
        min-height: 0; /* CRITICAL FIX */
    }

    .chat-messages::-webkit-scrollbar {
        width: 5px;
    }
    .chat-messages::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.1);
    }
    .chat-messages::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.35);
        border-radius: 20px;
    }

    .chat-bubble-wrapper.pending .chat-bubble { opacity: 0.7; filter: blur(0.1px); }
    .chat-bubble-wrapper.failed .chat-bubble { border: 1px solid rgba(255,0,0,0.2); opacity: 0.7; }

</style>

<div class="overlay-gradient"></div>

<div class="session-container">
    <!-- 🏷️ Title + Date/Time -->
    <h4><?php echo esc($event['sessions_name'] ?? 'Session'); ?></h4>

    <?php if ($eventDate || $startTime): ?>
        <div class="session-meta">
            <?php if ($eventDate): ?><span><?php echo esc($eventDate); ?></span><?php endif; ?>
            <?php if ($startTime && $endTime): ?><span><?php echo "{$startTime} - {$endTime} ({$timezone})"; ?></span><?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 🎥 Video -->
    <?php if ($canAccess && !empty($videoSrc)): ?>
        <div class="video-wrapper">
            <iframe src="<?php echo esc($videoSrc); ?>"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen></iframe>
        </div>
    <?php elseif (empty($videoSrc)): ?>
        <div class="locked-video">
            <i class="fa fa-info-circle"></i> This session does not have a video available yet.
        </div>
    <?php else: ?>
        <div class="locked-video">
            <i class="fa fa-lock"></i> Please upgrade your ticket to access this session.
        </div>
    <?php endif; ?>


    <!-- 👥 Speakers -->
    <?php if (!empty($sessionSpeakers)): ?>
        <div class="speaker-section">
            <h6 style="color:#A70B91; margin-bottom:8px;">Speakers</h6>
            <?php foreach ($sessionSpeakers as $speaker): ?>
                <div class="speaker-card">
                    <img src="<?php echo base_url('uploads/speakers/' . ($speaker['speaker_photo'] ?? '')); ?>"
                         onerror="this.src='<?php echo asset_url('images/user.png'); ?>';"
                         alt="<?php echo esc($speaker['speaker_name']); ?>">
                    <div class="speaker-info">
                        <h6><?php echo esc($speaker['speaker_name']); ?></h6>
                        <p><?php echo esc(($speaker['speaker_title'] ?? '') . ', ' . ($speaker['speaker_company'] ?? '')); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- 📝 Session Overview -->
    <div class="session-overview">
        <h6>Overview</h6>
        <p><?php echo nl2br(esc($event['description'] ?? 'No description available.')); ?></p>

        <?php if (!empty($event['workbook'])): ?>
            <a href="<?php echo base_url('uploads/workbooks/' . $event['workbook']); ?>" download class="btn-download epr-btn-four">
                Download Workbook
            </a>
        <?php endif; ?>
        <button class="btn-download epr-btn-four" onclick="openChatDrawer()">
            Live Chat
        </button>
    </div>
</div>

<!-- ===========================================================
     SLIDING CHAT DRAWER (BOTTOM SHEET)
=========================================================== -->
<div id="chatDrawer" class="chat-drawer">

    <div class="chat-header">
        <span>Live Chat</span>
        <button class="chat-close-btn" onclick="closeChatDrawer()">×</button>
    </div>

    <!-- OPTION A — Minimal Glass Chat -->
    <div id="chat-container" class="chat-glass">
        <div id="chat-messages" class="chat-messages"></div>

        <div class="chat-input-row">
            <textarea id="chatInput" class="chat-input" placeholder="Type a message..."></textarea>
            <button class="chat-send-btn" onclick="sendMessage()">Send</button>
        </div>
    </div>

</div>

<!-- Overlay -->
<div id="chatOverlay" class="chat-overlay" onclick="closeChatDrawer()"></div>

<script>
    function openChatDrawer() {
        document.getElementById("chatDrawer").classList.add("active");
        document.getElementById("chatOverlay").classList.add("active");
    }

    function closeChatDrawer() {
        document.getElementById("chatDrawer").classList.remove("active");
        document.getElementById("chatOverlay").classList.remove("active");
    }
</script>

<!-- Supabase, Emoji picker (module) and Tribute CSS/JS — include once on page -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script type="module" src="https://unpkg.com/emoji-picker-element@latest/index.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css">
<script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>

<script>
    /*
      Mobile Chat — standalone, uses your routes:
       - POST <?php echo base_url('api/chat/send/'); ?>{sessionId}
   - GET  <?php echo base_url('api/attendees/all'); ?>
   - GET  <?php echo base_url('api/users/'); ?>{id}

  Drop this script into mobile/session_detail.php (replace the previous mobile chat JS).
*/

    (async function MobileChatInit() {
        // ---------- Config (server-side values injected via PHP) ----------
        const SUPABASE_URL = "<?php echo getenv('supabase.url'); ?>";
        const SUPABASE_ANON = "<?php echo getenv('supabase.anon_key'); ?>";

        const sessionId = "<?php echo $event['sessions_id']; ?>";
        const attendeeId = "<?php echo session('attendee_id'); ?>";
        const API_KEY = "<?php echo env('api.securityKey'); ?>"; // used for your CI4 endpoints (if required)

        const SEND_ENDPOINT      = "<?php echo base_url('api/chat/send/'); ?>" + sessionId;        // POST
        const ATTENDEES_ENDPOINT = "<?php echo base_url('api/attendees/all'); ?>";               // GET
        const USERS_ENDPOINT     = "<?php echo base_url('api/users/'); ?>";                     // GET + id

        // ---------- DOM ----------
        const chatBox   = document.getElementById("chat-messages");
        const chatInput = document.getElementById("chatInput");
        if (!chatBox || !chatInput) {
            console.warn("MobileChat: chat DOM elements not found (chat-messages or chatInput). Aborting.");
            return;
        }

        // ---------- Supabase ----------
        const supabaseClient = supabase.createClient(SUPABASE_URL, SUPABASE_ANON);

        // ---------- State ----------
        const profileCache = {}; // id -> {firstname, lastname, profile_picture}
        const seenMessageIds = new Set(); // avoid duplicate rendering when realtime + history both send same msg

        // ---------- Helpers ----------
        function scrollChatDown() {
            try { chatBox.scrollTop = chatBox.scrollHeight; } catch(e){}
        }

        function escapeHtml(s) {
            if (s === undefined || s === null) return "";
            return String(s)
                .replace(/&/g,'&amp;')
                .replace(/</g,'&lt;')
                .replace(/>/g,'&gt;')
                .replace(/"/g,'&quot;')
                .replace(/'/g,'&#039;')
                .replace(/\n/g, '<br>');
        }

        // sanitize but allow <a> (mentions) — we use placeholder markers to safely re-insert allowed mention HTML
        function sanitizeAllowLinks(str) {
            if (!str) return "";
            // pull mention HTML markers like [[MENTION:...]] out before escaping
            const placeholders = [];
            const markerRegex = /\[\[MENTION:(.*?)\]\]/g;
            str = str.replace(markerRegex, (m, inner) => {
                placeholders.push(inner);
                return `[[MENTION_PLACEHOLDER_${placeholders.length-1}]]`;
            });

            let escaped = escapeHtml(str);

            // restore mention HTML (we trust our mention HTML generator, not external input)
            placeholders.forEach((html, i) => {
                escaped = escaped.replace(`[[MENTION_PLACEHOLDER_${i}]]`, html);
            });

            return escaped;
        }

        // Replace tokens @{user:ID} -> mention HTML (wrapped in marker so sanitizer will restore)
        function replaceTokensWithNames(message) {
            if (!message) return message;
            return message.replace(/@\{user:(\d+)\}/g, (m, id) => {
                const u = profileCache[id];
                if (!u) return "@Unknown";
                const fullname = `${u.firstname || ''} ${u.lastname || ''}`.trim();
                const profileUrl = "/attendees/profile/" + id;
                // wrap in marker so sanitizeAllowLinks can re-insert safely
                return `[[MENTION:<a href="${profileUrl}" class="mention-link" style="color:#ff4eb8;font-weight:600;text-decoration:none">@${fullname}</a>]]`;
            });
        }

        // Convert raw "@Full Name" occurrences into mention HTML directly (for when Tribute inserts display name)
        function replaceRawMentions(message) {
            if (!message) return message;
            for (const id in profileCache) {
                const u = profileCache[id];
                if (!u) continue;
                const fullname = `${u.firstname} ${u.lastname}`.trim();
                if (!fullname) continue;
                const escaped = fullname.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
                const regex = new RegExp("@" + escaped, "g"); // case-sensitive to match what user selected; can use 'i' if you want
                const profileUrl = "/attendees/profile/" + id;
                message = message.replace(regex,
                    `[[MENTION:<a href="${profileUrl}" class="mention-link" style="color:#ff4eb8;font-weight:600;text-decoration:none">@${fullname}</a>]]`
                );
            }
            return message;
        }

        // Ensure profile is loaded (populates profileCache)
        async function getUserProfile(userId) {
            if (profileCache[userId]) return profileCache[userId];
            try {
                const res = await fetch(USERS_ENDPOINT + userId, {
                    headers: { "X-API-KEY": API_KEY },
                    credentials: "include"
                });
                const j = await res.json();
                if (j && j.status === "success" && j.data) {
                    profileCache[userId] = j.data;
                    return j.data;
                }
            } catch (err) {
                console.warn("getUserProfile error", err);
            }
            // fallback
            profileCache[userId] = { firstname: "User", lastname: userId, profile_picture: "" };
            return profileCache[userId];
        }

        // Render a message; will replace tokens and raw mentions, sanitize and add to DOM
        async function renderMessage(msg) {
            if (!msg || !('id' in msg)) {
                // some messages may come without id (edge); allow rendering but no dedupe
            } else {
                if (seenMessageIds.has(String(msg.id))) return; // already rendered
                seenMessageIds.add(String(msg.id));
            }

            const user = await getUserProfile(msg.attendee_id);
            const fullname = `${user.firstname || ''} ${user.lastname || ''}`.trim();
            const avatar = user.profile_picture ? "<?php echo base_url('uploads/profile_pictures'); ?>/" + user.profile_picture : "<?php echo asset_url('images/user.png'); ?>";
            const isMe = String(msg.attendee_id) === String(attendeeId);

            // 1. replace raw @Name -> mention HTML markers
            let content = replaceRawMentions(msg.message || "");

            // 2. replace token @{user:ID} -> mention HTML markers
            content = replaceTokensWithNames(content);

            // 3. sanitize but preserve mention HTML
            content = sanitizeAllowLinks(content);

            const bubble = `
      <div class="chat-bubble-wrapper ${isMe ? 'me' : 'them'}" data-msgid="${msg.id ?? ''}">
        ${!isMe ? `<img src="${avatar}" class="chat-avatar" onerror="this.src='<?php echo asset_url('images/user.png'); ?>'">` : ''}
        <div class="chat-bubble ${isMe ? 'me' : 'them'}">
          <div class="chat-name">${escapeHtml(fullname)}</div>
          <div class="chat-text">${content}</div>
          <div class="chat-time">${new Date(msg.created_at || Date.now()).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
        </div>
      </div>
    `;
            chatBox.insertAdjacentHTML('beforeend', bubble);
            scrollChatDown();
        }

        // ---------- Load history (Supabase REST)
        async function loadChatHistory() {
            try {
                const url = `${SUPABASE_URL}/rest/v1/tbl_session_chats?session_id=eq.${sessionId}&order=id.asc`;
                const res = await fetch(url, {
                    headers: {
                        "apikey": SUPABASE_ANON,
                        "Authorization": `Bearer ${SUPABASE_ANON}`
                    }
                });
                const msgs = await res.json();
                if (!Array.isArray(msgs)) return;
                for (const m of msgs) await renderMessage(m);
            } catch (err) {
                console.warn("loadChatHistory error", err);
            }
        }

        // ---------- Mentions (load attendee list) ----------
        async function initMentions() {
            try {
                const res = await fetch(ATTENDEES_ENDPOINT, {
                    headers: { "X-API-KEY": API_KEY },
                    credentials: "include"
                });
                const json = await res.json();
                if (!json || !Array.isArray(json.data) && !Array.isArray(json)) {
                    // older controllers sometimes return array directly
                    const dataArray = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : []);
                    if (!dataArray.length) return;
                    populateMentions(dataArray);
                    return;
                }
                const list = Array.isArray(json.data) ? json.data : json;
                populateMentions(list);
            } catch (err) {
                console.warn("initMentions failed", err);
            }

            function populateMentions(list) {
                // Fill cache + tribute values
                const values = list.map(a => {
                    const id = String(a.id);
                    const fullname = `${a.firstname || ''} ${a.lastname || ''}`.trim();
                    profileCache[id] = {
                        firstname: a.firstname || '',
                        lastname: a.lastname || '',
                        profile_picture: a.profile_picture || ''
                    };
                    return { key: fullname, id: id };
                });

                // Tribute — insert display name when selected
                const tribute = new Tribute({
                    trigger: "@",
                    lookup: "key",
                    values: values,
                    selectTemplate: item => "@" + item.original.key,
                    menuItemTemplate: item => item.string
                });
                tribute.attach(chatInput);

                console.log("Mentions initialized (mobile):", values.length, "users");
            }
        }

        // ---------- Send message ----------
        async function sendMessage() {
            const raw = chatInput.value.trim();
            if (!raw) return;

            // convert displayed @Full Name -> @{user:ID} using profileCache
            let final = raw;
            for (const id in profileCache) {
                const u = profileCache[id];
                if (!u) continue;
                const fullname = `${u.firstname} ${u.lastname}`.trim();
                if (!fullname) continue;
                const escaped = fullname.replace(/[-\/\\^$*+?.()|[\]{}]/g, "\\$&");
                const regex = new RegExp("@" + escaped, "g"); // match the display name
                final = final.replace(regex, `@{user:${id}}`);
            }

            try {
                const res = await fetch(SEND_ENDPOINT, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-API-KEY": API_KEY
                    },
                    credentials: "include",
                    body: JSON.stringify({
                        attendee_id: attendeeId,
                        message: final
                    })
                });

                // log result for easier debugging (you can remove this later)
                try {
                    const j = await res.clone().json();
                    console.log("send() response:", res.status, j);
                } catch (e) {
                    console.log("send() raw status:", res.status);
                }
                // NOTE: we don't optimistic-render — we rely on realtime event to deliver the final message
                chatInput.value = "";
            } catch (err) {
                console.warn("sendMessage error", err);
            }
        }
        window.sendMessage = sendMessage; // keep compatibility with button onclick

        // send on Enter (mobile still allows)
        chatInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // ---------- Emoji picker (emoji-picker-element) ----------
        function initEmoji() {
            try {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm';
                btn.style.marginLeft = '8px';
                btn.innerText = "😊";

                // place button next to textarea (chatInput.parentElement expected)
                chatInput.parentElement.appendChild(btn);

                const picker = document.createElement('emoji-picker');
                picker.style.position = 'absolute';
                picker.style.zIndex = '99999';
                picker.style.display = 'none';
                chatInput.parentElement.appendChild(picker);

                btn.addEventListener('click', () => {
                    picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
                });

                picker.addEventListener('emoji-click', (ev) => {
                    const emoji = ev.detail.unicode;
                    const start = chatInput.selectionStart;
                    const end = chatInput.selectionEnd;
                    chatInput.value = chatInput.value.slice(0, start) + emoji + chatInput.value.slice(end);
                    chatInput.selectionStart = chatInput.selectionEnd = start + emoji.length;
                    picker.style.display = 'none';
                    chatInput.focus();
                });
            } catch (err) {
                console.warn("initEmoji failed", err);
            }
        }

        // ---------- Realtime subscription ----------
        function initRealtime() {
            try {
                const channel = supabaseClient
                    .channel("session-" + sessionId)
                    .on("postgres_changes", {
                        event: "INSERT",
                        schema: "public",
                        table: "tbl_session_chats",
                        filter: "session_id=eq." + sessionId
                    }, payload => {
                        // payload.new is the new row
                        renderMessage(payload.new);
                    })
                    .subscribe();

                // no .then chain here; subscribe returns a Subscription object (don't call .then)
            } catch (err) {
                console.warn("Realtime init error", err);
            }
        }

        // ---------- Init sequence (mentions first to populate cache) ----------
        await initMentions();     // preload profileCache so replacements can work
        await loadChatHistory();  // load existing messages
        initEmoji();              // emoji button and picker
        initRealtime();           // realtime subscription
        scrollChatDown();

        console.log("Mobile chat initialized (session:", sessionId, ")");
    })();
</script>


<?php echo module_view('MobileApp', 'includes/footer'); ?>
