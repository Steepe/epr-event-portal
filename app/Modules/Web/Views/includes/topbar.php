<?php
$session = session();
$uri = service('uri');
$current = $uri->getSegment(2) ?? '';
$conferenceId = $session->get('live-conference-id');
$agendaUrl = $conferenceId ? base_url('attendees/agenda/' . $conferenceId) : base_url('attendees/lobby');
$currentUserId = (int) ($session->get('user_id') ?: $session->get('attendee_id'));
$websocketUrl = trim((string) env('websocket.url'));
// Realtime uses the API reconciliation fallback unless a reachable socket URL
// is explicitly configured. This avoids repeated localhost socket failures in
// browsers while preserving instant updates through the configured channel.

$profilePicture = asset_url('images/user.png');
$profileFile = $session->get('profile_picture') ?: $session->get('profile_pic');
if (!empty($profileFile)) {
    $profilePicture = preg_match('/^https?:\/\//', $profileFile)
        ? $profileFile
        : base_url('uploads/attendee_pictures/' . ltrim($profileFile, '/'));
}

$unreadCount = isset($unread_messages) ? (int) $unread_messages : 0;
if ($currentUserId > 0) {
    try {
        $db = db_connect();
        if ($db->tableExists('tbl_attendee_connections')) {
            $unreadCount += (int) $db->table('tbl_attendee_connections')
                ->where('receiver_id', $currentUserId)
                ->where('status', 'pending')
                ->countAllResults();
        }
    } catch (\Throwable $e) {
        log_message('debug', '[Topbar] Unable to load connection notification count: ' . $e->getMessage());
    }
}
$navKey = $current;
if ($current === 'sessions') {
    $navKey = 'agenda';
} elseif (in_array($current, ['networking_center', 'attendees', 'speakers'], true)) {
    $navKey = 'networking_center';
} elseif ($current === 'emergence_booth') {
    $navKey = 'emergence_booth';
}

$showNav = in_array($current, [
    'agenda',
    'sessions',
    'networking_center',
    'attendees',
    'speakers',
    'exhibitors',
    'sponsors',
    'emergence_booth',
    'notifications',
    'profile',
], true);

$showNavLogo = in_array($current, [
    'agenda',
    'sessions',
    'networking_center',
    'attendees',
    'speakers',
    'exhibitors',
    'sponsors',
    'emergence_booth',
    'notifications',
    'profile',
], true);

$navItems = [
    ['LOBBY', 'lobby', base_url('attendees/lobby')],
    ['AGENDA', 'agenda', $agendaUrl],
    ['NETWORKING CENTER', 'networking_center', base_url('attendees/networking_center')],
    ['EXHIBITORS', 'exhibitors', base_url('attendees/exhibitors')],
    ['SPONSORS', 'sponsors', base_url('attendees/sponsors')],
    ['EMERGENCE BOOTH', 'emergence_booth', base_url('attendees/emergence_booth')],
];

$notificationPreview = [
    ['Sessions', 'Live now: Building Systems That Outlast Motivation', 'Kemi Bello is on stage. 1,284 people are watching.', '2 MIN AGO', true, 'fa-play-circle', '#d8198e', '#fff', base_url('attendees/sessions/1')],
    ['Messages', 'Grace Otieno sent you a message', '“Happy to share our weekly ops dashboard after the session.”', '8 MIN AGO', true, 'fa-comment', '#9d0f82', '#fff', base_url('attendees/attendees')],
    ['Messages', 'Femi Adebayo mentioned you in session chat', '@Ada Okafor would love to compare notes after the session.', '12 MIN AGO', true, 'fa-at', '#9d0f82', '#fff', base_url('attendees/sessions/1')],
    ['Networking', 'Your 1:1 with Kemi Bello is confirmed', 'Tomorrow · 2:30 PM WAT · 15 minutes', '40 MIN AGO', true, 'fa-calendar-check-o', '#efb11e', '#260029', base_url('attendees/speakers')],
];
$displayUnreadCount = max(4, $unreadCount);
?>

<body class="rc-body">
<script>
    window.EPR_PORTAL_REALTIME = {
        userId: <?php echo json_encode($currentUserId); ?>,
        wsUrl: <?php echo json_encode($websocketUrl); ?>,
        pendingConnectionsUrl: <?php echo json_encode(base_url('api/connections/pending')); ?>,
        notificationsUrl: <?php echo json_encode(base_url('attendees/notifications')); ?>
    };
