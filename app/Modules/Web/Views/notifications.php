<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$pendingConnections = $pendingConnections ?? [];
$notifications = [
    ['Sessions', 'Live session starting soon', 'Building Sustainable Systems for Execution starts at 2:00 PM GMT.', '10 MIN AGO', true, 'fa-play-circle', base_url('attendees/lobby'), 'TODAY'],
    ['Messages', 'New attendee message', 'A fellow attendee sent a networking message.', '32 MIN AGO', true, 'fa-comment', base_url('attendees/attendees'), 'TODAY'],
    ['Booths', 'Exhibitor reply', 'An exhibitor has responded to your enquiry.', 'YESTERDAY', false, 'fa-th-large', base_url('attendees/exhibitors'), 'EARLIER'],
    ['Account', 'Profile reminder', 'Complete your profile so attendees can find you more easily.', 'EARLIER', false, 'fa-lock', base_url('attendees/profile'), 'EARLIER'],
];

$filters = ['All', 'Unread', 'Sessions', 'Messages', 'Networking', 'Booths'];
$catMap = [
    'Sessions' => ['#d8198e', '#fff'],
    'Messages' => ['#9d0f82', '#fff'],
    'Networking' => ['#efb11e', '#260029'],
    'Booths' => ['#742c76', '#fff'],
    'Account' => ['rgba(239,177,30,.18)', '#efb11e'],
];
?>

<main class="rc-page rc-page-center" data-screen-label="Notifications">
    <div class="rc-container" style="max-width:980px">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:24px;flex-wrap:wrap">
            <div class="rc-title-stack">
                <span class="rc-kicker">Notifications</span>
                <span class="rc-page-title">Stay in the loop.</span>
                <span class="rc-page-sub">Session reminders, messages, booth replies, and account prompts collect here.</span>
            </div>
            <button class="rc-outline-pill" type="button" id="markAllRead">
                <i class="fa fa-check" aria-hidden="true"></i>Mark all read
            </button>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <?php foreach ($filters as $index => $filter): ?>
                <button class="notification-filter"
                        type="button"
                        data-filter="<?php echo esc($filter); ?>"
                        style="padding:8px 14px;border-radius:999px;font:inherit;font-size:13px;cursor:pointer;color:#fff;background:<?php echo $index === 0 ? 'rgba(157,15,130,.55)' : 'rgba(34,16,38,.72)'; ?>;border:1px solid <?php echo $index === 0 ? '#d8198e' : 'rgba(255,255,255,.2)'; ?>">
                    <?php echo esc($filter); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <section class="rc-panel" style="padding:8px;display:flex;flex-direction:column">
            <?php foreach (['TODAY', 'EARLIER'] as $group): ?>
                <div class="notification-group" data-group="<?php echo $group; ?>">
                    <div class="rc-mono" style="padding:14px 16px 8px;font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.55)"><?php echo $group; ?></div>
                    <?php if ($group === 'TODAY'): ?>
                        <?php foreach ($pendingConnections as $request): ?>
                            <?php
                            $requesterName = trim(ucfirst(strtolower($request['firstname'] ?? '')) . ' ' . ucfirst(strtolower($request['lastname'] ?? '')));
                            $requesterName = $requesterName !== '' ? $requesterName : ($request['email'] ?? 'An attendee');
                            ?>
                            <div class="notification-row"
                                 data-category="Networking"
                                 data-unread="1"
                                 data-connection-id="<?php echo (int) $request['id']; ?>"
                                 style="display:grid;grid-template-columns:44px minmax(0,1fr) auto;gap:14px;align-items:start;padding:14px 16px;border-top:1px solid rgba(255,255,255,.06);background:rgba(157,15,130,.16)">
                                <span style="width:44px;height:44px;border-radius:12px;background:#efb11e;color:#260029;display:flex;align-items:center;justify-content:center;font-size:17px">
                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                </span>
                                <span style="display:flex;flex-direction:column;gap:4px;min-width:0">
                                    <strong style="font-size:14px;font-weight:600;line-height:1.35;color:#fff">Connection request</strong>
                                    <span data-connection-message style="font-size:13px;color:rgba(255,255,255,.72);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                        <?php echo esc($requesterName); ?> wants to connect with you.
                                    </span>
                                    <span class="rc-mono" style="font-size:10px;letter-spacing:.08em;color:rgba(255,255,255,.5)">
                                        <?php echo esc($request['email'] ?? ''); ?>
                                    </span>
                                </span>
                                <span style="display:flex;align-items:center;gap:8px">
                                    <button type="button"
                                            class="rc-outline-pill"
                                            data-connection-action="accept"
                                            style="border-color:rgba(74,222,128,.55);color:#4ade80;background:rgba(74,222,128,.10)">
                                        Accept
                                    </button>
                                    <button type="button"
                                            class="rc-outline-pill"
                                            data-connection-action="decline"
                                            style="border-color:rgba(255,255,255,.18);color:rgba(255,255,255,.76);background:rgba(255,255,255,.05)">
                                        Decline
                                    </button>
                                    <span class="notification-dot" style="width:8px;height:8px;border-radius:50%;background:#d8198e;"></span>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php foreach ($notifications as [$cat, $title, $body, $time, $unread, $icon, $url, $itemGroup]): ?>
                        <?php if ($itemGroup !== $group) continue; ?>
                        <?php [$tileBg, $tileInk] = $catMap[$cat] ?? ['rgba(255,255,255,.08)', '#fff']; ?>
                        <a href="<?php echo $url; ?>"
                           class="notification-row"
                           data-category="<?php echo esc($cat); ?>"
                           data-unread="<?php echo $unread ? '1' : '0'; ?>"
                           style="display:grid;grid-template-columns:44px minmax(0,1fr) auto;gap:14px;align-items:start;padding:14px 16px;border-top:1px solid rgba(255,255,255,.06);background:<?php echo $unread ? 'rgba(157,15,130,.16)' : 'transparent'; ?>">
                            <span style="width:44px;height:44px;border-radius:12px;background:<?php echo $tileBg; ?>;color:<?php echo $tileInk; ?>;display:flex;align-items:center;justify-content:center;font-size:17px">
                                <i class="fa <?php echo $icon; ?>" aria-hidden="true"></i>
                            </span>
                            <span style="display:flex;flex-direction:column;gap:4px;min-width:0">
                                <strong style="font-size:14px;font-weight:<?php echo $unread ? '600' : '400'; ?>;line-height:1.35;color:#fff"><?php echo esc($title); ?></strong>
                                <span style="font-size:13px;color:rgba(255,255,255,.72);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?php echo esc($body); ?></span>
                                <span class="rc-mono" style="font-size:10px;letter-spacing:.08em;color:rgba(255,255,255,.5)"><?php echo esc($time); ?></span>
                            </span>
                            <span class="notification-dot" style="width:8px;height:8px;margin-top:6px;border-radius:50%;background:<?php echo $unread ? '#d8198e' : 'transparent'; ?>"></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
    </div>
