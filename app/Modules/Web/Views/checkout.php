<?php
$paypalConfigured = $paypalConfigured ?? false;
$paypalClientId = $paypalClientId ?? '';
$publicKey = $publicKey ?? '';
$hasFlutterwave = $canPay && $publicKey !== '';
$hasPaypal = $canPay && $paypalConfigured;
$currency = $checkout['currency'] ?? 'NGN';
$amount = (float) ($checkout['amount'] ?? 25000);
$productName = $checkout['productName'] ?? 'Full Access Pass';
$productDescription = $checkout['productDescription'] ?? 'Complete your payment.';
$formattedAmount = number_format($amount, 0);
$closeUrl = $checkout['successUrl'] ?? site_url('attendees/lobby');
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
        .rc-checkout-shell{position:absolute;inset:0;z-index:3;display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box;background:rgba(10,2,12,.62)}
        .rc-checkout-card{position:relative;width:100%;max-width:860px;max-height:100%;overflow:auto;box-sizing:border-box;background:#2a0d2e;border:1px solid rgba(255,255,255,.12);border-radius:20px;box-shadow:0 24px 70px rgba(0,0,0,.55);display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.15fr)}
        .rc-checkout-close{position:absolute;top:12px;right:16px;z-index:2;padding:0;border:0;background:transparent;color:rgba(255,255,255,.8);font:inherit;font-size:22px;line-height:1;text-decoration:none;cursor:pointer}.rc-checkout-close:hover{color:#fff;text-decoration:none}
        .rc-checkout-side{padding:30px;display:flex;flex-direction:column;gap:18px;background:radial-gradient(circle at 20% 0%,rgba(216,25,142,.28),rgba(0,0,0,0) 60%);border-right:1px solid rgba(255,255,255,.08)}
        .rc-checkout-main{padding:30px;display:flex;flex-direction:column;gap:16px;min-height:440px;box-sizing:border-box}
        .rc-method-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.rc-method{display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 6px;border:1px solid rgba(255,255,255,.2);border-radius:12px;background:rgba(255,255,255,.04);color:#fff;font:inherit;font-size:12px;font-weight:500;cursor:pointer}.rc-method i{font-size:17px}.rc-method.is-active{border-color:#d8198e;background:rgba(157,15,130,.55)}
        .rc-pay-pane{display:flex;flex-direction:column;gap:12px}.rc-pay-pane[hidden]{display:none!important}.rc-pay-field{display:flex;flex-direction:column;gap:6px}.rc-pay-field>span{font-size:12px;color:rgba(255,255,255,.7)}.rc-pay-field input,.rc-pay-field select{width:100%;height:44px;box-sizing:border-box;padding:0 14px;border:1px solid rgba(255,255,255,.3);border-radius:10px;outline:none;background:rgba(255,255,255,.04);color:#fff;font:inherit;font-size:14px}.rc-pay-field input:focus,.rc-pay-field select:focus{border-color:#efb11e}.rc-pay-two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .rc-bank-copy{font-size:13px;line-height:1.5;color:rgba(255,255,255,.75)}.rc-bank-table{display:flex;flex-direction:column;border:1px solid rgba(255,255,255,.14);border-radius:12px;background:rgba(255,255,255,.04);overflow:hidden}.rc-bank-row{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 14px;border-top:1px solid rgba(255,255,255,.06)}.rc-bank-row:first-child{border-top:0}.rc-bank-row span:first-child{font-size:12px;color:rgba(255,255,255,.7)}.rc-bank-row strong{font-size:14px;font-weight:600}.rc-bank-row:nth-child(n+3) strong{font-family:'JetBrains Mono',monospace}
        .rc-ussd-code{padding:18px;border:1px dashed rgba(239,177,30,.55);border-radius:12px;background:rgba(239,177,30,.08);display:flex;flex-direction:column;align-items:center;gap:6px;text-align:center}.rc-ussd-code span:first-child{font-size:12px;color:rgba(255,255,255,.7)}.rc-ussd-code strong{font-family:'JetBrains Mono',monospace;font-size:24px;font-weight:500;color:#efb11e;letter-spacing:.04em}.rc-ussd-code small{font-size:11px;color:rgba(255,255,255,.6)}
        .rc-checkout-pay{margin-top:auto;width:100%;height:50px;border:0;border-radius:15px;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);color:#fff;display:flex;align-items:center;justify-content:center;gap:10px;font:inherit;font-size:15px;font-weight:600;cursor:pointer}.rc-checkout-pay:hover{filter:brightness(1.15)}.rc-checkout-secure{display:flex;align-items:center;justify-content:center;gap:8px;font-size:11px;color:rgba(255,255,255,.6)}
        .rc-checkout-alert{border-radius:12px;padding:14px 16px;background:rgba(239,177,30,.1);border:1px solid rgba(239,177,30,.35);color:#fff;font-size:13px;line-height:1.5}#paypalButtons{margin-top:auto;min-height:44px}
        @media(max-width:780px){.rc-checkout-card{grid-template-columns:1fr}.rc-checkout-side{border-right:0;border-bottom:1px solid rgba(255,255,255,.08)}}
    </style>
</head>
<body class="rc-body">
<div class="rc-stage">
    <div class="rc-stage-inner">
        <div class="rc-checkout-shell">
            <main class="rc-checkout-card" data-screen-label="Checkout">
                <a class="rc-checkout-close" href="<?php echo esc($closeUrl); ?>" aria-label="Close checkout">&times;</a>

                <section class="rc-checkout-side">
                    <span class="rc-kicker">UPGRADE ACCESS</span>
                    <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="Accelerate" style="display:block;width:200px;height:auto">
                    <div style="display:flex;flex-direction:column;gap:4px"><span style="font-size:18px;font-weight:600"><?php echo esc($productName); ?></span><span style="font-size:13px;color:rgba(255,255,255,.7)">Emergence Conference 2026 · all days</span></div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <?php foreach (['Every live and paid session, all conference days', 'Replays for 30 days after the event', '1:1 bookings with speakers and booth teams', 'Accelerate 2026 Workbook and templates'] as $perk): ?>
                            <span style="display:flex;gap:10px;align-items:flex-start;font-size:13px;line-height:1.45"><i class="fa fa-check" style="color:#efb11e;margin-top:3px" aria-hidden="true"></i><?php echo esc($perk); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top:auto;padding-top:16px;border-top:1px solid rgba(255,255,255,.1);display:flex;flex-direction:column;gap:8px;font-size:13px">
                        <div style="display:flex;justify-content:space-between"><span style="color:rgba(255,255,255,.7)">Ticket</span><span><?php echo esc($currency); ?> <?php echo esc($formattedAmount); ?></span></div>
                        <div style="display:flex;justify-content:space-between"><span style="color:rgba(255,255,255,.7)">Processing fee</span><span><?php echo esc($currency); ?> 0</span></div>
                        <div style="display:flex;justify-content:space-between;align-items:baseline;padding-top:8px"><span style="font-weight:600">Total</span><span style="font-size:22px;font-weight:700;color:#efb11e"><?php echo esc($currency); ?> <?php echo esc($formattedAmount); ?></span></div>
                    </div>
                </section>

                <section class="rc-checkout-main">
                    <span style="font-size:18px;font-weight:600">Payment method</span>
                    <div class="rc-method-grid" role="group" aria-label="Payment method">
                        <button type="button" class="rc-method is-active" data-pay-method="card"><i class="fa fa-credit-card" aria-hidden="true"></i>Card</button>
                        <button type="button" class="rc-method" data-pay-method="bank"><i class="fa fa-university" aria-hidden="true"></i>Bank transfer</button>
                        <button type="button" class="rc-method" data-pay-method="ussd"><i class="fa fa-mobile" aria-hidden="true"></i>USSD</button>
                    </div>

                    <div class="rc-pay-pane" data-pay-pane="card">
                        <label class="rc-pay-field"><span>Card number</span><input placeholder="0000 0000 0000 0000" inputmode="numeric" autocomplete="off"></label>
                        <div class="rc-pay-two"><label class="rc-pay-field"><span>Expiry</span><input placeholder="MM / YY" autocomplete="off"></label><label class="rc-pay-field"><span>CVV</span><input placeholder="123" type="password" inputmode="numeric" autocomplete="off"></label></div>
                        <label class="rc-pay-field"><span>Name on card</span><input placeholder="<?php echo esc($checkout['name'] ?: 'Ada Okafor', 'attr'); ?>" autocomplete="off"></label>
                    </div>
                    <div class="rc-pay-pane" data-pay-pane="bank" hidden>
                        <span class="rc-bank-copy">Transfer exactly <?php echo esc($currency); ?> <?php echo esc($formattedAmount); ?> to the account below. Access unlocks as soon as we confirm it, usually within 5 minutes.</span>
                        <div class="rc-bank-table"><div class="rc-bank-row"><span>Bank</span><strong>Providus Bank</strong></div><div class="rc-bank-row"><span>Account name</span><strong>EPR Global Events</strong></div><div class="rc-bank-row"><span>Account number</span><strong>9902 418 271</strong></div><div class="rc-bank-row"><span>Reference</span><strong>ACC-26-04821</strong></div></div>
                    </div>
                    <div class="rc-pay-pane" data-pay-pane="ussd" hidden>
                        <label class="rc-pay-field"><span>Your bank</span><select><option>GTBank</option><option>Access Bank</option><option>First Bank</option><option>Zenith Bank</option><option>UBA</option></select></label>
                        <div class="rc-ussd-code"><span>Dial this code on your registered phone</span><strong>*737*000*4821#</strong><small>Code expires in 10 minutes</small></div>
                    </div>

                    <?php if (! $canPay): ?>
                        <div class="rc-checkout-alert"><?php echo esc($message); ?></div>
                    <?php elseif ($hasFlutterwave): ?>
                        <button type="button" id="payButton" class="rc-checkout-pay"><i class="fa fa-lock" aria-hidden="true"></i><span>Pay <?php echo esc($currency); ?> <?php echo esc($formattedAmount); ?></span></button>
                    <?php elseif ($hasPaypal): ?>
                        <div id="paypalButtons"></div>
                    <?php else: ?>
                        <div class="rc-checkout-alert">No payment provider is configured. Please contact support.</div>
                    <?php endif; ?>
                    <span class="rc-checkout-secure"><i class="fa fa-shield" aria-hidden="true"></i>Secured payment · receipt sent to your email</span>
                </section>
            </main>
        </div>
    </div>
</div>

<?php if ($canPay && ($hasFlutterwave || $hasPaypal)): ?>
    <?php if ($hasFlutterwave): ?><script src="https://checkout.flutterwave.com/v3.js"></script><?php endif; ?>
    <?php if ($hasPaypal): ?><script src="https://www.paypal.com/sdk/js?client-id=<?php echo rawurlencode($paypalClientId); ?>&currency=<?php echo rawurlencode($currency); ?>&intent=capture"></script><?php endif; ?>
    <script>
    (() => {
        let method = "card";
        const labels = {card: <?php echo json_encode('Pay ' . $currency . ' ' . $formattedAmount); ?>, bank: "I've sent the transfer", ussd: "I've dialled the code"};
        document.querySelectorAll("[data-pay-method]").forEach(button => button.addEventListener("click", () => {
            method = button.dataset.payMethod;
            document.querySelectorAll("[data-pay-method]").forEach(item => item.classList.toggle("is-active", item === button));
            document.querySelectorAll("[data-pay-pane]").forEach(pane => pane.hidden = pane.dataset.payPane !== method);
            const label = document.querySelector("#payButton span");
            if (label) label.textContent = labels[method];
        }));

        <?php if ($hasFlutterwave): ?>
        document.getElementById("payButton")?.addEventListener("click", () => {
            FlutterwaveCheckout({
                public_key: <?php echo json_encode($publicKey); ?>,
                tx_ref: <?php echo json_encode($checkout['txRef']); ?>,
                amount: <?php echo json_encode($amount); ?>,
                currency: <?php echo json_encode($currency); ?>,
                payment_options: method === "card" ? "card" : method === "bank" ? "banktransfer" : "ussd",
                redirect_url: <?php echo json_encode(site_url('checkout/verify')); ?>,
                meta: {mode: <?php echo json_encode($checkout['mode']); ?>, attendee_id: <?php echo json_encode((int) ($checkout['user']['id'] ?? 0)); ?>, conference_id: <?php echo json_encode($checkout['conferenceId']); ?>},
                customer: {email: <?php echo json_encode($checkout['email']); ?>, phone_number: <?php echo json_encode($checkout['attendee']['telephone'] ?? ''); ?>, name: <?php echo json_encode($checkout['name']); ?>},
                customizations: {title: "EPR Global", description: <?php echo json_encode($productDescription); ?>, logo: <?php echo json_encode(base_url('assets/images/eventslogo.png')); ?>}
            });
        });
        <?php endif; ?>

        <?php if ($hasPaypal): ?>
        if (document.getElementById("paypalButtons")) {
            paypal.Buttons({
                style: {shape: "rect", height: 44},
                createOrder: async () => {
                    const response = await fetch(<?php echo json_encode(site_url('checkout/paypal/order')); ?>, {method: "POST", headers: {"Content-Type": "application/json"}, body: JSON.stringify({payment_id: <?php echo json_encode((int) ($checkout['paymentId'] ?? 0)); ?>})});
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || "Unable to create PayPal order.");
                    return result.orderID;
                },
                onApprove: async data => {
                    const response = await fetch(<?php echo json_encode(site_url('checkout/paypal/capture')); ?>, {method: "POST", headers: {"Content-Type": "application/json"}, body: JSON.stringify({payment_id: <?php echo json_encode((int) ($checkout['paymentId'] ?? 0)); ?>, order_id: data.orderID})});
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || "Unable to capture PayPal payment.");
                    window.location.href = result.redirect_url || <?php echo json_encode($closeUrl); ?>;
                },
                onError: error => alert(error.message || "PayPal checkout could not be completed.")
            }).render("#paypalButtons");
        }
        <?php endif; ?>
    })();
    </script>
<?php endif; ?>
</body>
</html>
