<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/explore'), 'activeTab' => 'explore', 'title' => 'Exhibitors', 'screenLabel' => 'Exhibitors']) ?>
<?php $exhibitors = $exhibitors ?? []; $fallbackLogo = mobile_asset_url('images/eventslogo.png'); ?>

<div class="m-screen">
    <div class="m-head">
        <span class="m-kicker">Accelerate 2026</span>
        <h1 class="m-title">Exhibitors</h1>
        <span class="m-sub" style="line-height:normal"><?= count($exhibitors) ?> <?= count($exhibitors) === 1 ? 'booth' : 'booths' ?> open now</span>
    </div>

    <?php foreach ($exhibitors as $ex): ?>
        <?php
        $logoFile = basename((string) ($ex['logo'] ?? ''));
        $logo     = $fallbackLogo;
        if ($logoFile !== '') {
            // The desktop portal stores booth logos in exhibitors_logos; older uploads live in exhibitors.
            $logo = file_exists(FCPATH . 'uploads/exhibitors_logos/' . $logoFile)
                ? '/uploads/exhibitors_logos/' . rawurlencode($logoFile)
                : '/uploads/exhibitors/' . rawurlencode($logoFile);
        }
        $tagline = trim((string) ($ex['tagline'] ?? '')) ?: 'Explore their virtual booth';
        $vimeo   = preg_replace('/\D/', '', (string) ($ex['vimeo_id'] ?? ''));
        $website = trim((string) ($ex['website'] ?? ''));
        $promo   = !empty($ex['has_promotion']) ? trim((string) ($ex['promotion_text'] ?? '')) : '';
        ?>
        <article class="m-card m-card--20 m-exhibitor" data-booth
                 data-id="<?= (int) $ex['id'] ?>" data-name="<?= esc($ex['company_name'], 'attr') ?>" data-logo="<?= esc($logo, 'attr') ?>"
                 data-video="<?= $vimeo !== '' ? 'https://player.vimeo.com/video/' . $vimeo : '' ?>"
                 data-contact="<?= esc((string) ($ex['contact_person'] ?? ''), 'attr') ?>" data-email="<?= esc((string) ($ex['email'] ?? ''), 'attr') ?>"
                 data-phone="<?= esc((string) ($ex['telephone'] ?? ''), 'attr') ?>" data-website="<?= esc($website, 'attr') ?>"
                 data-summary="<?= esc((string) ($ex['profile_summary'] ?? ''), 'attr') ?>" data-promo="<?= esc($promo, 'attr') ?>">
            <button class="m-exhibitor__visual" type="button" data-booth-open aria-label="Open <?= esc($ex['company_name'], 'attr') ?> booth">
                <img src="<?= esc($logo, 'attr') ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='<?= $fallbackLogo ?>'">
                <span class="m-exhibitor__bar"><?= esc($ex['company_name']) ?></span>
                <?php if ($vimeo !== ''): ?><span class="m-video__tag m-video__tag--bl" style="padding:6px 10px"><i class="fa fa-play" aria-hidden="true"></i>BOOTH VIDEO</span><?php endif; ?>
            </button>
            <div class="m-exhibitor__info">
                <div class="m-exhibitor__copy"><b><?= esc($ex['company_name']) ?></b><span><?= esc($tagline) ?></span></div>
                <button class="m-exhibitor__enter" type="button" data-booth-open>Enter Booth</button>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (! $exhibitors): ?>
        <div class="m-empty">No exhibitor booths are open right now.</div>
    <?php endif; ?>
</div>

<aside class="m-sheet" data-sheet="booth" role="dialog" aria-modal="true" aria-label="Exhibitor booth" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-booth">
        <div class="m-booth__visual" id="boothVisual" data-video-title="Booth video">
            <img id="boothLogo" src="<?= $fallbackLogo ?>" alt="" data-video-hide>
            <button class="m-play m-play--56" type="button" id="boothPlay" data-video-hide aria-label="Play booth video"><i class="fa fa-play" aria-hidden="true"></i></button>
        </div>
        <span class="m-booth__name" id="boothName"></span>
        <div class="m-booth__contact">
            <img src="<?= mobile_asset_url('images/user.png') ?>" alt="">
            <div><b id="boothContact"></b><span id="boothEmail"></span><span id="boothPhone"></span><a id="boothWebsite" target="_blank" rel="noopener"></a></div>
        </div>
        <p class="m-booth__text" id="boothSummary" hidden></p>
        <p class="m-locknote" id="boothPromo" hidden></p>
        <form class="m-stack" id="boothForm" style="gap:16px">
            <input class="m-input m-input--44" name="subject" placeholder="Enter Subject" maxlength="150" required>
            <textarea class="m-input" name="message" rows="4" placeholder="Enter message here..." maxlength="2000" required></textarea>
            <button class="m-btn m-btn--plain" type="submit">Send</button>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var q = function (id) { return document.getElementById(id); };
    var sheet = document.querySelector('.m-sheet[data-sheet="booth"]');
    var visual = q('boothVisual'), form = q('boothForm');
    var current = null;

    function setText(id, value) { var el = q(id); el.textContent = value || ''; el.hidden = !value; return el; }
    function resetVideo() {
        var frame = visual.querySelector('iframe'); if (frame) frame.remove();
        q('boothLogo').hidden = false;
        q('boothPlay').hidden = !(current && current.dataset.video);
    }

    document.addEventListener('click', function (event) {
        var opener = event.target.closest('[data-booth-open]');
        if (!opener) return;
        current = opener.closest('[data-booth]');
        var d = current.dataset;
        q('boothLogo').src = d.logo;
        q('boothName').textContent = d.name;
        setText('boothContact', d.contact || d.name);
        setText('boothEmail', d.email);
        setText('boothPhone', d.phone);
        var site = setText('boothWebsite', d.website);
        if (d.website) site.href = /^https?:\/\//i.test(d.website) ? d.website : 'https://' + d.website;
        setText('boothSummary', d.summary);
        setText('boothPromo', d.promo);
        form.reset();
        resetVideo();
        EPR.openSheet('booth');
    });

    q('boothPlay').addEventListener('click', function () { if (current) EPR.playInto(visual, current.dataset.video); });
    sheet.addEventListener('sheet:close', resetVideo);

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var subject = form.subject.value.trim(), message = form.message.value.trim();
        if (!subject || !message) { EPR.toast('Please add a subject and message.', true); return; }
        var button = form.querySelector('button'); button.disabled = true;
        var name = current.dataset.name;
        EPR.post(<?= json_encode(site_url('mobile/exhibitors/send-message')) ?>, { exhibitor_id: current.dataset.id, message: subject + '\n\n' + message }, true).then(function (res) {
            if (!res.ok || !res.body || res.body.status !== 'success') throw new Error();
            EPR.closeSheet();
            EPR.toast('Message sent to ' + name + '.');
        }).catch(function () {
            EPR.toast('Unable to send your message. Please try again.', true);
        }).then(function () { button.disabled = false; });
    });
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
