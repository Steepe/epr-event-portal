<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/explore'), 'activeTab' => 'explore', 'title' => 'Upgrade', 'screenLabel' => 'Upgrade']) ?>
<?php
$countries = $countries ?? [];
$isPremium = (int) ($premium ?? 0) === 1;
?>

<div class="m-screen">
    <div class="m-head">
        <a class="m-backlink" href="<?= site_url('mobile/explore') ?>" data-back="<?= site_url('mobile/explore') ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK</a>
        <span class="m-kicker">Accelerate 2026</span>
        <h1 class="m-title m-title--28">Upgrade to Premium Access</h1>
        <span class="m-sub">Unlock all paid sessions across the platform.</span>
    </div>

    <?php if ($isPremium): ?>
        <div class="m-card m-info m-info--20"><b style="font-weight:700">You already have Premium Access.</b><span style="color:rgba(255,255,255,.7);line-height:normal">If this is incorrect, kindly contact support.</span></div>
    <?php else: ?>
        <div class="m-card m-upgrade">
            <div class="m-upgrade__top">
                <div><small>Plan</small><span class="m-upgrade__plan">Premium Access</span></div>
                <div><small>Price</small><span class="m-upgrade__price" id="upgradePrice">—</span></div>
            </div>
            <label>Select Country
                <select class="m-input m-input--14" id="upgradeCountry">
                    <?php foreach ($countries as $code => $country): ?>
                        <option value="<?= esc($code, 'attr') ?>"<?= $code === ($selected_country ?? '') ? ' selected' : '' ?>><?= esc($country['name']) ?> (<?= esc($code) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </label>
            <span class="m-upgrade__note">Gateway auto-selects: <strong>Flutterwave</strong> for all local (non-USD) currencies, <strong>PayPal</strong> for USD.</span>
            <div class="m-upgrade__actions">
                <button class="m-btn" type="button" id="upgradePay">Pay &amp; Upgrade</button>
                <button class="m-btn-ghost m-btn-ghost--44" type="button" id="upgradeInfo">Payment Info</button>
                <span class="m-upgrade__gateway" id="upgradeGateway">GATEWAY: —</span>
            </div>
        </div>
        <div class="m-card m-info"><b>Session Access</b><span>Upgrading grants uninterrupted access to all paid sessions across the conference.</span></div>
    <?php endif; ?>
</div>

<?php if (! $isPremium): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var countries = <?= json_encode($countries) ?>;
    var countryPrices = <?= json_encode((object) ($countryPrices ?? [])) ?>;
    var currencyPrices = <?= json_encode((object) ($currencyPrices ?? [])) ?>;
    var basePrice = <?= json_encode((float) ($baseUSDPrice ?? 20)) ?>;
    var select = document.getElementById('upgradeCountry');
    var pay = document.getElementById('upgradePay');

    function quote() {
        var code = select.value;
        var currency = (countries[code] && countries[code].currency) || 'USD';
        var amount = basePrice;
        if (countryPrices[code] !== undefined) amount = parseFloat(countryPrices[code]);
        else if (currencyPrices[currency] !== undefined) amount = parseFloat(currencyPrices[currency]);
        else currency = 'USD';
        return { country: code, currency: currency, amount: amount, gateway: currency !== 'USD' ? 'flutterwave' : 'paypal' };
    }
    function gatewayName(q) { return q.gateway === 'flutterwave' ? 'Flutterwave' : 'PayPal'; }
    function paint() {
        var q = quote();
        document.getElementById('upgradePrice').textContent = q.amount.toLocaleString() + ' ' + q.currency;
        document.getElementById('upgradeGateway').textContent = 'GATEWAY: ' + gatewayName(q);
    }
    select.addEventListener('change', paint);
    paint();

    document.getElementById('upgradeInfo').addEventListener('click', function () {
        EPR.toast('Payments are processed securely by ' + gatewayName(quote()) + '.');
    });

    pay.addEventListener('click', function () {
        var q = quote();
        pay.disabled = true; pay.textContent = 'Processing...';
        EPR.post(document.body.getAttribute('data-api') + '/payments/initialize', {
            user_id: <?= json_encode((string) ($user_id ?? '')) ?>,
            conference_id: <?= json_encode((string) ($conference_id ?? '')) ?>,
            gateway: q.gateway, country: q.country, currency: q.currency, amount: q.amount
        }).then(function (res) {
            if (!res.body || res.body.status !== 'success' || !res.body.redirect_url) throw new Error((res.body && res.body.message) || 'Unable to initialize payment.');
            window.location = res.body.redirect_url;
        }).catch(function (error) {
            EPR.toast(error.message || 'Unable to initialize payment.', true);
            pay.disabled = false; pay.innerHTML = 'Pay &amp; Upgrade';
        });
    });
});
</script>
<?php endif; ?>

<?= module_view('MobileApp', 'includes/footer') ?>
