<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/profile'), 'activeTab' => 'me', 'title' => 'Envision']) ?>

<div class="m-screen">
    <div class="m-head">
        <a class="m-backlink" href="<?= site_url('mobile/profile') ?>" data-back="<?= site_url('mobile/profile') ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i>BACK</a>
        <span class="m-kicker">Accelerate Practice</span>
        <h1 class="m-title m-title--28">Vision Birthing Exercise</h1>
        <span class="m-sub">A guided video exercise for clarifying execution goals.</span>
    </div>

    <section class="m-card m-envision">
        <div class="m-envision__frame">
            <iframe src="https://player.vimeo.com/video/771780852?h=2ad1e8b0f7&amp;color=eebf34&amp;title=0&amp;byline=0&amp;portrait=0" title="Vision Birthing Exercise" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        </div>
    </section>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
