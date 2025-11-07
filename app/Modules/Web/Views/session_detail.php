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

<script src="https://2a63bc5e27bc4bb68eb137c6c91e2f40.weavy.io/uikit-web/weavy.js"></script>
<script>
    const weavy = new Weavy();
    weavy.url = "https://2a63bc5e27bc4bb68eb137c6c91e2f40.weavy.io";
    weavy.tokenFactory = async (refresh) => "<?php echo  session('access_token') ?>";
</script>

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
                <?php if ($canAccess && !empty($videoSrc)): ?>
                    <div style="padding:56.25% 0 0 0;position:relative;">
                        <iframe src="<?php echo  esc($videoSrc) ?>"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen
                                style="position:absolute;top:0;left:0;width:100%;height:100%;">
                        </iframe>
                    </div>
                <?php else: ?>
                    <div style="padding:56.25% 0 0 0;position:relative;background:#f5f5f5;">
                        <div class="lock-overlay d-flex justify-content-center align-items-center">
                            <i class="fa fa-lock"></i> &nbsp; Please upgrade your ticket to access this session.
                        </div>
                    </div>
                <?php endif; ?>
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
                                    <img src="<?php echo  base_url('uploads/speaker_pictures/' . $speaker['profile_picture']) ?>"
                                         alt="<?php echo  esc($speaker['firstname']) ?>"
                                         onerror="this.src='<?php echo  asset_url('images/user.png') ?>';"
                                         style="width:120px;height:120px;">
                                </div>
                                <div class="text-center pt-2 pl-2">
                                    <h6><strong><?php echo  esc($speaker['firstname'] . ' ' . $speaker['lastname']) ?></strong></h6>
                                    <p style="font-size:12px;"><?php echo  esc($speaker['position'] . ', ' . $speaker['company']) ?></p>
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
                        <wy-chat uid="<?php echo  esc($event['chat_uid'] ?? 'none') ?>"></wy-chat>
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
</body>
</html>
