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
                <li class="nav-item"><button class="nav-link" data-toggle="pill" data-target="#qa-profile">Ask Questions</button></li>
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
                <div class="tab-pane fade" id="qa-profile">
                    <iframe src="<?php echo  base_url('messages/chat/qa/' . ($event['sessions_id'] ?? 0)) ?>"
                            style="background:transparent;height:550px;width:100%;border:none;"></iframe>
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
<script>
    const supabaseClient = supabase.createClient(
        "<?php echo getenv('supabase.url'); ?>",
        "<?php echo getenv('supabase.anon_key'); ?>"
    );

    const sessionId   = "<?php echo $event['sessions_id']; ?>";
    const attendeeId  = "<?php echo session('attendee_id'); ?>";
    const apiEndpoint = "/api/chat/send/" + sessionId;

    // Auto-scroll handler
    function scrollChatDown() {
        const chat = document.getElementById("chat-messages");
        chat.scrollTop = chat.scrollHeight;
    }

    // Render incoming messages
    function appendMessageToUI(data) {
        const box = document.getElementById("chat-messages");

        const bubble = `
            <div style="padding:8px; margin-bottom:10px;
                        background:${data.attendee_id == attendeeId ? '#D8198E' : '#333'};
                        color:white; border-radius:10px;">
                <strong>User ${data.attendee_id}</strong><br>
                ${data.message}
                <div style="font-size:10px; margin-top:4px; color:#ccc;">
                    ${data.timestamp}
                </div>
            </div>
        `;

        box.insertAdjacentHTML("beforeend", bubble);
        scrollChatDown();
    }

    // Subscribe to realtime chat
    supabaseClient
        .channel("session-" + sessionId)
        .on(
            "postgres_changes",
            {
                event: "INSERT",
                schema: "public",
                table: "tbl_session_chats",
                filter: "session_id=eq." + sessionId
            },
            (payload) => {
                appendMessageToUI({
                    attendee_id: payload.new.attendee_id,
                    message: payload.new.message,
                    timestamp: payload.new.created_at
                });
            }
        )
        .subscribe();

    // Send message (CI4 → Supabase → Realtime)
    async function sendMessage() {
        let msg = document.getElementById("chatInput").value.trim();
        if (msg.length === 0) return;

        await fetch(apiEndpoint, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                attendee_id: attendeeId,
                message: msg
            })
        });

        document.getElementById("chatInput").value = "";
    }
</script>


</body>
</html>