</script>
<div class="rc-stage">
    <div class="rc-stage-inner">
        <header class="rc-topbar <?php echo $showNav ? 'has-nav' : ''; ?>">
            <a href="<?php echo base_url('attendees/start'); ?>" aria-label="Start">
                <img class="rc-topbar-logo" src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Events">
            </a>

            <?php if ($showNav): ?>
                <nav class="rc-nav" aria-label="Portal navigation">
                    <?php foreach ($navItems as [$label, $key, $url]): ?>
                        <a href="<?php echo $url; ?>" class="<?php echo $navKey === $key ? 'is-active' : ''; ?>">
                            <?php echo esc($label); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <div class="rc-userbar">
                <?php if ($showNavLogo): ?>
                    <a href="<?php echo base_url('attendees/lobby'); ?>" aria-label="Lobby">
                        <img class="rc-topbar-accelerate"
                             src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>"
                             alt="Accelerate">
                    </a>
                    <span class="rc-topbar-divider"></span>
                <?php endif; ?>

                <button class="rc-bell <?php echo $current === 'notifications' ? 'is-active' : ''; ?>"
                   type="button"
                   title="Notifications"
                   aria-label="Notifications"
                   aria-expanded="false"
                   data-rc-notification-toggle>
                    <i class="fa fa-bell" aria-hidden="true"></i>
                    <span class="rc-bell-badge" <?php echo $displayUnreadCount > 0 ? '' : 'hidden'; ?>><?php echo $displayUnreadCount; ?></span>
                </button>

                <div class="rc-user-menu" data-rc-user-menu>
                    <button type="button" class="rc-user-toggle" aria-label="Open account menu" aria-expanded="false" data-rc-user-toggle>
                        <img src="<?php echo $profilePicture; ?>" alt="Profile">
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </button>
                    <div class="rc-dropdown">
                        <a href="<?php echo base_url('attendees/profile'); ?>">
                            <i class="fa fa-user" aria-hidden="true"></i>Profile
                        </a>
                        <span class="rc-dropdown-separator"></span>
                        <a href="<?php echo base_url('attendees/logout'); ?>" class="danger">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <aside class="rc-notification-popover" aria-hidden="true" data-rc-notification-popover>
            <div class="rc-notification-popover-head">
                <div>
                    <span class="rc-notification-popover-title">Notifications</span>
                    <span class="rc-notification-count"><?php echo $displayUnreadCount > 0 ? $displayUnreadCount . ' NEW' : 'ALL READ'; ?></span>
                </div>
                <button type="button" data-rc-mark-notifications-read>Mark all read</button>
            </div>
            <?php foreach ($notificationPreview as [$category, $title, $body, $time, $unread, $icon, $tileBg, $tileInk, $url]): ?>
                <a class="rc-notification-preview-row"
                   href="<?php echo $url; ?>"
                   data-unread="<?php echo $unread ? '1' : '0'; ?>"
                   style="background:<?php echo $unread ? 'rgba(157,15,130,.16)' : 'transparent'; ?>">
                    <span class="rc-notification-preview-icon" style="background:<?php echo $tileBg; ?>;color:<?php echo $tileInk; ?>">
                        <i class="fa <?php echo $icon; ?>" aria-hidden="true"></i>
                    </span>
                    <span class="rc-notification-preview-copy">
                        <strong style="font-weight:<?php echo $unread ? '600' : '400'; ?>"><?php echo esc($title); ?></strong>
                        <span><?php echo esc($body); ?></span>
                        <small><?php echo esc($time); ?></small>
                    </span>
                    <span class="rc-notification-preview-dot" style="background:<?php echo $unread ? '#d8198e' : 'transparent'; ?>"></span>
                </a>
            <?php endforeach; ?>
            <a class="rc-notification-popover-all" href="<?php echo base_url('attendees/notifications'); ?>">View all notifications</a>
        </aside>

<script>
(function() {
    const notificationToggle = document.querySelector("[data-rc-notification-toggle]");
    const notificationPopover = document.querySelector("[data-rc-notification-popover]");
    const userMenu = document.querySelector("[data-rc-user-menu]");
    const userToggle = document.querySelector("[data-rc-user-toggle]");

    function closeNotificationPopover() {
        notificationPopover?.classList.remove("is-open");
        notificationPopover?.setAttribute("aria-hidden", "true");
        notificationToggle?.setAttribute("aria-expanded", "false");
    }

    function closeUserMenu() {
        userMenu?.classList.remove("is-open");
        userToggle?.setAttribute("aria-expanded", "false");
    }

    notificationToggle?.addEventListener("click", function(event) {
        event.stopPropagation();
        const willOpen = !notificationPopover.classList.contains("is-open");
        closeUserMenu();
        notificationPopover.classList.toggle("is-open", willOpen);
        notificationPopover.setAttribute("aria-hidden", willOpen ? "false" : "true");
        notificationToggle.setAttribute("aria-expanded", willOpen ? "true" : "false");
    });

    userToggle?.addEventListener("click", function(event) {
        event.stopPropagation();
        const willOpen = !userMenu.classList.contains("is-open");
        closeNotificationPopover();
        userMenu.classList.toggle("is-open", willOpen);
        userToggle.setAttribute("aria-expanded", willOpen ? "true" : "false");
    });

    document.querySelector("[data-rc-mark-notifications-read]")?.addEventListener("click", function() {
        notificationPopover.querySelectorAll("[data-unread='1']").forEach(function(row) {
            row.dataset.unread = "0";
            row.style.background = "transparent";
            row.querySelector("strong").style.fontWeight = "400";
            row.querySelector(".rc-notification-preview-dot").style.background = "transparent";
        });
        const badge = notificationToggle.querySelector(".rc-bell-badge");
        badge.hidden = true;
        badge.textContent = "0";
        notificationPopover.querySelector(".rc-notification-count").textContent = "ALL READ";
    });

    document.addEventListener("click", function(event) {
        if (!event.target.closest("[data-rc-notification-popover]")) {
            closeNotificationPopover();
        }
        if (!event.target.closest("[data-rc-user-menu]")) {
            closeUserMenu();
        }
    });

    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeNotificationPopover();
            closeUserMenu();
        }
    });
})();
</script>
