<?php echo module_view('Web', 'includes/header'); ?>
<?php echo module_view('Web', 'includes/topbar'); ?>
<main style="width:min(680px,calc(100% - 40px));margin:170px auto;color:#fff;text-align:center">
    <section class="rc-panel" style="padding:42px">
        <i class="fa <?php echo $hasAccess ? 'fa-check-circle' : 'fa-clock-o'; ?>" style="font-size:52px;color:#efb11e" aria-hidden="true"></i>
        <h1 style="font-size:34px;margin:18px 0 10px"><?php echo $hasAccess ? 'Thank you' : 'Payment is being confirmed'; ?></h1>
        <?php if ($hasAccess): ?>
            <p style="color:rgba(255,255,255,.75);line-height:1.6">Your access to <strong><?php echo esc($product['name'] ?? 'this product'); ?></strong> is ready.</p>
            <?php if (! empty($product['access_url'])): ?>
                <a class="rc-btn" target="_blank" rel="noopener" href="<?php echo esc($product['access_url'], 'attr'); ?>">Open product</a>
            <?php endif; ?>
        <?php else: ?>
            <p style="color:rgba(255,255,255,.75);line-height:1.6">Stripe is confirming your payment.<?php echo ! empty($isReplay) ? ' You will return to the sessions list when replay access is ready.' : ' Refresh this page in a moment to check your access.'; ?></p>
            <?php if (! empty($isReplay)): ?><p><a href="<?php echo esc(current_url() . '?' . service('request')->getUri()->getQuery(), 'attr'); ?>" style="color:#efb11e">Check again</a></p><?php endif; ?>
        <?php endif; ?>
        <?php if (empty($isReplay)): ?><p><a href="<?php echo site_url('attendees/store'); ?>" style="color:#efb11e">Return to store</a></p><?php endif; ?>
    </section>
</main>
<?php if (! $hasAccess && ! empty($isReplay)): ?>
<script>
(() => {
    const url = new URL(window.location.href);
    const checks = Number(url.searchParams.get('confirmation_check') || 0);
    if (checks < 10) {
        window.setTimeout(() => {
            url.searchParams.set('confirmation_check', String(checks + 1));
            window.location.replace(url.toString());
        }, 3000);
    }
})();
</script>
<?php endif; ?>
<?php echo module_view('Web', 'includes/scripts'); ?>
</body></html>
