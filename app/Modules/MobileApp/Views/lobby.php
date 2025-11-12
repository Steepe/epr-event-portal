<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:32
 */

echo module_view('MobileApp', 'includes/header');

$attendee_id = session('user_id') ?? null;
$country = session('reg_country') ?? 'Nigeria';
$conference_id = session('live-conference-id') ?? null;
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        color: #fff;
        overflow: hidden;
        position: relative;
        background-color: #000;
    }

    /* 🎥 Background Video */
    #bgVideo {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: 50% 45%;
        z-index: -1;
    }

    #videoOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(40, 0, 60, 0.3);
        z-index: 0;
    }

    /* ⚠️ Payment Notice */
    #paymentNotice {
        display: none;
        position: fixed;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        background: rgba(255, 243, 205, 0.9);
        color: #856404;
        border: 1px solid #ffeeba;
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        font-size: 13px;
        z-index: 10;
    }

    #priceInfo {
        font-weight: 600;
        color: #7a5200;
    }

    #closeNotice {
        background: none;
        border: none;
        color: #856404;
        font-size: 18px;
        cursor: pointer;
        font-weight: bold;
        margin-left: 10px;
    }

    /* 🧠 Bubble Grid (mobile-friendly) */
    .bubble-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
        padding: 80px 20px 120px;
        justify-items: center;
        z-index: 2;
        position: relative;
    }

    .bubble {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: 600;
        color: #fff;
        font-size: 14px;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: float1 6s ease-in-out infinite;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .bubble:hover {
        transform: scale(1.05);
        box-shadow: 0 0 25px rgba(255, 255, 255, 0.5);
    }

    .bubble a {
        color: #fff;
        text-decoration: none;
        display: block;
        line-height: 1.3;
        font-size: 13px;
    }

    /* 🎨 Bubble Colors */
    .agenda     { background: radial-gradient(circle at top left, #f1a91e, #d68500); }
    .networking { background: radial-gradient(circle at top left, #8b4ac0, #693b9c); }
    .exhibitors { background: radial-gradient(circle at top left, #d61be3, #9b0eaa); }
    .sponsors   { background: radial-gradient(circle at top left, #a82ad2, #7200a4); }
    .envision   { background: radial-gradient(circle at top left, #a04fc0, #6a239a); }
    .emergence  { background: radial-gradient(circle at top left, #e988a2, #c95c7c); }

    /* 🌊 Floating Animations */
    @keyframes float1 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

    /* ⚙️ Footer */
    footer {
        position: fixed;
        bottom: 10px;
        left: 0;
        width: 100%;
        text-align: center;
        font-size: 12px;
        color: #ddd;
        z-index: 2;
    }

    footer a {
        color: #ffb400;
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 480px) {
        .bubble {
            width: 110px;
            height: 110px;
            font-size: 12px;
        }

        .bubble-grid {
            gap: 20px;
            padding-top: 70px;
        }
    }
</style>

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/brain-lobby-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<!-- ⚠️ Payment Notice -->
<div id="paymentNotice">
    <strong>Access Restricted:</strong>
    This is a paid event. Complete your registration payment to unlock all sessions.
    <span id="priceInfo"></span>
    <button id="closeNotice">&times;</button>
</div>

<!-- 🧠 Lobby Bubbles (Grid Layout for Mobile) -->
<div class="bubble-grid">

    <div class="bubble agenda">
        <a href="<?php echo base_url('mobile/agenda/' . $conference_id); ?>">AGENDA</a>
    </div>

    <div class="bubble networking">
        <a href="<?php echo base_url('mobile/networking_center'); ?>">NETWORKING<br>CENTER</a>
    </div>

    <div class="bubble exhibitors">
        <a href="<?php echo base_url('mobile/exhibitors'); ?>">EXHIBITORS</a>
    </div>

    <div class="bubble sponsors">
        <a href="<?php echo base_url('mobile/sponsors'); ?>">SPONSORS</a>
    </div>

    <div class="bubble envision">
        <a href="<?php echo base_url('mobile/envision'); ?>">ENVISION</a>
    </div>

    <div class="bubble emergence">
        <a href="<?php echo base_url('mobile/emergence_booth'); ?>">EMERGENCE<br>BOOTH</a>
    </div>
</div>

<!-- ⚙️ Footer -->
<footer>
    Crafted with ❤️ by <a href="https://www.creyatif.com/website" target="_blank">Crèyatif</a> •
    &copy; <?php echo date("Y"); ?> Powered By EPR Global.
</footer>

<?php echo module_view('MobileApp', 'includes/footer'); ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const video = document.getElementById("bgVideo");
        if (video) video.playbackRate = 0.5;
    });

    document.querySelectorAll('.bubble').forEach(b => {
        b.style.animationDelay = `${Math.random() * 2}s`;
    });

    // Paid Event Logic
    document.addEventListener("DOMContentLoaded", async () => {
        const attendeeId = "<?php echo $attendee_id; ?>";
        const country = "<?php echo $country; ?>";
        const apiBase = "<?php echo rtrim(base_url('api'), '/'); ?>";
        const apiKey  = "<?php echo env('api.securityKey'); ?>";

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

            const isPaidEvent  = conf.is_paid === 1;
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
