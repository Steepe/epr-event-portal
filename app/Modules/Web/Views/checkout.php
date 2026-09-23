<?php
$paypalConfigured = $paypalConfigured ?? false;
$paypalClientId = $paypalClientId ?? '';
$publicKey = $publicKey ?? '';
$hasFlutterwave = $canPay && $publicKey !== '';
$hasPaypal = $canPay && $paypalConfigured;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc($checkout['productName'] ?? 'Checkout'); ?></title>
    <link rel="shortcut icon" href="<?php echo asset_url('images/favicon.png'); ?>">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: #f7f4f8;
            color: #1E1A2E;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            display: grid;
            place-items: center;
            padding: 24px;
        }
        .checkout {
            width: 100%;
            max-width: 520px;
            background: #fff;
            border: 1px solid #e9e2ee;
            border-radius: 8px;
            box-shadow: 0 16px 44px rgba(43, 26, 69, .08);
            padding: 28px;
        }
        .brand {
            width: 170px;
            margin-bottom: 20px;
        }
        .eyebrow {
            color: #E5337B;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }
        h1 {
            margin: 8px 0 8px;
            font-size: 28px;
            line-height: 1.15;
        }
        p {
            color: #6B6778;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        .summary {
            background: #2B1A45;
            border-radius: 8px;
            color: #fff;
            margin: 24px 0;
            padding: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }
        .summary p {
            color: #E9E4F2;
            margin-top: 8px;
        }
        .price {
            font-size: 34px;
            font-weight: 800;
            white-space: nowrap;
        }
        dl {
            display: grid;
            grid-template-columns: 90px 1fr;
            gap: 8px 14px;
            margin: 0 0 24px;
            font-size: 14px;
        }
        dt {
            color: #6B6778;
        }
        dd {
            margin: 0;
            font-weight: 600;
        }
        button, .link-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 5px;
            background: #E5337B;
            color: #fff;
            font: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        button:hover, .link-button:hover {
            background: #C41F63;
        }
        .alert {
            color: #9b1c38;
            background: #fdecef;
            border-radius: 6px;
            padding: 14px;
            margin-top: 20px;
        }
        .divider {
            align-items: center;
            color: #8a8495;
            display: flex;
            font-size: 12px;
            font-weight: 700;
            gap: 12px;
            letter-spacing: .08em;
            margin: 18px 0;
            text-transform: uppercase;
        }
        .divider::before,
        .divider::after {
            background: #e9e2ee;
            content: "";
            flex: 1;
            height: 1px;
        }
        #paypalButtons {
            margin-top: 12px;
        }
        .muted {
            color: #8a8495;
            font-size: 13px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
<main class="checkout">
    <img class="brand" src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Global">

    <div class="eyebrow">Secure checkout</div>
    <h1><?php echo esc($checkout['productName'] ?? 'Checkout'); ?></h1>
    <p><?php echo esc($checkout['productDescription'] ?? 'Complete your payment.'); ?></p>

    <section class="summary">
        <div class="summary-row">
            <div>
                <strong><?php echo esc($checkout['productName'] ?? 'Ticket'); ?></strong>
                <p><?php echo esc($checkout['mode'] === 'attendee' ? 'Portal attendee payment' : 'Emergence registration upsell'); ?></p>
            </div>
            <div class="price">
                <?php echo esc($checkout['currency']); ?> <?php echo esc(number_format((float) $checkout['amount'], 2)); ?>
            </div>
        </div>
    </section>

    <dl>
        <dt>Name</dt>
        <dd><?php echo esc($checkout['name'] ?: 'Guest'); ?></dd>
        <dt>Email</dt>
        <dd><?php echo esc($checkout['email'] ?: 'Not provided'); ?></dd>
    </dl>

    <?php if (! $canPay): ?>
        <div class="alert"><?php echo esc($message); ?></div>
    <?php else: ?>
        <?php if ($hasFlutterwave): ?>
            <button type="button" id="payButton">Pay now</button>
        <?php endif; ?>

        <?php if ($hasFlutterwave && $hasPaypal): ?>
            <div class="divider">or</div>
        <?php endif; ?>

        <?php if ($hasPaypal): ?>
            <div id="paypalButtons"></div>
        <?php elseif ($hasFlutterwave): ?>
            <p class="muted">PayPal will appear here after PayPal credentials are configured.</p>
        <?php else: ?>
            <div class="alert">No payment provider is configured. Please contact support.</div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php if ($canPay && ($hasFlutterwave || $hasPaypal)): ?>
<?php if ($hasFlutterwave): ?>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<?php endif; ?>
<?php if ($hasPaypal): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo rawurlencode($paypalClientId); ?>&currency=<?php echo rawurlencode($checkout['currency']); ?>&intent=capture"></script>
<?php endif; ?>
<script>
<?php if ($hasFlutterwave): ?>
document.getElementById('payButton').addEventListener('click', function () {
    FlutterwaveCheckout({
        public_key: <?php echo json_encode($publicKey); ?>,
        tx_ref: <?php echo json_encode($checkout['txRef']); ?>,
        amount: <?php echo json_encode((float) $checkout['amount']); ?>,
        currency: <?php echo json_encode($checkout['currency']); ?>,
        payment_options: 'card, mobilemoneyghana, ussd',
        redirect_url: <?php echo json_encode(site_url('checkout/verify')); ?>,
        meta: {
            mode: <?php echo json_encode($checkout['mode']); ?>,
            attendee_id: <?php echo json_encode((int) $checkout['user']['id']); ?>,
            conference_id: <?php echo json_encode($checkout['conferenceId']); ?>
        },
        customer: {
            email: <?php echo json_encode($checkout['email']); ?>,
            phone_number: <?php echo json_encode($checkout['attendee']['telephone'] ?? ''); ?>,
            name: <?php echo json_encode($checkout['name']); ?>
        },
        customizations: {
            title: 'EPR Global',
            description: <?php echo json_encode($checkout['productDescription']); ?>,
            logo: <?php echo json_encode(base_url('assets/images/eventslogo.png')); ?>
        }
    });
});
<?php endif; ?>

<?php if ($hasPaypal): ?>
paypal.Buttons({
    style: {
        shape: 'rect',
        height: 44
    },
    createOrder: async function () {
        const response = await fetch(<?php echo json_encode(site_url('checkout/paypal/order')); ?>, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                payment_id: <?php echo json_encode((int) $checkout['paymentId']); ?>
            })
        });
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || 'Unable to create PayPal order.');
        }
        return result.orderID;
    },
    onApprove: async function (data) {
        const response = await fetch(<?php echo json_encode(site_url('checkout/paypal/capture')); ?>, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                payment_id: <?php echo json_encode((int) $checkout['paymentId']); ?>,
                order_id: data.orderID
            })
        });
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || 'Unable to capture PayPal payment.');
        }
        window.location.href = result.redirect_url || <?php echo json_encode($checkout['successUrl']); ?>;
    },
    onError: function (error) {
        alert(error.message || 'PayPal checkout could not be completed.');
    }
}).render('#paypalButtons');
<?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>
