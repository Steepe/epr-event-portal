<?php
echo module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => ! empty($isReplay) ? false : site_url('mobile/store'), 'activeTab' => 'explore', 'title' => 'Store purchase', 'screenLabel' => 'Store']);
$productName = (string) ($product['name'] ?? 'your purchase');
?>
<div class="m-screen">
  <section class="m-card" style="padding:28px 22px;text-align:center">
    <?php if ($hasAccess): ?>
      <i class="fa fa-check-circle" aria-hidden="true" style="font-size:48px;color:#efb11e"></i>
      <h1 class="m-title" style="margin-top:16px">Your access is ready</h1>
      <p class="m-sub" style="line-height:1.6">Your access to <strong><?= esc($productName) ?></strong> is active.</p>
      <?php if (! empty($product['access_url'])): ?><a class="m-btn" href="<?= esc($product['access_url'], 'attr') ?>" target="_blank" rel="noopener">Open purchase</a><?php endif; ?>
    <?php else: ?>
      <i class="fa fa-clock-o" aria-hidden="true" style="font-size:48px;color:#efb11e"></i>
      <h1 class="m-title" style="margin-top:16px">Payment is being confirmed</h1>
      <p class="m-sub" style="line-height:1.6">Your payment is being confirmed securely by Stripe.<?= ! empty($isReplay) ? ' You will return to the sessions list when replay access is ready.' : ' Refresh this page shortly to see your access.' ?></p>
    <?php endif; ?>
    <?php if (! empty($isReplay)): ?>
      <a class="m-btn-ghost m-btn-ghost--44" style="margin-top:14px" href="<?= esc(current_url() . '?' . service('request')->getUri()->getQuery(), 'attr') ?>">Check again</a>
    <?php else: ?>
      <a class="m-btn-ghost m-btn-ghost--44" style="margin-top:14px" href="<?= site_url('mobile/store') ?>">Return to store</a>
    <?php endif; ?>
  </section>
</div>
<?php if (! $hasAccess && ! empty($isReplay)): ?>
<script>
(() => {
  const url = new URL(window.location.href);
  const checks = Number(url.searchParams.get('confirmation_check') || 0);
  if (checks < 10) window.setTimeout(() => {
    url.searchParams.set('confirmation_check', String(checks + 1));
    window.location.replace(url.toString());
  }, 3000);
})();
</script>
<?php endif; ?>
<?= module_view('MobileApp', 'includes/footer') ?>
