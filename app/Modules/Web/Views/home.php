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
$portalLocked = (bool) ($portal_locked ?? false);
$checkoutEnabled = (bool) ($checkout_enabled ?? false);
$ticketPrice = $ticket_price ?? null;
$ticketCurrency = $ticket_currency ?? 'USD';
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
        display: <?php echo $portalLocked ? 'block' : 'none'; ?>;
        min-height: 60px;
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

<div id="paymentNotice" class="alert alert-warning text-center">
    <strong>Access Restricted:</strong>
    Your registration is saved, but portal access is locked until payment is confirmed.
    <?php if ($ticketPrice !== null): ?>
        <span id="priceInfo">Ticket: <?php echo esc($ticketCurrency); ?> <?php echo esc(number_format((float) $ticketPrice, 2)); ?></span>
    <?php endif; ?>
    <?php if ($checkoutEnabled): ?>
        <a href="<?php echo site_url('attendees/checkout'); ?>" class="btn btn-sm epr-btn-one ml-2">Pay now</a>
    <?php else: ?>
        <span class="ml-2">Payment will open soon.</span>
    <?php endif; ?>
</div>




<div class="container mt-5" style="margin-top: 90px !important;">
    <div class="col-md-10 mx-auto">
        <div style="position:relative;padding-top:56.25%;"><iframe src="https://iframe.mediadelivery.net/embed/405384/60589a7b-3fe7-40fb-a2ae-3c024f23e01e?autoplay=true&loop=false&muted=false&preload=true&responsive=true" loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true"></iframe></div>
    </div>
</div>

<?php if (! $portalLocked): ?>
    <div class="row w-100 mt-4 text-center">
        <a href="<?php echo base_url('attendees/lobby'); ?>"
           class="btn epr-btn-one"
           style="margin: auto; font-size: 17px;">ENTER LOBBY</a>
    </div>
<?php endif; ?>

<?php echo module_view('Web', 'includes/scripts'); ?>

</body>
</html>
