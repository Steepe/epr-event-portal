<?php
/**
 * Lobby Page (Brain Hub Design)
 * Author: Oluwamayowa Steepe
 * Project: epr-event-portal
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/lobby_topbar');

$attendee_id = session('attendee_id') ?? null;
$country = session('reg_country') ?? 'Nigeria';
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-size: cover;
        overflow: hidden;
    }

    /* 🎥 Fullscreen Video Background */
    #bgVideo {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: 50% 45%; /* 👈 adjust this */
        z-index: -1;
    }

    #videoOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(40, 0, 60, 0.3); /* purple tint overlay */
        z-index: 0; /* sits above the video but below content */
    }

    /* --- Payment Notice --- */
    #paymentNotice {
        display: none;
        margin: 20px auto;
        width: 90%;
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        font-size: 14px;
        position: relative;
        z-index: 1000;
    }

    #paymentNotice .content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    #priceInfo {
        font-weight: 600;
        color: #7a5200;
        margin-left: 10px;
    }

    #closeNotice {
        background: none;
        border: none;
        color: #856404;
        font-size: 20px;
        cursor: pointer;
        font-weight: bold;
        margin-left: 10px;
    }

    #closeNotice:hover {
        color: #5c4300;
    }

    /* --- Floating Brain Bubbles --- */
    .bubble {
        position: absolute;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
    }

    .bubble:hover {
        transform: scale(1.08);
        box-shadow: 0 0 25px rgba(255, 255, 255, 0.6);
    }

    .past {
        background: radial-gradient(circle at top left, #f3722c, #d8198e);
        bottom: 38%;
        right: 10%;
    }


    .agenda     { background: radial-gradient(circle at top left, #f1a91e, #d68500); top: 15%; left: 25%; }
    .networking { background: radial-gradient(circle at top left, #8b4ac0, #693b9c); top: 40%; left: 45%; }
    .envision   { background: radial-gradient(circle at top left, #a04fc0, #6a239a); top: 18%; right: 24%; }
    .exhibitors { background: radial-gradient(circle at top left, #d61be3, #9b0eaa); bottom: 22%; left: 25%; }
    .sponsors   { background: radial-gradient(circle at top left, #a82ad2, #7200a4); bottom: 10%; left: 45%; }
    .emergence  { background: radial-gradient(circle at top left, #e988a2, #c95c7c); bottom: 20%; right: 25%; }

    .bubble a {
        color: #fff;
        text-decoration: none;
        font-weight: 600;
    }

    footer {
        position: fixed;
        bottom: 10px;
        left: 0;
        width: 100%;
        text-align: center;
        color: #ddd;
        font-size: 13px;
    }

    footer a {
        color: #d8198e;
        text-decoration: none;
    }

    /* Floating animation */
    @keyframes float1 { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-12px); } }
    @keyframes float2 { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(10px); } }
    @keyframes float3 { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-8px); } }
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo  asset_url('videos/brain-lobby-bg.mp4'); ?>" type="video/mp4">
    Your browser does not support the video tag.
</video>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const video = document.getElementById("bgVideo");
        if (video) {
            video.playbackRate = 0.5; // 👈 0.5 = half speed, 1 = normal speed
        }
    });
</script>

<div id="videoOverlay"></div>



<!-- 🔔 Paid Event Notification -->
<div id="paymentNotice" class="alert alert-warning text-center">
    <div class="content">
        <div>
            <strong>Access Restricted:</strong>
            This is a paid event. Please complete your registration payment to unlock all sessions.
            <span id="priceInfo"></span>
        </div>
        <button id="closeNotice">&times;</button>
    </div>
</div>

<!-- 🧠 Brain Hub Interactive Bubbles -->
<div class="bubble agenda">
    <a href="<?php echo  base_url('attendees/agenda'); ?>">AGENDA</a>
</div>

<div class="bubble networking">
    <a href="<?php echo  base_url('attendees/networking_center'); ?>">NETWORKING<br>CENTER</a>
</div>

<div class="bubble exhibitors">
    <a href="<?php echo  base_url('attendees/exhibitors'); ?>">EXHIBITORS</a>
</div>

<div class="bubble sponsors">
    <a href="<?php echo  base_url('attendees/sponsors'); ?>">SPONSORS</a>
</div>

<div class="bubble emergence">
    <a href="<?php echo  base_url('attendees/emergence_booth'); ?>">EMERGENCE<br>BOOTH</a>
</div>

<!-- 🔔 Announcement Sidebar -->
<div class="notification-slide" id="notification_slide">
    <div class="slider-arrow text-white">
        <i id="ringing" class="fa fa-bell faa-ring animated fa-5x ringing-bell" style="display: none;"></i>
        <img class="slide-out" src="<?php echo asset_url('images/slider-arrow-reverse.png') ?>">
        <img class="slide-in" src="<?php echo asset_url('images/slider-arrow.png') ?>" style="display: none;">
    </div>

    <div class="text-center" style="position: relative; top: -34px;">
        <h5 style="color: #EFB11E;">ANNOUNCEMENTS</h5>
    </div>

    <div id="announcements_div" class="announcements mb-5" style="height: 450px; overflow-y: scroll;">
        <?php if (isset($announcements)) : ?>
            <?php foreach ($announcements as $announcement) : ?>
                <div class="card announcement-card">
                    <div class="card-header">
                        <small class="float-right">
                            <?php echo date('M d H:i', $announcement['created_at']); ?>
                        </small>
                    </div>
                    <div class="card-body">
                        <p class="card-text font-12" style="margin-top: -30px;">
                            <?php echo $announcement['announcement']; ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ⚙️ Footer -->
<footer>
    <div>
        Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by
        <a href="https://www.creyatif.com/website" target="_blank">Crèyatif</a> •
        &copy; <?php echo  date("Y"); ?> Powered By EPR Global.
    </div>
</footer>

<?php
echo module_view('Web', 'includes/scripts');
?>

<script>
    // Floating animation assignment
    document.querySelectorAll('.bubble').forEach(b => {
        b.style.animation = `float${Math.floor(Math.random() * 3) + 1} 6s ease-in-out infinite`;
    });

    $(document).ready(function () {
        var objDiv = document.getElementById("notification_slide");
        if (objDiv) objDiv.scrollTop = objDiv.scrollHeight;
    });

    // Paid event logic
    document.addEventListener("DOMContentLoaded", async () => {
        const attendeeId = "<?php echo  $attendee_id; ?>";
        const country = "<?php echo  $country; ?>";
        const apiBase = "<?php echo  rtrim(base_url('api'), '/'); ?>";
        const apiKey = "<?php echo  env('api.securityKey'); ?>";

        async function apiGet(endpoint) {
            const res = await fetch(`${apiBase}/${endpoint}`, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "X-API-KEY": apiKey
                }
            });
            if (!res.ok) throw new Error(`API ${endpoint} failed (${res.status})`);
            return res.json();
        }

        try {
            const confData = await apiGet("conferences/live");
            const conf = confData?.data;
            if (!conf) return;

            const isPaidEvent = conf.is_paid === 1;
            const conferenceId = conf.conference_id;

            const priceData = await apiGet(`ticket-prices/${conferenceId}/${country}`);
            const payData = await apiGet(`payments/check/${attendeeId}`);
            const hasPaid = payData?.data && Object.keys(payData.data).length > 0;

            if (isPaidEvent && !hasPaid) {
                const notice = document.getElementById("paymentNotice");
                const priceInfo = document.getElementById("priceInfo");
                priceInfo.textContent =
                    priceData.status === "success"
                        ? `Ticket: ${priceData.currency} ${priceData.price}`
                        : "Ticket pricing unavailable.";
                notice.style.display = "block";
            }

            document.getElementById("closeNotice")?.addEventListener("click", () => {
                document.getElementById("paymentNotice").style.display = "none";
            });
        } catch (err) {
            console.error("Error loading conference/payment info:", err);
        }
    });
</script>

<!-- 💬 Tawk.to Live Chat -->
<script type="text/javascript">
    var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
    (function () {
        var s1 = document.createElement("script"),
            s0 = document.getElementsByTagName("script")[0];
        s1.async = true;
        s1.src = 'https://embed.tawk.to/6378b6c0daff0e1306d84ae3/1gi7ojojo';
        s1.charset = 'UTF-8';
        s1.setAttribute('crossorigin', '*');
        s0.parentNode.insertBefore(s1, s0);
    })();
</script>

</body>
</html>
