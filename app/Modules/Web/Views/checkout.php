<?php
$paypalConfigured = $paypalConfigured ?? false;
$paypalClientId = $paypalClientId ?? '';
$publicKey = $publicKey ?? '';
$hasFlutterwave = $canPay && $publicKey !== '';
$hasPaypal = $canPay && $paypalConfigured;
$currency = $checkout['currency'] ?? 'NGN';
$amount = (float) ($checkout['amount'] ?? 0);
$productName = $checkout['productName'] ?? 'Full Access Pass';
$productDescription = $checkout['productDescription'] ?? 'Complete your payment.';
$modeLabel = ($checkout['mode'] ?? '') === 'attendee' ? 'Portal attendee payment' : 'Emergence registration upsell';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc($productName); ?></title>
    <link rel="shortcut icon" href="<?php echo asset_url('images/favicon.png'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo asset_url('font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo asset_url('css/accelerate-portal.css'); ?>" rel="stylesheet" type="text/css">
    <script defer src="<?php echo asset_url('js/accelerate-background.js'); ?>"></script>
    <style>
        .rc-checkout-shell {
            position: absolute;
            inset: 0;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-sizing: border-box;
            background: rgba(10,2,12,.28);
        }
        .rc-checkout-card {
            position: relative;
            width: 100%;
            max-width: 860px;
            max-height: 100%;
            overflow: auto;
            box-sizing: border-box;
            background: #2a0d2e;
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 20px;
            box-shadow: 0 24px 70px rgba(0,0,0,.55);
            display: grid;
            grid-template-columns: minmax(0,1fr) minmax(0,1.15fr);
        }
        .rc-checkout-side {
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            background: radial-gradient(circle at 20% 0%, rgba(216,25,142,.28), rgba(0,0,0,0) 60%);
            border-right: 1px solid rgba(255,255,255,.08);
        }
        .rc-checkout-main {
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: 440px;
            box-sizing: border-box;
        }
        .rc-method-grid {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 8px;
        }
        .rc-method {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 6px;
            border-radius: 12px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.16);
            color: #fff;
            font-size: 12px;
            font-weight: 500;
        }
        .rc-method.is-active {
            border-color: #efb11e;
            background: rgba(239,177,30,.13);
        }
        .rc-checkout-alert {
            border-radius: 12px;
            padding: 14px 16px;
            background: rgba(239,177,30,.1);
            border: 1px solid rgba(239,177,30,.35);
            color: #fff;
            font-size: 13px;
            line-height: 1.5;
        }
        #paypalButtons {
            margin-top: 2px;
            min-height: 44px;
        }
        @media (max-width: 780px) {
            .rc-checkout-card {
                grid-template-columns: 1fr;
            }
            .rc-checkout-side {
                border-right: 0;
                border-bottom: 1px solid rgba(255,255,255,.08);
            }
        }
    </style>
