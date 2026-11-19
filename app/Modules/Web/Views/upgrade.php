<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 20/11/2025
 * Time: 20:27
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$session      = session();
$currentPlan  = $currentPlan ?? (int) ($session->get('plan') ?? 1);
$userId       = $user_id ?? '';
$conferenceId = $conference_id ?? '';

/*
    Variables sent from controller:
    ------------------------------------
    $selected_country
    $countryList          // [code => name]
    $countryToCurrency    // [code => currency]
    $currencyPrices       // [currency => amount]
    $countryPrices        // [country => amount]
    $baseUSDPrice
*/
?>

<style>
.upgrade-wrapper { max-width:900px; margin:30px auto; padding:24px; background:#fff;
    border-radius:12px; box-shadow:0 6px 20px rgba(0,0,0,0.06); }

.header { display:flex; align-items:center; gap:16px; margin-bottom:18px; }
.header h2 { color:#9D0F82; font-weight:700; margin:0; }

.card { padding:16px; border-radius:12px; background:#faf3f9;
    border:1px solid #f0d9e9; margin-bottom:18px; }

.price-big { font-size:28px; font-weight:800; color:#9D0F82; }

.select { width:100%; padding:12px; border-radius:8px; border:1px solid #ddd; }

.btn-epr-purple { background:#9D0F82;color:#fff;padding:10px 16px;border-radius:8px;border:none;
    font-weight:700;cursor:pointer; }

.btn-secondary { background:#f3f3f3;color:#444;padding:9px 12px;border-radius:8px;
    border:1px solid #e1e1e1; cursor:pointer; }

.small { font-size:13px; color:#666; }
</style>


<div class="upgrade-wrapper">

    <div class="header">
        <h2>Upgrade to Premium Access</h2>
        <div class="small">Unlock all paid sessions across the platform</div>
    </div>


    <?php if ($currentPlan >= 2): ?>

        <div class="card">
            <strong>You already have Premium Access.</strong>
            <p class="small">If this is incorrect, kindly contact support.</p>
        </div>

    <?php else: ?>

        <!-- Upgrade Card -->
        <div class="card">

            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div class="small">Plan</div>
                    <div style="font-weight:700;">Premium Access</div>
                </div>

                <div>
                    <div class="small">Price</div>
                    <div class="price-big" id="displayPrice">--</div>
                </div>
            </div>

            <div style="margin-top:20px;">
                <label class="small">Select Country</label>
                <select id="countrySelect" class="select">
                    <?php foreach ($countryList as $code => $name): ?>
                        <option value="<?php echo esc($code); ?>"
                            <?php echo ($selected_country == $code) ? 'selected' : ''; ?>>
                            <?php echo esc($name . " ({$code})"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="small" style="margin-top:12px;">
                Gateway auto-selects:
                <strong>Flutterwave</strong> for all local (non-USD) currencies,
                <strong>PayPal</strong> for USD.
            </div>

            <div style="margin-top:18px;">
                <button id="payNowBtn" class="btn-epr-purple">Pay & Upgrade</button>
                <button type="button" class="btn-secondary">Payment Info</button>
                <div class="small" id="gatewayBadge" style="margin-top:6px;">Gateway: —</div>
            </div>

        </div>


        <!-- Info Card -->
        <div class="card">
            <div style="font-weight:700;">Session Access</div>
            <div class="small">
                Upgrading grants uninterrupted access to all paid sessions across the conference.
            </div>
        </div>

    <?php endif; ?>

</div>


<script>
/* Server-sent authoritative maps */
const countryToCurrency = <?php echo json_encode($countryToCurrency); ?>;
const currencyPrices    = <?php echo json_encode($currencyPrices); ?>;
const countryPrices     = <?php echo json_encode($countryPrices); ?>;
const baseUSDPrice      = <?php echo json_encode($baseUSDPrice); ?>;

const countrySelect = document.getElementById('countrySelect');
const displayPrice  = document.getElementById('displayPrice');
const gatewayBadge  = document.getElementById('gatewayBadge');
const payNowBtn     = document.getElementById('payNowBtn');

/**
 * Resolve currency from DB mapping
 */
function currencyForCountry(code) {
    return (countryToCurrency[code] ?? 'USD').toUpperCase();
}

/**
 * Resolve price using DB priority:
 * 1. Country-specific price (tbl_premium_prices.country)
 * 2. Currency-level price (tbl_premium_prices.currency)
 * 3. USD base fallback
 */
function priceForCountry(code) {
    if (countryPrices[code] !== undefined) {
        return parseFloat(countryPrices[code]);
    }

    const currency = currencyForCountry(code);
    if (currencyPrices[currency] !== undefined) {
        return parseFloat(currencyPrices[currency]);
    }

    return parseFloat(baseUSDPrice);
}

/**
 * Gateway Decision (correct non-hardcoded logic)
 * If currency != USD → Flutterwave
 * Else → PayPal
 */
function decideGateway(code) {
    const currency = currencyForCountry(code);
    return currency !== 'USD' ? 'flutterwave' : 'paypal';
}

/**
 * Update visible UI components
 */
function updateUI() {
    const country  = countrySelect.value;
    const currency = currencyForCountry(country);
    const amount   = priceForCountry(country);
    const gateway  = decideGateway(country);

    displayPrice.textContent = amount + ' ' + currency;
    gatewayBadge.textContent = "Gateway: " + (gateway === 'flutterwave' ? "Flutterwave" : "PayPal");
}

countrySelect.addEventListener('change', updateUI);
updateUI(); // initial render


/**
 * PAYMENT FLOW
 */
payNowBtn.addEventListener('click', async () => {
    payNowBtn.disabled = true;

    try {
        const country  = countrySelect.value;
        const currency = currencyForCountry(country);
        const amount   = priceForCountry(country);
        const gateway  = decideGateway(country);

        const body =
            "user_id=" + encodeURIComponent("<?php echo $userId; ?>") +
            "&conference_id=" + encodeURIComponent("<?php echo $conferenceId; ?>") +
            "&gateway=" + encodeURIComponent(gateway) +
            "&country=" + encodeURIComponent(country) +
            "&currency=" + encodeURIComponent(currency) +
            "&amount=" + encodeURIComponent(amount);

        const res = await fetch("<?php echo base_url('api/payments/initialize'); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-API-KEY": "<?php echo getenv('api.securityKey'); ?>"
            },
            body
        });

        const json = await res.json();

        if (!json || json.status !== "success") {
            throw new Error(json?.message || "Unable to initialize payment");
        }

        window.location = json.redirect_url;

    } catch (err) {
        alert("Payment failed: " + err.message);
        console.error(err);

    } finally {
        payNowBtn.disabled = false;
    }
});
</script>

<?php echo module_view('Web', 'includes/scripts'); ?>
</body>
</html>
