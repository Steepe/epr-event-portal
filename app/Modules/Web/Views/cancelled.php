<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 22/11/2025
 * Time: 14:24
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
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
    <div class="payment-title">Payment Cancelled</div>

    <div class="payment-text">Your transaction was not completed.</div>
    <div class="payment-text">No charges were applied to your account.</div>

    <a href="<?php echo base_url('attendees/upgrade'); ?>" class="btn-epr-purple">
        Try Again
    </a>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
