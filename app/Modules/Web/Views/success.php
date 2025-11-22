<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 22/11/2025
 * Time: 14:21
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$request = \Config\Services::request();
$conferenceId = $request->getGet('conference_id');
?>

<style>
    .payment-wrapper {
        max-width: 850px;
        margin: 40px auto;
        padding: 28px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        text-align: center;
    }
    .payment-title {
        font-size: 26px;
        font-weight: 800;
        color: #9D0F82;
        margin-bottom: 10px;
    }
    .payment-text {
        font-size: 15px;
        color: #444;
        margin-bottom: 6px;
    }
    .ref-box {
        margin: 18px auto;
        padding: 10px 16px;
        background: #faf3f9;
        border: 1px solid #f0d9e9;
        border-radius: 10px;
        font-weight: 600;
        color: #9D0F82;
        display: inline-block;
    }
    .btn-epr-purple {
        background:#9D0F82;
        color:#fff;
        padding:12px 20px;
        border-radius:8px;
        border:none;
        font-weight:700;
        cursor:pointer;
        margin-top:18px;
        display:inline-block;
        text-decoration:none;
    }
</style>

<div class="payment-wrapper">
    <div class="payment-title">Payment Successful 🎉</div>

    <div class="payment-text">Your Premium Access has been activated.</div>
    <div class="payment-text">You now have access to all paid sessions.</div>

    <?php if (!empty($tx_ref)): ?>
        <div class="ref-box">
            Reference: <?php echo esc($tx_ref); ?>
        </div>
    <?php endif; ?>


    <a href="<?php echo base_url('attendees/agenda/' . $conferenceId); ?>" class="btn-epr-purple">
        Continue to Agenda
    </a>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
