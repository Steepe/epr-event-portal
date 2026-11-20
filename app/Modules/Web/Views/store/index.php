<?php echo module_view('Web', 'includes/header'); ?>
<?php echo module_view('Web', 'includes/topbar'); ?>
<style>
  .store-wrap{width:min(1180px,calc(100% - 48px));margin:126px auto 64px;color:#fff}.store-hero{padding:38px;border:1px solid rgba(255,255,255,.16);border-radius:22px;background:linear-gradient(130deg,rgba(73,6,70,.92),rgba(30,9,43,.9));box-shadow:0 30px 80px rgba(0,0,0,.25)}.store-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;margin-top:22px}.store-card{display:flex;flex-direction:column;gap:16px;padding:24px;border:1px solid rgba(255,255,255,.15);border-radius:18px;background:rgba(16,7,28,.82)}.store-card h2{margin:0;font-size:22px}.store-card p{margin:0;color:rgba(255,255,255,.72);line-height:1.55}.store-price{font-size:28px;font-weight:700;color:#efb11e}.store-input{width:100%;box-sizing:border-box;border:1px solid rgba(255,255,255,.22);border-radius:9px;background:rgba(0,0,0,.22);color:#fff;padding:11px}.store-btn{width:100%;border:0;border-radius:9px;background:linear-gradient(90deg,#7d0686,#bd159e);padding:13px;color:#fff;font-weight:700;cursor:pointer;text-decoration:none;text-align:center;box-sizing:border-box}.store-note{padding:12px 15px;border-radius:10px;margin:18px 0;background:rgba(239,177,30,.12);color:#ffe0a1}.store-alert{padding:12px 15px;border-radius:10px;margin:18px 0;background:rgba(217,25,142,.2);color:#fff}
</style>
<main class="store-wrap">
  <section class="store-hero"><span class="rc-kicker">EPR GLOBAL</span><h1 style="margin:8px 0;font-size:clamp(32px,5vw,54px)">Emergence Store</h1><p style="max-width:720px;margin:0;color:rgba(255,255,255,.76);font-size:17px;line-height:1.6">Continue the conversation after the conference. Purchase replay access, register interest in upcoming coaching, and explore EPR digital products.</p></section>
  <?php if (session()->getFlashdata('error')): ?><div class="store-alert"><?php echo esc(session()->getFlashdata('error')); ?></div><?php endif; ?>
  <?php if (service('request')->getGet('cancelled')): ?><div class="store-note">Checkout was cancelled. Nothing was charged.</div><?php endif; ?>
  <?php if (empty($ready)): ?><div class="store-note">The Emergence Store is being prepared. Please check back shortly.</div><?php else: ?>
    <section class="store-grid">
      <?php foreach ($products as $product): $owned = in_array((int) $product['product_id'], $entitledProductIds ?? [], true); $isFree = (float) $product['price_amount'] <= 0; ?>
      <article class="store-card"><div><span class="rc-kicker" style="font-size:10px;color:#efb11e"><?php echo esc(strtoupper(str_replace('_', ' ', $product['access_type']))); ?></span><h2><?php echo esc($product['name']); ?></h2></div><p><?php echo nl2br(esc($product['description'] ?: 'More details will be announced soon.')); ?></p><div class="store-price"><?php echo $isFree ? 'Free' : esc($product['currency']) . ' ' . number_format((float) $product['price_amount'], 2); ?></div>
        <?php if ($owned && $product['access_type'] === 'replays'): ?><a class="store-btn" href="<?php echo site_url('attendees/store/replays/' . (int) $product['product_id']); ?>">Watch replays</a>
        <?php elseif ($owned && $product['access_type'] === 'external' && ! empty($product['access_url'])): ?><a class="store-btn" href="<?php echo esc($product['access_url'], 'attr'); ?>" target="_blank" rel="noopener">Open product</a>
        <?php elseif ($owned): ?><div class="store-note" style="margin:0">You are registered for this.</div>
        <?php else: ?><form method="post" action="<?php echo site_url('attendees/store/checkout/' . (int) $product['product_id']); ?>"><?php echo csrf_field(); ?><?php if (! $isFree): ?><label style="font-size:12px;color:rgba(255,255,255,.7)">Discount code (optional)<input class="store-input" name="coupon_code" maxlength="80" placeholder="Enter code"></label><?php endif; ?><button class="store-btn" style="margin-top:12px" type="submit"><?php echo esc($product['button_label'] ?: ($isFree ? 'Register interest' : 'Buy now')); ?></button></form><?php endif; ?>
      </article>
      <?php endforeach; ?>
      <?php if (!$products): ?><div class="store-note" style="grid-column:1/-1">Products will be added soon.</div><?php endif; ?>
    </section>
  <?php endif; ?>
</main>
<?php echo module_view('Web', 'includes/scripts'); ?>
</body></html>
