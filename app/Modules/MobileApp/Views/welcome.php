<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:13
 */

echo module_view('MobileApp', 'includes/header');

$attendee_id = session('user_id') ?? null;
$country = session('reg_country') ?? 'Nigeria';
?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        color: #fff;
        overflow: hidden;
        background-image: url('<?php echo asset_url('images/mobile-bg.png'); ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    /* ⚠️ Payment Notice */
    #paymentNotice {
        position: absolute;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        width: 90%;
        background: rgba(255, 243, 205, 0.9);
        color: #856404;
        border: 1px solid #ffeeba;
        border-radius: 10px;
        padding: 10px 15px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        display: none;
        text-align: center;
    }

    #paymentNotice button {
        border: none;
        background: #9D0F82;
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        margin-top: 5px;
    }

    #closeNotice {
        position: absolute;
        top: 5px;
        right: 12px;
        background: none;
        border: none;
        color: #856404;
        font-size: 20px;
        cursor: pointer;
        line-height: 1;
    }

    /* 🎥 Video Container */
    .video-wrapper {
        position: relative;
        width: 90%;
        max-width: 420px;
        aspect-ratio: 16 / 9;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        margin: 0 auto;
    }

    .video-wrapper iframe {
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 14px;
    }

    /* 🚪 CTA Button */
    .enter-lobby {
        color: #fff;
        border: none;
        border-radius: 30px;
        padding: 14px 0;
        font-weight: 600;
        width: 80%;
        max-width: 350px;
        margin-top: 2rem;
        transition: 0.3s;
        text-decoration: none;
        text-align: center;
        font-size: 16px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.3);
    }

    .enter-lobby:hover {
        transform: scale(1.05);
    }

    @media (max-width: 480px) {
        .video-wrapper {
            max-width: 350px;
        }

        .enter-lobby {
            font-size: 15px;
            padding: 12px 0;
        }
    }
</style>

<!-- ⚠️ Payment Notice -->
<!--<div id="paymentNotice">
    <button type="button" id="closeNotice">&times;</button>
    <div class="notice-text">
        <strong>Access Restricted:</strong>
        This is a paid event. Please complete your registration payment to unlock all sessions.
        <div class="mt-2">
            <span id="priceInfo" style="font-weight:600;"></span>
            <button id="upgradeBtn">Pay Now</button>
        </div>
    </div>
</div>-->

<!-- 🎥 Centered Video -->
<div class="video-wrapper">
    <iframe src="https://player.vimeo.com/video/1014896127"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen></iframe>
</div>

<!-- 🚪 Enter Lobby Button -->
<a href="<?php echo site_url('mobile/lobby'); ?>" class="enter-lobby epr-btn-one">
    ENTER LOBBY
</a>


<script>
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

        const isPaidEvent  = conf.is_paid;
        const conferenceId = conf.conference_id;

        const priceData = await apiGet(`ticket-prices/${conferenceId}/${country}`);
        const payData = await apiGet(`payments/check/${attendeeId}`);
        const hasPaid = payData?.data && Object.keys(payData.data).length > 0;

        if (isPaidEvent && !hasPaid) {
            const notice = document.getElementById("paymentNotice");
            const priceInfo = document.getElementById("priceInfo");

            if (priceData.status === "success") {
                priceInfo.textContent = `Ticket: ${priceData.currency} ${priceData.price}`;
            } else {
                priceInfo.textContent = "Ticket pricing unavailable.";
            }

            notice.style.display = "block";
        }
    } catch (err) {
        console.error("Error loading conference/payment info:", err);
    }

    const closeNotice = document.getElementById("closeNotice");
    const paymentNotice = document.getElementById("paymentNotice");
    if (closeNotice && paymentNotice) {
        closeNotice.addEventListener("click", () => {
            paymentNotice.style.opacity = "0";
            paymentNotice.style.transition = "opacity 0.4s ease";
            setTimeout(() => paymentNotice.style.display = "none", 400);
        });
    }
});
</script>
