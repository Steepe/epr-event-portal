<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 06:32
 */

echo module_view('Web', 'includes/header_home');
echo module_view('Web', 'includes/home_topbar');

$attendee_id = session('attendee_id') ?? null;
$country = session('reg_country') ?? 'Nigeria';
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        font-size: 12px !important;
    }

    #paymentNotice {
        position: fixed;
        top: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2000;
        width: 75%;
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        display: none;
        height: 60px;
    }

    #paymentNotice a {
        color: #9D0F82;
        font-weight: bold;
    }

    #paymentNotice button {
        float: right;
        border: none;
        font-size: 15px;
        color: #ffffff;
        cursor: pointer;
        width: 100px;
        margin-left: 20px;
    }
</style>




<div class="container mt-5" style="margin-top: 90px !important;">
    <div class="col-md-10 mx-auto">
        <div style="position:relative;padding-top:56.25%;"><iframe src="https://iframe.mediadelivery.net/embed/405384/60589a7b-3fe7-40fb-a2ae-3c024f23e01e?autoplay=true&loop=false&muted=false&preload=true&responsive=true" loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true"></iframe></div>
    </div>
</div>

<div class="row w-100 mt-4 text-center">
    <a href="<?php echo base_url('attendees/lobby'); ?>"
       class="btn epr-btn-one"
       style="margin: auto; font-size: 17px;">ENTER LOBBY</a>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
        const attendeeId = "<?php echo $attendee_id; ?>";
        const country = "<?php echo $country; ?>";
        const apiBase = "<?php echo rtrim(base_url('api'), '/'); ?>";
        const apiKey  = "<?php echo env('api.securityKey'); ?>";

        // ✅ Helper to call any GET API securely
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
            // ✅ 1. Get live conference
            const confData = await apiGet("conferences/live");
            const conf = confData?.data;
            if (!conf) return; // No live conference


            const isPaidEvent  = conf.is_paid;
            const conferenceId = conf.conference_id;
            console.log(conferenceId);

            // ✅ 2. Get ticket price by user’s country
            const priceData = await apiGet(`ticket-prices/${conferenceId}/${country}`);

            // ✅ 3. Get payment record for attendee
            const payData = await apiGet(`payments/check/${attendeeId}`);
            const hasPaid = payData?.data && Object.keys(payData.data).length > 0;

            // ✅ 4. Show notice if not paid
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
    });

    // Dismissible notice
    document.addEventListener("DOMContentLoaded", () => {
        const closeNotice = document.getElementById("closeNotice");
        const paymentNotice = document.getElementById("paymentNotice");

        if (closeNotice && paymentNotice) {
            closeNotice.addEventListener("click", () => {
                paymentNotice.style.opacity = "0";
                paymentNotice.style.transition = "opacity 0.4s ease";
                setTimeout(() => {
                    if (paymentNotice && paymentNotice.style.display !== "none") {
                        paymentNotice.style.opacity = "0";
                        setTimeout(() => paymentNotice.style.display = "none", 400);
                    }
                }, 8000);
            });
        }
    });

</script>

</body>
</html>
