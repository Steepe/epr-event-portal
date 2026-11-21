<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$product = $products[0] ?? null;
$productId = (int) ($product['product_id'] ?? 0);
$owned = $productId > 0 && in_array($productId, $entitledProductIds ?? [], true);
$currency = strtoupper((string) ($product['currency'] ?? 'USD'));
$pricePrefix = $currency === 'USD' ? '$' : $currency . ' ';
$price = $product ? $pricePrefix . number_format((float) $product['price_amount'], 2) : '$19.99';
$accessUrl = '';
if ($owned && ($product['access_type'] ?? '') === 'replays') {
    $accessUrl = site_url('attendees/store/replays/' . $productId);
} elseif ($owned && ($product['access_type'] ?? '') === 'external' && ! empty($product['access_url'])) {
    $accessUrl = (string) $product['access_url'];
}
?>
<style>
  .execution-store{width:min(1420px,calc(100% - 40px));margin:118px auto 58px;color:#451a5d;font-family:Poppins,Arial,sans-serif}
  .execution-offer{position:relative;overflow:hidden;background:#fcf9fd;border-top:9px solid #e71f86;box-shadow:0 20px 60px rgba(47,14,62,.2);padding:44px 5.75% 36px}
  .execution-head{display:flex;align-items:flex-start;justify-content:space-between;gap:24px}.execution-brand{letter-spacing:.2em;font-size:23px;font-weight:800;color:#712b88;line-height:1}.execution-brand em{font-style:normal;color:#e71f86}.execution-theme{display:block;margin-top:12px;font-size:16px;letter-spacing:.11em;font-weight:500;color:#61526b}.execution-head-right{display:flex;align-items:center;gap:28px}.execution-logo{display:block;width:178px;height:auto}.execution-date{white-space:nowrap;border:0;border-radius:7px;background:#742787;color:#fff;padding:12px 24px;font-size:15px;font-weight:800;box-shadow:0 4px 8px rgba(57,15,69,.22)}
  .execution-main{display:grid;grid-template-columns:minmax(330px,.78fr) minmax(550px,1.22fr);gap:44px;align-items:center;margin-top:54px}.execution-kicker{margin:0 0 12px;color:#e71f86;font-size:19px;font-weight:800;letter-spacing:.16em}.execution-title{max-width:480px;margin:0;color:#421754;font-size:48px;line-height:1.06;letter-spacing:-.035em;font-weight:800}.execution-intro{max-width:420px;margin:18px 0 22px;color:#66516e;font-size:19px;line-height:1.45}.execution-price-card{max-width:470px;box-sizing:border-box;border-radius:23px;background:#40144e;padding:28px 34px;color:#fff;box-shadow:0 18px 28px rgba(48,11,59,.25)}.execution-price-label{display:block;color:#f6d5e8;font-size:16px;font-weight:700;letter-spacing:.11em}.execution-price{margin:4px 0 0;color:#f5b736;font-size:88px;font-weight:800;line-height:1;letter-spacing:-.07em}.execution-full-price{margin:10px 0 0;font-size:17px;font-weight:600}.execution-full-price strong{color:#fff;font-size:21px}.execution-day{display:inline-block;margin-top:13px;border-radius:20px;background:#ea1d86;padding:7px 16px;color:#fff;font-size:15px;font-weight:800;letter-spacing:.055em}.execution-purchase{max-width:470px;margin-top:18px}.execution-reserve{display:flex;align-items:center;justify-content:center;width:100%;min-height:58px;box-sizing:border-box;border:2px solid #e71f86;border-radius:12px;background:#fff;color:#e71f86;font:800 19px Poppins,Arial,sans-serif;letter-spacing:.12em;text-decoration:none;cursor:pointer;transition:background .18s,color .18s,transform .18s}.execution-reserve:hover,.execution-reserve:focus{background:#e71f86;color:#fff;transform:translateY(-1px);text-decoration:none}.execution-status{max-width:470px;margin-top:18px;border:1px solid #e5a72e;border-radius:12px;background:#fff7e7;padding:15px;color:#6a4b16;font-size:14px;line-height:1.5}
  .execution-outcomes{border:2px solid #e8aa31;border-radius:32px;background:#fff8ec;padding:24px 28px 30px}.execution-outcomes h2{margin:0 0 17px;color:#702887;font-size:27px;font-weight:800;letter-spacing:.035em}.execution-benefits{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px 16px}.execution-benefit{min-height:92px;box-sizing:border-box;border-radius:15px;background:#fff;padding:15px 18px;box-shadow:0 7px 12px rgba(78,45,64,.12)}.execution-benefit h3{margin:0;color:#431755;font-size:17px;line-height:1.22;font-weight:800}.execution-number{color:#e71f86;margin-right:5px}.execution-benefit p{margin:7px 0 0;color:#66516e;font-size:14px;line-height:1.38}.execution-footer{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-top:32px;color:#66516e;font-size:15px}.execution-footer strong{color:#e71f86;font-size:17px;letter-spacing:.105em}.execution-alert{margin:20px 0;border-radius:12px;padding:13px 16px;background:#fff0f8;border:1px solid #e71f86;color:#7e1b5f;font-size:14px}.execution-alert.note{background:#fff7e7;border-color:#e5a72e;color:#6a4b16}
  @media(max-width:1080px){.execution-main{grid-template-columns:1fr;gap:34px}.execution-title,.execution-intro,.execution-price-card,.execution-purchase,.execution-status{max-width:none}.execution-outcomes{max-width:none}}
  @media(max-width:720px){.execution-store{width:100%;margin:86px 0 0}.execution-offer{padding:30px 20px 26px}.execution-head{display:block}.execution-brand{font-size:18px}.execution-theme{font-size:11px;letter-spacing:.08em;line-height:1.45}.execution-head-right{justify-content:space-between;margin-top:23px;gap:14px}.execution-logo{width:130px}.execution-date{padding:10px 12px;font-size:12px}.execution-main{margin-top:38px}.execution-kicker{font-size:14px}.execution-title{font-size:38px}.execution-intro{font-size:16px}.execution-price-card{padding:23px}.execution-price{font-size:68px}.execution-outcomes{padding:21px 15px;border-radius:24px}.execution-outcomes h2{font-size:21px}.execution-benefits{grid-template-columns:1fr;gap:11px}.execution-benefit{min-height:0;padding:14px}.execution-benefit h3{font-size:16px}.execution-footer{display:block;margin-top:26px;font-size:13px}.execution-footer strong{display:block;margin-top:12px;font-size:13px}.execution-reserve{font-size:16px;letter-spacing:.09em}}
</style>

<main class="execution-store">
  <?php if (session()->getFlashdata('error')): ?><div class="execution-alert"><?php echo esc(session()->getFlashdata('error')); ?></div><?php endif; ?>
  <?php if (service('request')->getGet('cancelled')): ?><div class="execution-alert note">Checkout was cancelled. Nothing was charged.</div><?php endif; ?>

  <section class="execution-offer" aria-labelledby="execution-title">
    <header class="execution-head">
      <div>
        <div class="execution-brand">EMERGENCE <em>2026</em></div>
        <span class="execution-theme">ACCELERATE · BUILDING SUSTAINABLE SYSTEMS FOR EXECUTION</span>
      </div>
      <div class="execution-head-right">
        <img class="execution-logo" src="<?php echo asset_url('images/events-logo-purple.png'); ?>" alt="EPR Global">
        <span class="execution-date">NOV 21, 2026</span>
      </div>
    </header>

    <div class="execution-main">
      <section>
        <p class="execution-kicker">LIVE GROUP COACHING + BLUEPRINT</p>
        <h1 id="execution-title" class="execution-title">Execution Blueprint Coaching Session</h1>
        <p class="execution-intro">Build your full 2027 plan in a live group session.</p>
        <div class="execution-price-card">
          <span class="execution-price-label">EMERGENCE CONFERENCE PRICE</span>
          <div class="execution-price"><?php echo esc($price); ?></div>
          <p class="execution-full-price">Full price <strong>$29.00</strong></p>
          <span class="execution-day">ONE DAY ONLY · NOV 21</span>
        </div>

        <?php if (empty($ready) || ! $product): ?>
          <div class="execution-status">This offer is being prepared. Please check back shortly.</div>
        <?php elseif ($owned && $accessUrl !== ''): ?>
          <a class="execution-reserve" href="<?php echo esc($accessUrl, 'attr'); ?>"<?php echo ($product['access_type'] ?? '') === 'external' ? ' target="_blank" rel="noopener"' : ''; ?>>ACCESS YOUR PURCHASE</a>
        <?php elseif ($owned): ?>
          <div class="execution-status">Your place is reserved. Your access is already active.</div>
        <?php else: ?>
          <form class="execution-purchase" method="post" action="<?php echo site_url('attendees/store/checkout/' . $productId); ?>">
            <?php echo csrf_field(); ?>
            <button class="execution-reserve" type="submit">RESERVE YOUR SEAT</button>
          </form>
        <?php endif; ?>
      </section>

      <section class="execution-outcomes" aria-labelledby="execution-outcomes-title">
        <h2 id="execution-outcomes-title">WHAT YOU'LL WALK AWAY WITH</h2>
        <div class="execution-benefits">
          <article class="execution-benefit"><h3><span class="execution-number">01</span>Live group coaching</h3><p>Build your 2027 plan live in a coach-led group.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">02</span>Builds on your workbook</h3><p>Your baseline scores and gaps become the starting point.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">03</span>Identity, vision &amp; skills</h3><p>Clarify who you're becoming and the skills to build.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">04</span>Marketplace alignment</h3><p>Match your goal to real demand in your marketplace.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">05</span>Your top goals for 2027</h3><p>Choose up to three goals and map each with SYSTEM.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">06</span>A sequenced 90-day plan</h3><p>Milestones, 30-day priorities and weekly routines.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">07</span>Your written Blueprint</h3><p>Your full plan, trackers and 7, 30 and 90-day reviews.</p></article>
          <article class="execution-benefit"><h3><span class="execution-number">08</span>6 months of replays</h3><p>Rewatch every keynote, panel and track lab at your pace.</p></article>
        </div>
      </section>
    </div>

    <footer class="execution-footer"><span>Emergence 2026 · Hosted by EPR Global</span><strong>ATTEND → APPLY → EXECUTE</strong></footer>
  </section>
</main>
<?php echo module_view('Web', 'includes/scripts'); ?>
</body></html>
