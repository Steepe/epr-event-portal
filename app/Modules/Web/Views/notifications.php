<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$pendingConnections = $pendingConnections ?? [];
$conferenceId = (int) (session('live-conference-id') ?: session('conference_id'));
$agendaUrl = $conferenceId > 0 ? base_url('attendees/agenda/' . $conferenceId) : base_url('attendees/lobby');
$notifications = [
    ['id' => 'n1', 'cat' => 'Sessions', 'group' => 'TODAY', 'unread' => true, 'title' => 'Live now: Building Systems That Outlast Motivation', 'body' => 'Kemi Bello is on stage. 1,284 people are watching.', 'time' => '2 MIN AGO', 'icon' => 'fa-play-circle', 'url' => base_url('attendees/sessions/1')],
    ['id' => 'n2', 'cat' => 'Messages', 'group' => 'TODAY', 'unread' => true, 'title' => 'Grace Otieno sent you a message', 'body' => '“Happy to share our weekly ops dashboard after the session.”', 'time' => '8 MIN AGO', 'icon' => 'fa-comment', 'url' => base_url('attendees/attendees')],
    ['id' => 'n3', 'cat' => 'Messages', 'group' => 'TODAY', 'unread' => true, 'title' => 'Femi Adebayo mentioned you in session chat', 'body' => '@Ada Okafor would love to compare notes after the session.', 'time' => '12 MIN AGO', 'icon' => 'fa-at', 'url' => base_url('attendees/sessions/1')],
    ['id' => 'n4', 'cat' => 'Networking', 'group' => 'TODAY', 'unread' => true, 'title' => 'Your 1:1 with Kemi Bello is confirmed', 'body' => 'Tomorrow · 2:30 PM WAT · 15 minutes', 'time' => '40 MIN AGO', 'icon' => 'fa-calendar-check-o', 'url' => base_url('attendees/speakers')],
    ['id' => 'n6', 'cat' => 'Sessions', 'group' => 'TODAY', 'unread' => false, 'title' => 'Starting in 15 min: Operating Cadence for Scale', 'body' => 'You added this session to your agenda.', 'time' => '2 HR AGO', 'icon' => 'fa-clock-o', 'url' => $agendaUrl],
    ['id' => 'n7', 'cat' => 'Booths', 'group' => 'EARLIER', 'unread' => false, 'title' => 'Utica Capital replied to your booth message', 'body' => '“Thanks for stopping by. Let’s set up a call next week.”', 'time' => 'YESTERDAY', 'icon' => 'fa-th-large', 'url' => base_url('attendees/exhibitors')],
    ['id' => 'n8', 'cat' => 'Networking', 'group' => 'EARLIER', 'unread' => false, 'title' => 'Zainab Musa accepted your connection request', 'body' => 'You can now message each other directly.', 'time' => 'YESTERDAY', 'icon' => 'fa-user-plus', 'url' => base_url('attendees/attendees')],
    ['id' => 'n9', 'cat' => 'Account', 'group' => 'EARLIER', 'unread' => false, 'title' => 'Complete your registration payment', 'body' => 'Unlock all paid sessions for Accelerate 2026. Ticket: NGN 25,000.', 'time' => '2 DAYS AGO', 'icon' => 'fa-lock', 'url' => base_url('attendees/checkout')],
];

$categories = [
    'Sessions' => ['fa-play-circle', '#d8198e', '#fff'],
    'Messages' => ['fa-comment', '#9d0f82', '#fff'],
    'Networking' => ['fa-user-plus', '#efb11e', '#260029'],
    'Booths' => ['fa-th-large', '#742c76', '#fff'],
    'Account' => ['fa-lock', 'rgba(239,177,30,.18)', '#efb11e'],
];
$filters = ['All', 'Unread', 'Sessions', 'Messages', 'Networking', 'Booths'];
$unreadCount = count($pendingConnections) + count(array_filter($notifications, static fn (array $item): bool => $item['unread']));
$filterCount = static function (string $filter) use ($pendingConnections, $notifications, $unreadCount): int {
    if ($filter === 'All') return count($notifications) + count($pendingConnections);
    if ($filter === 'Unread') return $unreadCount;
    if ($filter === 'Networking') return count($pendingConnections) + count(array_filter($notifications, static fn (array $item): bool => $item['cat'] === 'Networking'));
    return count(array_filter($notifications, static fn (array $item): bool => $item['cat'] === $filter));
};
$preferences = [
    ['sessions', 'Session reminders', '15 minutes before saved sessions start'],
    ['messages', 'Direct messages', 'When an attendee or speaker messages you'],
    ['mentions', 'Mentions', 'When you are @mentioned in chat or posts'],
    ['connections', 'Connections & bookings', 'Requests, acceptances and 1:1 confirmations'],
    ['booths', 'Booth replies', 'When an exhibitor responds to you'],
    ['email', 'Email digest', 'A summary every evening at 6 PM'],
];
?>

