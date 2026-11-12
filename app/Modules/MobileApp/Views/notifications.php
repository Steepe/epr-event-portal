<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => site_url('mobile/explore'), 'activeTab' => 'explore', 'title' => 'Notifications', 'screenLabel' => 'Notifications']) ?>
<?php
$notifications = $notifications ?? [];
$unreadCount   = count(array_filter($notifications, static fn (array $item): bool => $item['unread']));
$filters       = ['All', 'Unread', 'Sessions', 'Messages', 'Networking', 'Booths'];
$countFor      = static function (string $filter) use ($notifications, $unreadCount): int {
    if ($filter === 'All') {
        return count($notifications);
    }
    if ($filter === 'Unread') {
        return $unreadCount;
    }

    return count(array_filter($notifications, static fn (array $item): bool => $item['cat'] === $filter));
};
$groups = [];
foreach ($notifications as $item) {
    $groups[$item['group']][] = $item;
}
$preferences = [
    ['sessions', 'Session reminders', '15 minutes before saved sessions start', true],
    ['messages', 'Direct messages', 'When an attendee or speaker messages you', true],
    ['mentions', 'Mentions', 'When you are @mentioned in chat or posts', true],
    ['connections', 'Connections & bookings', 'Requests, acceptances and 1:1 confirmations', true],
    ['booths', 'Booth replies', 'When an exhibitor responds to you', false],
    ['digest', 'Email digest', 'A summary every evening at 6 PM', true],
];
?>

<div class="m-screen" data-notif-page>
    <div class="m-head">
        <span class="m-kicker">Accelerate 2026</span>
        <h1 class="m-title">Notifications</h1>
        <span class="m-sub" data-notif-summary><?= $unreadCount > 0 ? 'You have ' . $unreadCount . ' unread notification' . ($unreadCount === 1 ? '.' : 's.') : 'You’re all caught up.' ?></span>
        <button class="m-btn-ghost m-notifs__mark" type="button" data-notif-markall><i class="fa fa-check-square-o" aria-hidden="true"></i>Mark all as read</button>
    </div>

    <div class="m-chips m-chips--scroll">
        <?php foreach ($filters as $filter): ?>
            <button class="m-chip m-chip--count <?= $filter === 'All' ? 'is-active' : '' ?>" type="button" data-notif-filter="<?= $filter ?>"><?= $filter ?><span class="m-chip__count"><?= $countFor($filter) ?></span></button>
        <?php endforeach; ?>
    </div>

    <?php foreach (['TODAY', 'EARLIER'] as $groupName): ?>
        <?php if (empty($groups[$groupName])) { continue; } ?>
        <div class="m-ngroup" data-notif-group>
            <span class="m-label"><?= $groupName ?></span>
            <div class="m-card m-card--clip">
                <?php foreach ($groups[$groupName] as $item): ?>
                    <div class="m-nrow <?= $item['unread'] ? 'is-unread' : '' ?>" data-notif="<?= esc($item['id'], 'attr') ?>" data-cat="<?= esc($item['cat'], 'attr') ?>" data-unread="<?= $item['unread'] ? '1' : '0' ?>"<?= $item['connection_id'] ? ' data-connection="' . (int) $item['connection_id'] . '"' : '' ?>>
                        <span class="m-nicon m-nicon--<?= esc($item['cat'], 'attr') ?>"><i class="fa <?= esc($item['icon'], 'attr') ?>" aria-hidden="true"></i></span>
                        <div class="m-nrow__body">
                            <div class="m-nrow__title"><b><?= esc($item['title']) ?></b></div>
                            <p><?= esc($item['body']) ?></p>
                            <div class="m-nrow__foot">
                                <span class="m-nrow__time"><?= esc($item['time']) ?></span>
                                <?php if ($item['connection_id']): ?>
                                    <span class="m-nrow__conn"><button type="button" data-conn-action="accept">Accept</button><button type="button" data-conn-action="decline">Decline</button></span>
                                <?php else: ?>
                                    <button class="m-linkbtn" type="button" data-notif-toggle><?= $item['unread'] ? 'Mark read' : 'Mark unread' ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="m-nempty" data-notif-empty<?= $notifications ? ' hidden' : '' ?>>
        <i class="fa fa-bell-slash-o" aria-hidden="true"></i><b>You’re all caught up</b><span>Nothing here for this filter.</span>
    </div>

    <div class="m-ngroup" style="margin-top:6px">
        <span class="m-label">NOTIFY ME ABOUT</span>
        <div class="m-card m-card--clip">
            <?php foreach ($preferences as [$key, $label, $description, $default]): ?>
                <button class="m-pref <?= $default ? 'is-on' : '' ?>" type="button" data-pref="<?= $key ?>" aria-pressed="<?= $default ? 'true' : 'false' ?>">
                    <span class="m-pref__copy"><b><?= esc($label) ?></b><small><?= esc($description) ?></small></span>
                    <span class="m-switch"></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= module_view('MobileApp', 'includes/footer') ?>
