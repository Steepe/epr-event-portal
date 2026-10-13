<?php
$session = session();
$uri = service('uri');
$current = $uri->getSegment(2) ?? '';
$conferenceId = $session->get('live-conference-id');
$agendaUrl = $conferenceId ? base_url('attendees/agenda/' . $conferenceId) : base_url('attendees/lobby');
$currentUserId = (int) ($session->get('user_id') ?: $session->get('attendee_id'));
$websocketUrl = (string) env('websocket.url');
if ($websocketUrl === '') {
    $websocketUrl = ENVIRONMENT === 'development'
        ? 'ws://127.0.0.1:8081'
        : (service('request')->isSecure() ? 'wss://' : 'ws://') . service('request')->getUri()->getHost() . ':8081';
}

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
} elseif (in_array($current, ['networking_center', 'attendees', 'speakers', 'communities'], true)) {
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
    'communities',
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
    'communities',
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
?>

<body class="rc-body">
<script>
    window.EPR_PORTAL_REALTIME = {
        userId: <?php echo json_encode($currentUserId); ?>,
        wsUrl: <?php echo json_encode($websocketUrl); ?>,
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

                <a class="rc-bell <?php echo $current === 'notifications' ? 'is-active' : ''; ?>"
                   href="<?php echo base_url('attendees/notifications'); ?>"
                   title="Notifications"
                   aria-label="Notifications">
                    <i class="fa fa-bell" aria-hidden="true"></i>
                    <span class="rc-bell-badge" <?php echo $unreadCount > 0 ? '' : 'hidden'; ?>><?php echo $unreadCount; ?></span>
                </a>

                <div class="rc-user-menu">
                    <span class="rc-user-toggle" tabindex="0">
                        <img src="<?php echo $profilePicture; ?>" alt="Profile">
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </span>
                    <div class="rc-dropdown">
                        <a href="<?php echo base_url('attendees/profile'); ?>">
                            <i class="fa fa-user" aria-hidden="true"></i>Profile
                        </a>
                        <a href="<?php echo base_url('attendees/logout'); ?>" class="danger">
                            <i class="fa fa-sign-out" aria-hidden="true"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>
