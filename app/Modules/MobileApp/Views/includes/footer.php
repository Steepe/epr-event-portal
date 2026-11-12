<?php
$shell     = $shell ?? 'tabbed';
$activeTab = $activeTab ?? '';

$mobileAgendaId = session('live-conference-id');
$mobileTabs     = [
    ['lobby', 'Lobby', 'fa-th-large', site_url('mobile/lobby')],
    ['agenda', 'Agenda', 'fa-calendar', $mobileAgendaId ? site_url('mobile/agenda/' . $mobileAgendaId) : site_url('mobile/home')],
    ['network', 'Network', 'fa-users', site_url('mobile/networking-center')],
    ['booth', 'Booth', 'fa-building', site_url('mobile/emergence-booth')],
    ['explore', 'Explore', 'fa-compass', site_url('mobile/explore')],
];
$mobileNotifs = $shell === 'bare' ? [] : \App\Modules\MobileApp\Libraries\MobileNotifications::all();
$mobileUnread = count(array_filter($mobileNotifs, static fn (array $item): bool => $item['unread']));
?>
    </main>
<?php if ($shell === 'tabbed'): ?>
    <nav class="m-tabs" aria-label="Primary navigation">
        <?php foreach ($mobileTabs as [$key, $label, $icon, $url]): ?>
            <a class="<?= $activeTab === $key ? 'is-active' : '' ?>" href="<?= $url ?>"<?= $activeTab === $key ? ' aria-current="page"' : '' ?>><i class="fa <?= $icon ?>" aria-hidden="true"></i><span><?= $label ?></span></a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
</div>

<div class="m-scrim" id="mScrim" hidden></div>

<?php if ($shell !== 'bare'): ?>
<aside class="m-sheet" data-sheet="notif" role="dialog" aria-modal="true" aria-label="Notifications" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-sheetnotif__head">
        <div><b>Notifications</b><span class="m-pillcount" data-notif-pill><?= $mobileUnread ? $mobileUnread . ' NEW' : 'ALL READ' ?></span></div>
        <button class="m-linkbtn" type="button" data-notif-markall>Mark all read</button>
    </div>
    <?php foreach ($mobileNotifs as $index => $item): ?>
        <?php // Only the latest three are shown; the rest stay in the page so the unread badge counts them. ?>
        <a class="m-snrow <?= $item['unread'] ? 'is-unread' : '' ?>" href="<?= site_url('mobile/notifications') ?>" data-notif="<?= esc($item['id'], 'attr') ?>" data-cat="<?= esc($item['cat'], 'attr') ?>" data-unread="<?= $item['unread'] ? '1' : '0' ?>"<?= $index > 2 ? ' hidden' : '' ?>>
            <span class="m-nicon m-nicon--<?= esc($item['cat'], 'attr') ?>"><i class="fa <?= esc($item['icon'], 'attr') ?>" aria-hidden="true"></i></span>
            <span class="m-snrow__body">
                <b><?= esc($item['title']) ?></b>
                <span><?= esc($item['body']) ?></span>
                <small><?= esc($item['time']) ?></small>
            </span>
            <span class="m-snrow__dot"></span>
        </a>
    <?php endforeach; ?>
    <?php if (! $mobileNotifs): ?>
        <div class="m-sheetnotif__none">You’re all caught up.</div>
    <?php endif; ?>
    <a class="m-sheetnotif__all" href="<?= site_url('mobile/notifications') ?>">View all notifications</a>
</aside>
<?php endif; ?>

<div class="m-toast" id="mToast" role="status" aria-live="polite" hidden></div>
<script src="<?= mobile_asset_url('js/mobile-app.js') ?>?v=<?= @filemtime(FCPATH . 'assets/js/mobile-app.js') ?: 1 ?>"></script>
</body>
</html>
