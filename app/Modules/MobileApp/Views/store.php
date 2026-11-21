<?php
$product = $products[0] ?? null;
$productId = (int) ($product['product_id'] ?? 0);
$owned = $productId > 0 && in_array($productId, $entitledProductIds ?? [], true);
$currency = strtoupper((string) ($product['currency'] ?? 'USD'));
$pricePrefix = $currency === 'USD' ? '$' : $currency . ' ';
$price = $product ? $pricePrefix . number_format((float) $product['price_amount'], 2) : '$19.99';
$accessUrl = '';
if ($owned && ($product['access_type'] ?? '') === 'replays') {
    $accessUrl = site_url('mobile/store/replays/' . $productId);
} elseif ($owned && ($product['access_type'] ?? '') === 'external' && ! empty($product['access_url'])) {
    $accessUrl = (string) $product['access_url'];
}

echo module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/explore'), 'activeTab' => 'explore', 'title' => 'Emergence Store', 'screenLabel' => 'Store']);
?>
<style>
  .m-app--store,.m-app--store .m-shell{background:#fcf9fd;color:#451a5d}.m-app--store .m-bg{display:none}.m-app--store .m-topbar,.m-app--store .m-tabs{background:#fff;border-color:rgba(85,31,101,.15)}.m-app--store .m-topbar__logo img{filter:brightness(0) saturate(100%) invert(14%) sepia(35%) saturate(2255%) hue-rotate(245deg) brightness(83%) contrast(100%)}.m-app--store .m-topbar__back,.m-app--store .m-topbar__bell{color:#552263}.m-app--store .m-tabs a{color:#765d7a}.m-app--store .m-tabs a.is-active{color:#d8198e}.m-store{margin:-1px -16px 0;min-height:calc(100vh - 150px);border-top:7px solid #e71f86;background:#fcf9fd;padding:24px 18px 30px}.m-store__brand{font-size:16px;letter-spacing:.18em;font-weight:800;color:#712b88}.m-store__brand em{font-style:normal;color:#e71f86}.m-store__theme{display:block;margin-top:7px;color:#66516e;font-size:10px;font-weight:600;letter-spacing:.07em;line-height:1.45}.m-store__date{display:inline-block;margin-top:17px;border-radius:6px;background:#742787;padding:8px 12px;color:#fff;font-size:11px;font-weight:800}.m-store__eyebrow{margin:32px 0 8px;color:#e71f86;font-size:13px;font-weight:800;letter-spacing:.12em}.m-store h1{margin:0;color:#421754;font-size:35px;line-height:1.06;letter-spacing:-.035em}.m-store__intro{margin:13px 0 18px;color:#66516e;font-size:16px;line-height:1.45}.m-store__price{border-radius:20px;background:#40144e;padding:22px;color:#fff;box-shadow:0 14px 24px rgba(48,11,59,.2)}.m-store__price-label{display:block;color:#f6d5e8;font-size:12px;font-weight:700;letter-spacing:.1em}.m-store__amount{margin-top:3px;color:#f5b736;font-size:63px;font-weight:800;line-height:1;letter-spacing:-.06em}.m-store__full{margin:8px 0 0;font-size:14px}.m-store__day{display:inline-block;margin-top:11px;border-radius:18px;background:#ea1d86;padding:6px 12px;color:#fff;font-size:12px;font-weight:800;letter-spacing:.04em}.m-store__reserve{display:flex;width:100%;min-height:52px;align-items:center;justify-content:center;box-sizing:border-box;margin-top:15px;border:2px solid #e71f86;border-radius:11px;background:#fff;color:#e71f86!important;font-size:15px;font-weight:800;letter-spacing:.09em;text-decoration:none}.m-store__reserve:hover{background:#e71f86;color:#fff!important;text-decoration:none}.m-store__status,.m-store__notice{margin-top:15px;border:1px solid #e5a72e;border-radius:11px;background:#fff7e7;padding:13px;color:#6a4b16;font-size:13px;line-height:1.45}.m-store__notice{border-color:#e71f86;background:#fff0f8;color:#7e1b5f}.m-store__outcomes{margin-top:28px;border:2px solid #e8aa31;border-radius:23px;background:#fff8ec;padding:19px 14px}.m-store__outcomes h2{margin:0 0 13px;color:#702887;font-size:19px;letter-spacing:.02em}.m-store__benefits{display:grid;gap:10px}.m-store__benefit{border-radius:13px;background:#fff;padding:13px;box-shadow:0 5px 10px rgba(78,45,64,.1)}.m-store__benefit h3{margin:0;color:#431755;font-size:15px;line-height:1.28}.m-store__benefit b{color:#e71f86;margin-right:4px}.m-store__benefit p{margin:6px 0 0;color:#66516e;font-size:13px;line-height:1.42}.m-store__footer{margin-top:25px;text-align:center;color:#66516e;font-size:12px;line-height:1.6}.m-store__footer strong{display:block;margin-top:7px;color:#e71f86;letter-spacing:.08em}
</style>

<section class="m-store" aria-labelledby="mobile-store-title">
  <?php if (session()->getFlashdata('error')): ?><div class="m-store__notice"><?php echo esc(session()->getFlashdata('error')); ?></div><?php endif; ?>
  <?php if (service('request')->getGet('cancelled')): ?><div class="m-store__status">Checkout was cancelled. Nothing was charged.</div><?php endif; ?>
  <div class="m-store__brand">EMERGENCE <em>2026</em></div>
  <span class="m-store__theme">ACCELERATE · BUILDING SUSTAINABLE SYSTEMS FOR EXECUTION</span>
  <span class="m-store__date">NOV 21, 2026</span>

  <p class="m-store__eyebrow">LIVE GROUP COACHING + BLUEPRINT</p>
  <h1 id="mobile-store-title">Execution Blueprint Coaching Session</h1>
  <p class="m-store__intro">Build your full 2027 plan in a live group session.</p>
  <div class="m-store__price"><span class="m-store__price-label">EMERGENCE CONFERENCE PRICE</span><div class="m-store__amount"><?php echo esc($price); ?></div><p class="m-store__full">Full price <strong>$29.00</strong></p><span class="m-store__day">ONE DAY ONLY · NOV 21</span></div>

  <?php if (empty($ready) || ! $product): ?>
    <div class="m-store__status">This offer is being prepared. Please check back shortly.</div>
  <?php elseif ($owned && $accessUrl !== ''): ?>
    <a class="m-store__reserve" href="<?php echo esc($accessUrl, 'attr'); ?>"<?php echo ($product['access_type'] ?? '') === 'external' ? ' target="_blank" rel="noopener"' : ''; ?>>ACCESS YOUR PURCHASE</a>
  <?php elseif ($owned): ?>
    <div class="m-store__status">Your place is reserved. Your access is already active.</div>
  <?php else: ?>
    <form method="post" action="<?php echo site_url('mobile/store/checkout/' . $productId); ?>"><?php echo csrf_field(); ?><button class="m-store__reserve" type="submit">RESERVE YOUR SEAT</button></form>
  <?php endif; ?>

  <section class="m-store__outcomes" aria-labelledby="mobile-store-outcomes"><h2 id="mobile-store-outcomes">WHAT YOU'LL WALK AWAY WITH</h2><div class="m-store__benefits">
    <article class="m-store__benefit"><h3><b>01</b>Live group coaching</h3><p>Build your 2027 plan live in a coach-led group.</p></article>
    <article class="m-store__benefit"><h3><b>02</b>Builds on your workbook</h3><p>Your baseline scores and gaps become the starting point.</p></article>
    <article class="m-store__benefit"><h3><b>03</b>Identity, vision &amp; skills</h3><p>Clarify who you're becoming and the skills to build.</p></article>
    <article class="m-store__benefit"><h3><b>04</b>Marketplace alignment</h3><p>Match your goal to real demand in your marketplace.</p></article>
    <article class="m-store__benefit"><h3><b>05</b>Your top goals for 2027</h3><p>Choose up to three goals and map each with SYSTEM.</p></article>
    <article class="m-store__benefit"><h3><b>06</b>A sequenced 90-day plan</h3><p>Milestones, 30-day priorities and weekly routines.</p></article>
    <article class="m-store__benefit"><h3><b>07</b>Your written Blueprint</h3><p>Your full plan, trackers and 7, 30 and 90-day reviews.</p></article>
    <article class="m-store__benefit"><h3><b>08</b>6 months of replays</h3><p>Rewatch every keynote, panel and track lab at your pace.</p></article>
  </div></section>
  <footer class="m-store__footer">Emergence 2026 · Hosted by EPR Global<br>Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by Crèyatif · © 2026 Powered By EPR Global.<strong>ATTEND → APPLY → EXECUTE</strong></footer>
</section>

<?= module_view('MobileApp', 'includes/footer') ?>
