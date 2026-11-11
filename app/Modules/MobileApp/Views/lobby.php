<?php echo module_view('MobileApp', 'includes/header_home'); ?>

<?php $agendaUrl = $conferenceId ? site_url('mobile/agenda/' . $conferenceId) : site_url('mobile/home'); ?>

<style>
    body.epr-mobile-auth { padding:0 !important; }
    .mobile-lobby { position:relative; display:flex; min-height:100svh; flex-direction:column; box-sizing:border-box; overflow:hidden; padding:18px 20px 14px; color:#fff; background:radial-gradient(circle at 15% 12%,rgba(239,177,30,.16),transparent 30%),radial-gradient(circle at 88% 22%,rgba(216,25,142,.22),transparent 34%),radial-gradient(ellipse 90% 80% at 45% 55%,#62365c 0%,#422846 48%,#261528 100%); }
    .mobile-lobby::before { position:absolute; inset:0; content:''; opacity:.52; pointer-events:none; background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px); background-size:42px 42px; mask-image:linear-gradient(to bottom,rgba(0,0,0,.75),transparent 76%); }
    .mobile-lobby__head,.mobile-lobby__map,.mobile-lobby__foot { position:relative; z-index:1; }
    .mobile-lobby__head { display:flex; flex-direction:column; gap:10px; }
    .mobile-lobby__eyebrow { color:#efb11e; font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:600; letter-spacing:.22em; text-transform:uppercase; }
    .mobile-lobby__logo { display:block; width:190px; height:auto; }
    .mobile-lobby__map { min-height:380px; flex:1; }
    .mobile-lobby__links { position:absolute; inset:0; width:100%; height:100%; overflow:visible; }
    .mobile-lobby__links line { stroke:rgba(239,177,30,.32); stroke-dasharray:2 5; stroke-width:1; vector-effect:non-scaling-stroke; }
    .mobile-lobby__node { position:absolute; display:flex; align-items:center; justify-content:center; border:1px solid rgba(255,255,255,.28); border-radius:50%; box-shadow:0 0 0 10px var(--halo),0 0 34px var(--glow); color:#fff; font-size:12px; font-weight:600; letter-spacing:.04em; line-height:1.3; text-align:center; text-decoration:none; transform:translate(-50%,-50%); transition:transform .2s ease; }
    .mobile-lobby__node:active { transform:translate(-50%,-50%) scale(.94); }
    .mobile-lobby__node--network { top:18%; left:30%; width:104px; height:104px; background:radial-gradient(circle at top left,#8b4ac0,#693b9c); --halo:rgba(139,74,192,.12); --glow:rgba(139,74,192,.45); }
    .mobile-lobby__node--sponsors { top:28%; left:74%; width:104px; height:104px; background:radial-gradient(circle at top left,#a82ad2,#7200a4); --halo:rgba(168,42,210,.12); --glow:rgba(168,42,210,.45); }
    .mobile-lobby__node--exhibitors { top:50%; left:22%; width:104px; height:104px; background:radial-gradient(circle at top left,#d61be3,#9b0eaa); --halo:rgba(214,27,227,.12); --glow:rgba(214,27,227,.45); }
    .mobile-lobby__node--booth { top:60%; left:78%; width:104px; height:104px; background:radial-gradient(circle at top left,#e988a2,#c95c7c); --halo:rgba(233,136,162,.12); --glow:rgba(233,136,162,.45); }
    .mobile-lobby__node--agenda { top:80%; left:50%; width:124px; height:124px; font-size:13px; background:radial-gradient(circle at top left,#f1a91e,#d68500); --halo:rgba(241,169,30,.12); --glow:rgba(241,169,30,.45); }
    .mobile-lobby__node span { display:block; }
    .mobile-lobby__foot { color:rgba(255,255,255,.6); font-size:11px; text-align:center; }
    .mobile-lobby__foot b { color:#d8198e; font-weight:600; }
</style>

<main class="mobile-lobby">
    <header class="mobile-lobby__head">
        <span class="mobile-lobby__eyebrow">Lobby · Emergence 2026</span>
        <img class="mobile-lobby__logo" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate">
    </header>
    <section class="mobile-lobby__map" aria-label="Conference areas">
        <svg class="mobile-lobby__links" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
            <line x1="50" y1="80" x2="30" y2="18"></line><line x1="50" y1="80" x2="22" y2="50"></line>
            <line x1="30" y1="18" x2="74" y2="28"></line><line x1="30" y1="18" x2="22" y2="50"></line>
            <line x1="22" y1="50" x2="74" y2="28"></line><line x1="22" y1="50" x2="78" y2="60"></line><line x1="74" y1="28" x2="78" y2="60"></line>
        </svg>
        <a class="mobile-lobby__node mobile-lobby__node--network" href="<?= site_url('mobile/networking-center') ?>"><span>NETWORKING<br>CENTER</span></a>
        <a class="mobile-lobby__node mobile-lobby__node--sponsors" href="<?= site_url('mobile/sponsors') ?>"><span>SPONSORS</span></a>
        <a class="mobile-lobby__node mobile-lobby__node--exhibitors" href="<?= site_url('mobile/exhibitors') ?>"><span>EXHIBITORS</span></a>
        <a class="mobile-lobby__node mobile-lobby__node--booth" href="<?= site_url('mobile/emergence-booth') ?>"><span>EMERGENCE<br>BOOTH</span></a>
        <a class="mobile-lobby__node mobile-lobby__node--agenda" href="<?= $agendaUrl ?>"><span>LIVE<br>CONFERENCE</span></a>
    </section>
    <footer class="mobile-lobby__foot">Crafted with <i class="fa fa-heart" aria-hidden="true"></i> by <b>Crèyatif</b> · © <?= date('Y') ?> Powered By EPR Global.</footer>
</main>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
