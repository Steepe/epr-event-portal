<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/store'), 'activeTab' => 'explore', 'title' => 'Your replays', 'screenLabel' => 'Store']) ?>
<div class="m-screen">
  <div class="m-head"><span class="m-kicker">YOUR PURCHASE</span><h1 class="m-title"><?= esc($product['name']) ?></h1><span class="m-sub">Watch on demand at your own pace.</span></div>
  <?php foreach ($videos as $video): ?>
    <article class="m-card m-card--clip"><iframe src="<?= esc($video['bunny_embed_url'], 'attr') ?>" title="<?= esc($video['title'], 'attr') ?>" allow="autoplay;fullscreen;picture-in-picture" allowfullscreen style="display:block;width:100%;aspect-ratio:16/9;border:0"></iframe><h2 style="margin:0;padding:15px;font-size:16px"><?= esc($video['title']) ?></h2></article>
  <?php endforeach; ?>
  <?php if (! $videos): ?><div class="m-empty">Your replay videos will be added here soon.</div><?php endif; ?>
</div>
<?= module_view('MobileApp', 'includes/footer') ?>