</main>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const rows = [...document.querySelectorAll(".notification-row")];
    const filters = [...document.querySelectorAll(".notification-filter")];

    filters.forEach(button => {
        button.addEventListener("click", () => {
            filters.forEach(item => {
                item.style.background = "rgba(34,16,38,.72)";
                item.style.borderColor = "rgba(255,255,255,.2)";
            });
            button.style.background = "rgba(157,15,130,.55)";
            button.style.borderColor = "#d8198e";

            const filter = button.dataset.filter;
            rows.forEach(row => {
                const show = filter === "All"
                    || (filter === "Unread" && row.dataset.unread === "1")
                    || row.dataset.category === filter;
                row.style.display = show ? "grid" : "none";
            });
        });
    });

    document.getElementById("markAllRead")?.addEventListener("click", () => {
        rows.forEach(row => {
            row.dataset.unread = "0";
            row.style.background = "transparent";
            const dot = row.querySelector(".notification-dot");
            if (dot) dot.style.background = "transparent";
        });
    });

    document.addEventListener("click", event => {
        const button = event.target.closest("[data-connection-action]");
        if (!button) return;

        const row = button.closest("[data-connection-id]");
        const connectionId = row?.dataset.connectionId;
        const action = button.dataset.connectionAction;
        if (!row || !connectionId || !action) return;

        row.querySelectorAll("[data-connection-action]").forEach(item => item.disabled = true);
        button.textContent = action === "accept" ? "Accepting..." : "Declining...";

        fetch("<?php echo base_url('api/connections'); ?>/" + connectionId + "/" + action, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        })
            .then(response => response.json().catch(() => ({})).then(payload => {
                if (!response.ok || payload.status !== "success") {
                    throw new Error(payload.message || "connection-action-failed");
                }

                row.dataset.unread = "0";
                row.style.background = "transparent";
                const dot = row.querySelector(".notification-dot");
                if (dot) dot.style.background = "transparent";
                const message = row.querySelector("[data-connection-message]");
                if (message) {
                    message.textContent = action === "accept"
                        ? "Connection accepted. You can now view each other's social links."
                        : "Connection request declined.";
                }
                row.querySelectorAll("[data-connection-action]").forEach(item => item.remove());
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