</head>
<body class="rc-body">
<div class="rc-stage">
    <div class="rc-stage-inner">
        <div class="rc-checkout-shell">
            <main class="rc-checkout-card" data-screen-label="Checkout">
                <a href="<?php echo esc($checkout['successUrl'] ?? site_url('attendees/lobby')); ?>" aria-label="Close checkout" style="position:absolute;top:12px;right:16px;z-index:2;font-size:22px;cursor:pointer;color:rgba(255,255,255,.8);text-decoration:none">&times;</a>

                <section class="rc-checkout-side">
                    <span class="rc-kicker">Upgrade Access</span>
                    <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="Accelerate" style="display:block;width:200px;height:auto">
                    <div style="display:flex;flex-direction:column;gap:4px">
                        <span style="font-size:18px;font-weight:600"><?php echo esc($productName); ?></span>
                        <span style="font-size:13px;color:rgba(255,255,255,.7)"><?php echo esc($modeLabel); ?></span>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:10px">
                        <span style="display:flex;gap:10px;align-items:flex-start;font-size:13px;line-height:1.45"><i class="fa fa-check" style="color:#efb11e;margin-top:3px"></i>Full portal access</span>
                        <span style="display:flex;gap:10px;align-items:flex-start;font-size:13px;line-height:1.45"><i class="fa fa-check" style="color:#efb11e;margin-top:3px"></i>Sessions, agenda and networking tools</span>
                        <span style="display:flex;gap:10px;align-items:flex-start;font-size:13px;line-height:1.45"><i class="fa fa-check" style="color:#efb11e;margin-top:3px"></i>Receipt sent to your email</span>
                    </div>

                    <div style="margin-top:auto;padding-top:16px;border-top:1px solid rgba(255,255,255,.1);display:flex;flex-direction:column;gap:8px;font-size:13px">
                        <div style="display:flex;justify-content:space-between"><span style="color:rgba(255,255,255,.7)">Ticket</span><span><?php echo esc($currency); ?> <?php echo esc(number_format($amount, 2)); ?></span></div>
                        <div style="display:flex;justify-content:space-between"><span style="color:rgba(255,255,255,.7)">Processing fee</span><span><?php echo esc($currency); ?> 0.00</span></div>
                        <div style="display:flex;justify-content:space-between;align-items:baseline;padding-top:8px"><span style="font-weight:600">Total</span><span style="font-size:22px;font-weight:700;color:#efb11e"><?php echo esc($currency); ?> <?php echo esc(number_format($amount, 2)); ?></span></div>
                    </div>
                </section>

                <section class="rc-checkout-main">
                    <span style="font-size:18px;font-weight:600">Payment method</span>
                    <p style="margin:0;color:rgba(255,255,255,.72);font-size:13px;line-height:1.5"><?php echo esc($productDescription); ?></p>

                    <div class="rc-method-grid">
                        <span class="rc-method <?php echo $hasFlutterwave ? 'is-active' : ''; ?>"><i class="fa fa-credit-card" style="font-size:17px"></i>Card</span>
                        <span class="rc-method <?php echo $hasFlutterwave ? '' : 'is-active'; ?>"><i class="fa fa-university" style="font-size:17px"></i>Bank</span>
                        <span class="rc-method <?php echo $hasPaypal ? 'is-active' : ''; ?>"><i class="fa fa-paypal" style="font-size:17px"></i>PayPal</span>
                    </div>

                    <dl style="display:grid;grid-template-columns:90px minmax(0,1fr);gap:9px 14px;margin:6px 0 0;font-size:13px">
                        <dt style="color:rgba(255,255,255,.62)">Name</dt>
                        <dd style="margin:0;font-weight:600"><?php echo esc($checkout['name'] ?: 'Guest'); ?></dd>
                        <dt style="color:rgba(255,255,255,.62)">Email</dt>
                        <dd style="margin:0;font-weight:600;min-width:0;overflow-wrap:anywhere"><?php echo esc($checkout['email'] ?: 'Not provided'); ?></dd>
                    </dl>

                    <?php if (! $canPay): ?>
                        <div class="rc-checkout-alert"><?php echo esc($message); ?></div>
                    <?php else: ?>
                        <div style="margin-top:auto;display:flex;flex-direction:column;gap:14px">
                            <?php if ($hasFlutterwave): ?>
                                <button type="button" id="payButton" class="rc-btn" style="width:100%;display:flex;align-items:center;justify-content:center;gap:10px">
                                    <i class="fa fa-lock" aria-hidden="true"></i>Pay <?php echo esc($currency); ?> <?php echo esc(number_format($amount, 2)); ?>
                                </button>
                            <?php endif; ?>

                            <?php if ($hasFlutterwave && $hasPaypal): ?>
                                <span style="display:flex;align-items:center;gap:12px;color:rgba(255,255,255,.58);font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.16em;text-transform:uppercase">
                                    <span style="height:1px;background:rgba(255,255,255,.12);flex:1"></span>or<span style="height:1px;background:rgba(255,255,255,.12);flex:1"></span>
                                </span>
                            <?php endif; ?>

                            <?php if ($hasPaypal): ?>
                                <div id="paypalButtons"></div>
                            <?php elseif ($hasFlutterwave): ?>
                                <span style="font-size:12px;color:rgba(255,255,255,.58);text-align:center">PayPal will appear here after PayPal credentials are configured.</span>
                            <?php else: ?>
                                <div class="rc-checkout-alert">No payment provider is configured. Please contact support.</div>
                            <?php endif; ?>

                            <span style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:11px;color:rgba(255,255,255,.6)">
                                <i class="fa fa-shield" aria-hidden="true"></i>Secured payment
                            </span>
                        </div>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
</div>

<?php if ($canPay && ($hasFlutterwave || $hasPaypal)): ?>
<?php if ($hasFlutterwave): ?>
<script src="https://checkout.flutterwave.com/v3.js"></script>
<?php endif; ?>
<?php if ($hasPaypal): ?>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo rawurlencode($paypalClientId); ?>&currency=<?php echo rawurlencode($currency); ?>&intent=capture"></script>
<?php endif; ?>
<script>
<?php if ($hasFlutterwave): ?>
document.getElementById("payButton").addEventListener("click", function () {
    FlutterwaveCheckout({
        public_key: <?php echo json_encode($publicKey); ?>,
        tx_ref: <?php echo json_encode($checkout['txRef']); ?>,
        amount: <?php echo json_encode($amount); ?>,
        currency: <?php echo json_encode($currency); ?>,
        payment_options: "card, mobilemoneyghana, ussd",
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
            title: "EPR Global",
            description: <?php echo json_encode($productDescription); ?>,
            logo: <?php echo json_encode(base_url('assets/images/eventslogo.png')); ?>
        }
    });
});
<?php endif; ?>

<?php if ($hasPaypal): ?>
paypal.Buttons({
    style: {
        shape: "rect",
        height: 44
    },
    createOrder: async function () {
        const response = await fetch(<?php echo json_encode(site_url('checkout/paypal/order')); ?>, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                payment_id: <?php echo json_encode((int) $checkout['paymentId']); ?>
            })
        });
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || "Unable to create PayPal order.");
        }
        return result.orderID;
    },
    onApprove: async function (data) {
        const response = await fetch(<?php echo json_encode(site_url('checkout/paypal/capture')); ?>, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                payment_id: <?php echo json_encode((int) $checkout['paymentId']); ?>,
                order_id: data.orderID
            })
        });
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.message || "Unable to capture PayPal payment.");
        }
        window.location.href = result.redirect_url || <?php echo json_encode($checkout['successUrl']); ?>;
    },
    onError: function (error) {
        alert(error.message || "PayPal checkout could not be completed.");
    }
}).render("#paypalButtons");
<?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>