<main class="rc-notifications-page" data-screen-label="Notifications">
    <div class="rc-notifications-container">
        <header class="rc-notifications-header">
            <div class="rc-notifications-heading">
                <span class="rc-kicker">ACCELERATE 2026</span>
                <h1>Notifications</h1>
                <p data-notification-summary><?php echo $unreadCount > 0 ? 'You have ' . $unreadCount . ' unread notification' . ($unreadCount === 1 ? '.' : 's.') : 'You’re all caught up.'; ?></p>
            </div>
            <button class="rc-notifications-mark-all" type="button" id="markAllRead"><i class="fa fa-check-square-o" aria-hidden="true"></i>Mark all as read</button>
        </header>

        <div class="rc-notifications-layout">
            <section class="rc-notifications-main">
                <div class="rc-notifications-filters" role="group" aria-label="Notification filters">
                    <?php foreach ($filters as $index => $filter): ?>
                        <button class="notification-filter<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" data-filter="<?php echo esc($filter); ?>">
                            <?php echo esc($filter); ?><span data-filter-count><?php echo $filterCount($filter); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach (['TODAY', 'EARLIER'] as $group): ?>
                    <div class="notification-group rc-notification-group" data-group="<?php echo $group; ?>">
                        <span class="rc-notification-group-title"><?php echo $group; ?></span>

                        <?php if ($group === 'TODAY'): ?>
                            <?php foreach ($pendingConnections as $request): ?>
                                <?php
                                $requesterName = trim(ucfirst(strtolower($request['firstname'] ?? '')) . ' ' . ucfirst(strtolower($request['lastname'] ?? '')));
                                $requesterName = $requesterName !== '' ? $requesterName : ($request['email'] ?? 'An attendee');
                                ?>
                                <div class="notification-row rc-notification-row" data-category="Networking" data-unread="1" data-connection-id="<?php echo (int) $request['id']; ?>">
                                    <span class="rc-notification-tile" style="background:#efb11e;color:#260029"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
                                    <div class="rc-notification-copy">
                                        <div><span class="notification-dot"></span><strong>Connection request</strong></div>
                                        <p data-connection-message><?php echo esc($requesterName); ?> wants to connect with you.</p>
                                    </div>
                                    <div class="rc-notification-actions">
                                        <span class="rc-notification-time">JUST NOW</span>
                                        <span class="rc-connection-actions"><button type="button" data-connection-action="accept">Accept</button><button type="button" data-connection-action="decline">Decline</button></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php foreach ($notifications as $notification): ?>
                            <?php if ($notification['group'] !== $group) continue; ?>
                            <?php [$defaultIcon, $tileBg, $tileInk] = $categories[$notification['cat']]; ?>
                            <div class="notification-row rc-notification-row" data-category="<?php echo esc($notification['cat']); ?>" data-unread="<?php echo $notification['unread'] ? '1' : '0'; ?>">
                                <a class="rc-notification-row-link" href="<?php echo $notification['url']; ?>" aria-label="<?php echo esc($notification['title'], 'attr'); ?>"></a>
                                <span class="rc-notification-tile" style="background:<?php echo $tileBg; ?>;color:<?php echo $tileInk; ?>"><i class="fa <?php echo esc($notification['icon'] ?: $defaultIcon); ?>" aria-hidden="true"></i></span>
                                <div class="rc-notification-copy">
                                    <div><span class="notification-dot"></span><strong><?php echo esc($notification['title']); ?></strong></div>
                                    <p><?php echo esc($notification['body']); ?></p>
                                </div>
                                <div class="rc-notification-actions">
                                    <span class="rc-notification-time"><?php echo esc($notification['time']); ?></span>
                                    <button type="button" class="rc-notification-read-toggle"><?php echo $notification['unread'] ? 'Mark read' : 'Mark unread'; ?></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <div class="rc-notifications-empty" hidden><i class="fa fa-bell-slash-o" aria-hidden="true"></i><strong>You’re all caught up</strong><span>Nothing here for this filter.</span></div>
            </section>

            <aside class="rc-notification-preferences">
                <span class="rc-notification-prefs-title">NOTIFY ME ABOUT</span>
                <?php foreach ($preferences as $index => [$key, $label, $description]): ?>
                    <button type="button" class="rc-notification-preference" data-preference="<?php echo $key; ?>" aria-pressed="<?php echo in_array($index, [4, 6], true) ? 'false' : 'true'; ?>">
                        <span><strong><?php echo esc($label); ?></strong><small><?php echo esc($description); ?></small></span>
                        <span class="rc-notification-switch" aria-hidden="true"><span></span></span>
                    </button>
                <?php endforeach; ?>
            </aside>
        </div>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const getRows = () => [...document.querySelectorAll(".notification-row")];
    const filters = [...document.querySelectorAll(".notification-filter")];
    const empty = document.querySelector(".rc-notifications-empty");
    const summary = document.querySelector("[data-notification-summary]");

    const updateSummary = () => {
        const count = getRows().filter(row => row.dataset.unread === "1").length;
        summary.textContent = count ? `You have ${count} unread notification${count === 1 ? "." : "s."}` : "You’re all caught up.";
        const unreadFilterCount = document.querySelector('[data-filter="Unread"] [data-filter-count]');
        if (unreadFilterCount) unreadFilterCount.textContent = String(count);
    };
    const applyFilter = filter => {
        let visible = 0;
        getRows().forEach(row => {
            const show = filter === "All" || (filter === "Unread" && row.dataset.unread === "1") || row.dataset.category === filter;
            row.hidden = !show;
            if (show) visible += 1;
        });
        document.querySelectorAll(".notification-group").forEach(group => { group.hidden = !group.querySelector(".notification-row:not([hidden])"); });
        empty.hidden = visible !== 0;
    };
    filters.forEach(button => button.addEventListener("click", () => {
        filters.forEach(item => item.classList.remove("is-active"));
        button.classList.add("is-active");
        applyFilter(button.dataset.filter);
    }));
    document.getElementById("markAllRead")?.addEventListener("click", () => {
        getRows().forEach(row => {
            row.dataset.unread = "0";
            const toggle = row.querySelector(".rc-notification-read-toggle");
            if (toggle) toggle.textContent = "Mark unread";
        });
        updateSummary();
        applyFilter(document.querySelector(".notification-filter.is-active")?.dataset.filter || "All");
    });
    document.addEventListener("click", event => {
        const readToggle = event.target.closest(".rc-notification-read-toggle");
        if (readToggle) {
            event.preventDefault();
            event.stopPropagation();
            const row = readToggle.closest(".notification-row");
            const unread = row.dataset.unread !== "1";
            row.dataset.unread = unread ? "1" : "0";
            readToggle.textContent = unread ? "Mark read" : "Mark unread";
            updateSummary();
            return;
        }
        const preference = event.target.closest(".rc-notification-preference");
        if (preference) {
            preference.setAttribute("aria-pressed", preference.getAttribute("aria-pressed") === "true" ? "false" : "true");
            return;
        }
        const button = event.target.closest("[data-connection-action]");
        if (!button) return;
        const row = button.closest("[data-connection-id]");
        const connectionId = row?.dataset.connectionId;
        const action = button.dataset.connectionAction;
        if (!row || !connectionId || !action) return;
        row.querySelectorAll("[data-connection-action]").forEach(item => item.disabled = true);
        button.textContent = action === "accept" ? "Accepting..." : "Declining...";
        fetch("<?php echo base_url('api/connections'); ?>/" + connectionId + "/" + action, { method: "POST", headers: { "Content-Type": "application/json" } })
            .then(response => response.json().catch(() => ({})).then(payload => {
                if (!response.ok || payload.status !== "success") throw new Error(payload.message || "connection-action-failed");
                row.dataset.unread = "0";
                const message = row.querySelector("[data-connection-message]");
                if (message) message.textContent = action === "accept" ? "Connection accepted. You can now view each other’s social links." : "Connection request declined.";
                row.querySelector(".rc-connection-actions")?.remove();
                updateSummary();
            }))
            .catch(() => {
                row.querySelectorAll("[data-connection-action]").forEach(item => item.disabled = false);
                button.textContent = action === "accept" ? "Accept" : "Decline";
            });
    });
});
</script>
</body>
</html>
