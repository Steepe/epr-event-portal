<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), true],
    ['SPEAKERS', base_url('attendees/speakers'), false],
    ['COMMUNITIES', base_url('attendees/communities'), false],
];

$searchValue = (string) service('request')->getGet('attendee_name');
$currentUserId = (int) (session('user_id') ?: session('attendee_id'));
$onlineCount = count(array_filter($attendees ?? [], static fn (array $attendee): bool => !empty($attendee['is_online']) && (int) $attendee['is_online'] === 1));
$attendeeFilters = ['All', 'Online now', 'Founders', 'Operations', 'Finance', 'Tech'];
?>

<main class="rc-page rc-page-center" data-screen-label="Attendees">
    <div class="rc-container" style="gap:24px">
        <header class="rc-net-sub-header">
            <div class="rc-net-heading">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-back-link">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>NETWORKING CENTER
                </a>
                <span class="rc-page-title">Attendees</span>
                <span class="rc-page-sub"><span data-attendee-count><?php echo !empty($attendees) ? count($attendees) : 0; ?></span> people in your network · <?php echo $onlineCount; ?> online now</span>
            </div>

            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $active]): ?>
                    <a href="<?php echo $url; ?>"
                       class="rc-net-tab <?php echo $active ? 'is-active' : ''; ?>">
                        <?php echo esc($label); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </header>

        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <form method="get" data-attendee-search-form style="position:relative;flex:1;min-width:240px;max-width:380px;display:flex">
                <i class="fa fa-search" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,.6)" aria-hidden="true"></i>
                <input class="rc-input rc-attendee-search" type="text" name="attendee_name" value="<?php echo esc($searchValue, 'attr'); ?>" placeholder="Search by name, company or role" autocomplete="off" style="padding-left:42px;background:#1d0a21!important;border-radius:12px!important">
            </form>

            <div class="rc-attendee-filters">
                <?php foreach ($attendeeFilters as $filterIndex => $filter): ?>
                    <button type="button" class="rc-attendee-filter<?php echo $filterIndex === 0 ? ' is-active' : ''; ?>" data-attendee-filter="<?php echo esc($filter, 'attr'); ?>"><?php echo esc($filter); ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($attendees)): ?>
            <section class="rc-attendee-grid" data-attendee-grid>
                <?php foreach ($attendees as $attendee): ?>
                    <?php
                    $firstName = mb_convert_case((string) ($attendee['firstname'] ?? ''), MB_CASE_TITLE, 'UTF-8');
                    $lastName = mb_convert_case((string) ($attendee['lastname'] ?? ''), MB_CASE_TITLE, 'UTF-8');
                    $fullName = trim($firstName . ' ' . $lastName);
                    $firstLetter = mb_strtoupper(mb_substr($firstName ?: 'A', 0, 1, 'UTF-8'), 'UTF-8');
                    $image = !empty($attendee['profile_picture'])
                        ? base_url('uploads/attendee_pictures/' . $attendee['profile_picture'])
                        : asset_url('images/user.png');
                    $role = trim((string) ($attendee['position'] ?? $attendee['job_title'] ?? $attendee['occupation'] ?? 'Attendee'));
                    $company = trim((string) ($attendee['company'] ?? $attendee['organization'] ?? ''));
                    $messageRole = ($role ?: 'Attendee') . ($company !== '' ? ' · ' . $company : '');
                    $place = trim((string) (($attendee['city'] ?? '') . (!empty($attendee['country']) ? ', ' . $attendee['country'] : '')));
                    $isOnline = !empty($attendee['is_online']) && (int) $attendee['is_online'] === 1;
                    $tagHaystack = strtolower($role . ' ' . $company);
                    if (str_contains($tagHaystack, 'founder') || str_contains($tagHaystack, 'owner')) {
                        $tags = ['Founders', 'Strategy'];
                    } elseif (str_contains($tagHaystack, 'financ') || str_contains($tagHaystack, 'bank') || str_contains($tagHaystack, 'account')) {
                        $tags = ['Finance', 'Strategy'];
                    } elseif (str_contains($tagHaystack, 'engineer') || str_contains($tagHaystack, 'product') || str_contains($tagHaystack, 'tech')) {
                        $tags = ['Tech', 'Product'];
                    } else {
                        $tags = ['Operations', 'Systems'];
                    }
                    $isConnected = !empty($attendee['is_connected']) && (int) $attendee['is_connected'] === 1;
                    $connectionStatus = (string) ($attendee['connection_status'] ?? '');
                    $connectionDirection = (string) ($attendee['connection_direction'] ?? '');
                    $connectionId = (int) ($attendee['connection_id'] ?? 0);
                    $isSelf = (int) ($attendee['attendee_id'] ?? 0) === $currentUserId;
                    $connectLabel = 'Connect';
                    $connectIcon = 'fa-user-plus';
                    $connectMuted = false;
                    $connectDisabled = false;

                    if ($isSelf) {
                        $connectLabel = 'You';
                        $connectIcon = 'fa-user';
                        $connectMuted = true;
                        $connectDisabled = true;
                    } elseif ($isConnected || $connectionStatus === 'accepted') {
                        $connectLabel = 'Connected';
                        $connectIcon = 'fa-check';
                        $connectMuted = true;
                        $connectDisabled = true;
                    } elseif ($connectionStatus === 'pending' && $connectionDirection === 'sent') {
                        $connectLabel = 'Requested';
                        $connectIcon = 'fa-clock-o';
                        $connectMuted = true;
                        $connectDisabled = true;
                    } elseif ($connectionStatus === 'pending' && $connectionDirection === 'received') {
                        $connectLabel = 'Accept';
                        $connectIcon = 'fa-check';
                    }

                    $searchIndex = strtolower(trim($fullName . ' ' . $role . ' ' . $company . ' ' . $place));
                    $socials = [
                        'facebook' => trim((string) ($attendee['facebook'] ?? '')),
                        'twitter' => trim((string) ($attendee['twitter'] ?? '')),
                        'instagram' => trim((string) ($attendee['instagram'] ?? '')),
                        'linkedin' => trim((string) ($attendee['linkedin'] ?? '')),
                    ];
                    ?>
                    <article class="rc-person-card rc-attendee-card"
                             role="button"
                             tabindex="0"
                             aria-label="View profile for <?php echo esc($fullName ?: 'Attendee', 'attr'); ?>"
                             data-attendee-card
                             data-search="<?php echo esc($searchIndex, 'attr'); ?>"
                             data-tags="<?php echo esc(implode('|', $tags), 'attr'); ?>"
                             data-profile-id="<?php echo (int) $attendee['attendee_id']; ?>"
                             data-profile-name="<?php echo esc($fullName ?: 'Attendee', 'attr'); ?>"
                             data-profile-role="<?php echo esc($role ?: 'Attendee', 'attr'); ?>"
                             data-profile-company="<?php echo esc($company, 'attr'); ?>"
                             data-profile-location="<?php echo esc($place ?: 'Conference portal', 'attr'); ?>"
                             data-profile-initial="<?php echo esc($firstLetter, 'attr'); ?>"
                             data-profile-avatar="<?php echo esc($image, 'attr'); ?>"
                             data-profile-online="<?php echo $isOnline ? '1' : '0'; ?>"
                             data-profile-connected="<?php echo $isConnected ? '1' : '0'; ?>"
                             data-profile-connection-id="<?php echo $connectionId; ?>"
                             data-profile-connection-status="<?php echo esc($connectionStatus, 'attr'); ?>"
                             data-profile-connection-direction="<?php echo esc($connectionDirection, 'attr'); ?>"
                             data-profile-self="<?php echo $isSelf ? '1' : '0'; ?>"
                             data-social-facebook="<?php echo esc($socials['facebook'], 'attr'); ?>"
                             data-social-twitter="<?php echo esc($socials['twitter'], 'attr'); ?>"
                             data-social-instagram="<?php echo esc($socials['instagram'], 'attr'); ?>"
                             data-social-linkedin="<?php echo esc($socials['linkedin'], 'attr'); ?>"
                             style="padding:20px;display:flex;flex-direction:column;gap:14px">
                        <div style="display:flex;align-items:center;gap:14px">
                            <div class="rc-avatar-token">
                                <span data-avatar-fallback style="display:none"><?php echo esc($firstLetter); ?></span>
                                <img src="<?php echo $image; ?>"
                                     alt="<?php echo esc($fullName); ?>"
                                     onerror="this.remove();this.parentNode.querySelector('[data-avatar-fallback]').style.display='inline';"
                                     style="width:100%;height:100%;object-fit:cover">
                                <?php if ($isOnline): ?>
                                    <span class="rc-status-dot" aria-label="Online"></span>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:2px;min-width:0">
                                <span style="font-size:16px;font-weight:600"><?php echo esc($fullName ?: 'Attendee'); ?></span>
                                <span style="font-size:13px;color:rgba(255,255,255,.78)"><?php echo esc($role ?: 'Attendee'); ?></span>
                                <span style="font-size:13px;color:rgba(255,255,255,.62)"><?php echo esc($company); ?></span>
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:11px;letter-spacing:.08em;color:rgba(255,255,255,.65)">
                            <i class="fa fa-map-marker" aria-hidden="true"></i><?php echo esc($place ?: 'Conference portal'); ?>
                        </div>

                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <?php foreach ($tags as $tag): ?><span class="rc-card-tag"><?php echo esc($tag); ?></span><?php endforeach; ?>
                        </div>

                        <div style="margin-top:auto;display:flex;gap:8px">
                            <button type="button"
                                    class="rc-card-action rc-connect-toggle <?php echo $connectMuted ? 'is-muted' : ''; ?>"
                                    <?php echo $connectDisabled ? 'disabled' : ''; ?>>
                                <i class="fa <?php echo $connectIcon; ?>" aria-hidden="true"></i><span><?php echo esc($connectLabel); ?></span>
                            </button>
                            <button type="button"
                                    class="rc-icon-action rc-message-toggle"
                                    title="Message"
                                    aria-label="Message <?php echo esc($fullName ?: 'Attendee', 'attr'); ?>"
                                    data-attendee-id="<?php echo (int) $attendee['attendee_id']; ?>"
                                    data-attendee-name="<?php echo esc($fullName ?: 'Attendee', 'attr'); ?>"
                                    data-attendee-role="<?php echo esc($messageRole, 'attr'); ?>"
                                    data-attendee-initial="<?php echo esc($firstLetter, 'attr'); ?>"
                                    data-attendee-avatar="<?php echo esc($image, 'attr'); ?>"
                                    data-attendee-online="<?php echo $isOnline ? '1' : '0'; ?>"
                                    data-sender-id="<?php echo esc((string) $currentUserId, 'attr'); ?>">
                                <i class="fa fa-comment-o" aria-hidden="true"></i>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
            <div class="rc-empty rc-attendee-empty" data-attendee-empty hidden>No attendees match that search.</div>
        <?php else: ?>
            <div class="rc-empty">No attendees match that search.</div>
        <?php endif; ?>
    </div>
</main>

<div class="rc-profile-backdrop" data-rc-profile-close></div>
<aside class="rc-profile-drawer" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Attendee profile">
    <button type="button" class="rc-profile-close" data-rc-profile-close aria-label="Close attendee profile">&times;</button>
    <div class="rc-profile-hero">
        <span class="rc-profile-avatar" data-profile-avatar>
            <span data-profile-initial>A</span>
        </span>
        <span class="rc-profile-online" data-profile-online hidden>Online now</span>
        <h2 data-profile-name>Attendee</h2>
        <p data-profile-role>Attendee</p>
    </div>
    <div class="rc-profile-body">
        <div class="rc-profile-meta">
            <span><i class="fa fa-briefcase" aria-hidden="true"></i><strong>Company</strong><em data-profile-company></em></span>
            <span><i class="fa fa-map-marker" aria-hidden="true"></i><strong>Location</strong><em data-profile-location>Conference portal</em></span>
        </div>
        <div class="rc-profile-social-section">
            <div class="rc-profile-section-title">Social Links</div>
            <div class="rc-profile-socials" data-profile-socials></div>
            <div class="rc-profile-social-lock" data-profile-social-lock>
                Connect with this attendee to view their social links.
            </div>
        </div>
        <div class="rc-profile-actions">
            <button type="button" class="rc-card-action" data-profile-connect>
                <i class="fa fa-user-plus" aria-hidden="true"></i><span>Connect</span>
            </button>
            <button type="button" class="rc-icon-action" data-profile-message aria-label="Message attendee">
                <i class="fa fa-comment-o" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</aside>

<div class="rc-dm-backdrop" data-rc-dm-close></div>
<aside class="rc-dm-drawer" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Direct message">
    <div class="rc-dm-head">
        <span class="rc-dm-avatar" data-rc-dm-avatar>
            <span data-rc-dm-initial>A</span>
            <span class="rc-status-dot"></span>
        </span>
        <div class="rc-dm-person">
            <span data-rc-dm-name>Attendee</span>
            <small data-rc-dm-role>Attendee</small>
        </div>
        <button type="button" class="rc-dm-close" data-rc-dm-close aria-label="Close message panel">&times;</button>
    </div>
    <div class="rc-dm-messages" data-rc-dm-messages>
        <div class="rc-dm-empty">Start the conversation. Mention a session you both attended or what you're working on.</div>
    </div>
    <form class="rc-dm-compose" data-rc-dm-form>
        <div style="position:relative;display:flex;align-items:center;flex:1;min-width:0">
            <button type="button" data-rc-dm-emoji-toggle aria-label="Choose an emoji" aria-expanded="false" style="width:38px;height:38px;flex:none;border:0;border-radius:9px;background:transparent;color:rgba(255,255,255,.75);font-size:17px;cursor:pointer"><i class="fa fa-smile-o" aria-hidden="true"></i></button>
            <input type="text" data-rc-dm-input placeholder="Write a message..." autocomplete="off" required style="flex:1;min-width:0">
            <div data-rc-dm-emoji-picker hidden role="dialog" aria-label="Emoji picker" style="position:absolute;z-index:25;bottom:46px;left:0;width:216px;padding:8px;border:1px solid rgba(255,255,255,.16);border-radius:12px;background:#29112e;box-shadow:0 14px 32px rgba(0,0,0,.34);grid-template-columns:repeat(6,1fr);gap:3px">
                <?php foreach (['😀', '😂', '😍', '🥳', '👍', '👏', '🔥', '🎉', '💡', '✅', '🙌', '💯', '🤝', '❤️', '🙏', '👀', '🚀', '✨'] as $emoji): ?>
                    <button type="button" data-rc-dm-emoji="<?php echo esc($emoji, 'attr'); ?>" aria-label="Add <?php echo esc($emoji, 'attr'); ?>" style="border:0;border-radius:7px;background:transparent;padding:6px;cursor:pointer;font-size:19px;line-height:1"><?php echo $emoji; ?></button>
                <?php endforeach; ?>
            </div>
        </div>
        <button type="submit">Send</button>
    </form>
</aside>

</div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
(function() {
    const searchForm = document.querySelector("[data-attendee-search-form]");
    const searchInput = document.querySelector(".rc-attendee-search");
    const attendeeCards = Array.from(document.querySelectorAll("[data-attendee-card]"));
    const attendeeFilters = Array.from(document.querySelectorAll("[data-attendee-filter]"));
    const attendeeCount = document.querySelector("[data-attendee-count]");
    const attendeeEmpty = document.querySelector("[data-attendee-empty]");
    let activeFilter = "All";

    const profileDrawer = document.querySelector(".rc-profile-drawer");
    const profileBackdrop = document.querySelector(".rc-profile-backdrop");
    const profileAvatar = profileDrawer?.querySelector("[data-profile-avatar]");
    const profileOnline = profileDrawer?.querySelector("[data-profile-online]");
    const profileName = profileDrawer?.querySelector("[data-profile-name]");
    const profileRole = profileDrawer?.querySelector("[data-profile-role]");
    const profileCompany = profileDrawer?.querySelector("[data-profile-company]");
    const profileLocation = profileDrawer?.querySelector("[data-profile-location]");
    const profileSocials = profileDrawer?.querySelector("[data-profile-socials]");
    const profileSocialLock = profileDrawer?.querySelector("[data-profile-social-lock]");
    const profileConnect = profileDrawer?.querySelector("[data-profile-connect]");
    const profileMessage = profileDrawer?.querySelector("[data-profile-message]");

    const drawer = document.querySelector(".rc-dm-drawer");
    const backdrop = document.querySelector(".rc-dm-backdrop");
    const avatar = document.querySelector("[data-rc-dm-avatar]");
    const nameNode = document.querySelector("[data-rc-dm-name]");
    const roleNode = document.querySelector("[data-rc-dm-role]");
    const messagesNode = document.querySelector("[data-rc-dm-messages]");
    const form = document.querySelector("[data-rc-dm-form]");
    const input = document.querySelector("[data-rc-dm-input]");
    const emojiToggle = document.querySelector("[data-rc-dm-emoji-toggle]");
    const emojiPicker = document.querySelector("[data-rc-dm-emoji-picker]");
    const currentUserId = <?php echo json_encode($currentUserId); ?>;
    const messageThreadUrl = <?php echo json_encode(base_url('api/messages/thread')); ?>;
    const messageSendUrl = <?php echo json_encode(base_url('api/messages/send')); ?>;
    const messages = {};
    let activeThread = null;
    let activeProfile = null;

    const socialMeta = {
        facebook: { label: "Facebook", icon: "fa-facebook", base: "https://facebook.com/" },
        twitter: { label: "X", icon: "fa-twitter", base: "https://twitter.com/" },
        instagram: { label: "Instagram", icon: "fa-instagram", base: "https://instagram.com/" },
        linkedin: { label: "LinkedIn", icon: "fa-linkedin", base: "https://linkedin.com/in/" }
    };

    function text(value) {
        return value || "";
    }

    function isOnline(value) {
        return value === true || value === "1" || value === 1;
    }

    function createAvatar(target, details, sizeClass) {
        const initial = text(details.initial).slice(0, 1).toUpperCase() || "A";
        target.innerHTML = "";

        if (details.avatar) {
            const image = document.createElement("img");
            image.src = details.avatar;
            image.alt = details.name || "Attendee";
            image.addEventListener("error", function() {
                target.innerHTML = "";
                const fallback = document.createElement("span");
                fallback.textContent = initial;
                target.appendChild(fallback);
                if (isOnline(details.online)) {
                    const status = document.createElement("span");
                    status.className = "rc-status-dot";
                    target.appendChild(status);
                }
            }, { once: true });
            target.appendChild(image);
        } else {
            const fallback = document.createElement("span");
            fallback.textContent = initial;
            target.appendChild(fallback);
        }

        if (isOnline(details.online)) {
            const status = document.createElement("span");
            status.className = "rc-status-dot";
            target.appendChild(status);
        }

        if (sizeClass) {
            target.classList.add(sizeClass);
        }
    }

    function detailsFromCard(card) {
        return {
            receiverId: card.dataset.profileId,
            senderId: "<?php echo esc((string) $currentUserId, 'js'); ?>",
            name: card.dataset.profileName,
            role: card.dataset.profileRole,
            company: card.dataset.profileCompany,
            location: card.dataset.profileLocation,
            initial: card.dataset.profileInitial,
            avatar: card.dataset.profileAvatar,
            online: card.dataset.profileOnline,
            connected: card.dataset.profileConnected === "1",
            connectionId: card.dataset.profileConnectionId,
            connectionStatus: card.dataset.profileConnectionStatus,
            connectionDirection: card.dataset.profileConnectionDirection,
            isSelf: card.dataset.profileSelf === "1",
            socials: {
                facebook: card.dataset.socialFacebook,
                twitter: card.dataset.socialTwitter,
                instagram: card.dataset.socialInstagram,
                linkedin: card.dataset.socialLinkedin
            }
        };
    }

    function detailsFromMessageButton(button) {
        return {
            receiverId: button.dataset.attendeeId,
            senderId: button.dataset.senderId,
            name: button.dataset.attendeeName,
            role: button.dataset.attendeeRole,
            initial: button.dataset.attendeeInitial,
            avatar: button.dataset.attendeeAvatar,
            online: button.dataset.attendeeOnline
        };
    }

    function normalizeSocialUrl(network, value) {
        const cleanValue = text(value).trim();
        if (!cleanValue) {
            return "";
        }
        if (/^https?:\/\//i.test(cleanValue)) {
            return cleanValue;
        }
        return socialMeta[network].base + cleanValue.replace(/^@+/, "").replace(/^\/+/, "");
    }

    function renderProfileSocials(details) {
        profileSocials.innerHTML = "";
        profileSocialLock.hidden = details.connected;

        if (!details.connected) {
            return;
        }

        const links = Object.keys(socialMeta)
            .map(function(network) {
                return {
                    network: network,
                    url: normalizeSocialUrl(network, details.socials[network])
                };
            })
            .filter(function(item) {
                return item.url;
            });

        if (!links.length) {
            const empty = document.createElement("span");
            empty.className = "rc-profile-social-empty";
            empty.textContent = "No social links added yet.";
            profileSocials.appendChild(empty);
            return;
        }

        links.forEach(function(item) {
            const meta = socialMeta[item.network];
            const link = document.createElement("a");
            link.href = item.url;
            link.target = "_blank";
            link.rel = "noopener noreferrer";
            link.setAttribute("aria-label", meta.label);
            link.innerHTML = '<i class="fa ' + meta.icon + '" aria-hidden="true"></i>';
            profileSocials.appendChild(link);
        });
    }

    function setConnectButton(button, state, busy) {
        if (!button) {
            return;
        }

        const icon = button.querySelector("i");
        const label = button.querySelector("span");
        const connected = !!state.connected || state.connectionStatus === "accepted";
        const pendingSent = state.connectionStatus === "pending" && state.connectionDirection === "sent";
        const pendingReceived = state.connectionStatus === "pending" && state.connectionDirection === "received";
        let iconClass = "fa-user-plus";
        let text = "Connect";
        let muted = false;
        let disabled = false;

        if (busy) {
            iconClass = "fa-spinner";
            text = pendingReceived ? "Accepting..." : "Sending...";
            muted = true;
            disabled = true;
        } else if (state.isSelf) {
            iconClass = "fa-user";
            text = "You";
            muted = true;
            disabled = true;
        } else if (connected) {
            iconClass = "fa-check";
            text = "Connected";
            muted = true;
            disabled = true;
        } else if (pendingSent) {
            iconClass = "fa-clock-o";
            text = "Requested";
            muted = true;
            disabled = true;
        } else if (pendingReceived) {
            iconClass = "fa-check";
            text = "Accept";
        }

        button.classList.toggle("is-muted", muted);
        button.disabled = disabled;

        if (icon) {
            icon.className = "fa " + iconClass;
        }
        if (label) {
            label.textContent = text;
        }
    }

    function updateConnectionState(card, data) {
        if (!card || !data) {
            return;
        }

        card.dataset.profileConnected = data.is_connected ? "1" : "0";
        card.dataset.profileConnectionId = data.connection_id || "";
        card.dataset.profileConnectionStatus = data.connection_status || "";
        card.dataset.profileConnectionDirection = data.connection_direction || "";

        const details = detailsFromCard(card);
        setConnectButton(card.querySelector(".rc-connect-toggle"), details);

        if (activeProfile && activeProfile.receiverId === details.receiverId) {
            activeProfile.connected = details.connected;
            activeProfile.connectionId = details.connectionId;
            activeProfile.connectionStatus = details.connectionStatus;
            activeProfile.connectionDirection = details.connectionDirection;
            renderProfileSocials(activeProfile);
            setConnectButton(profileConnect, activeProfile);
        }
    }

    function sendConnectionRequest(card, button) {
        if (!card) {
            return;
        }

        const details = detailsFromCard(card);
        if (details.isSelf || details.connected || (details.connectionStatus === "pending" && details.connectionDirection === "sent")) {
            setConnectButton(button, details);
            return;
        }

        setConnectButton(button, details, true);

        fetch("<?php echo base_url('api/connections'); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                receiver_id: details.receiverId
            })
        })
            .then(function(response) {
                return response.json().catch(function() {
                    return {};
                }).then(function(payload) {
                    if (!response.ok || payload.status !== "success") {
                        throw new Error(payload.message || "connection-failed");
                    }
                    updateConnectionState(card, payload.data);
                });
            })
            .catch(function() {
                setConnectButton(button, details);
                const label = button?.querySelector("span");
                if (label) {
                    label.textContent = "Try again";
                }
            });
    }

    function openProfile(details) {
        closeDrawer();
        activeProfile = details;
        profileName.textContent = details.name || "Attendee";
        profileRole.textContent = details.role || "Attendee";
        profileCompany.textContent = details.company || "";
        profileLocation.textContent = details.location || "Conference portal";
        profileOnline.hidden = !isOnline(details.online);
        createAvatar(profileAvatar, details);
        renderProfileSocials(details);
        setConnectButton(profileConnect, details);

        profileDrawer.classList.add("is-open");
        profileBackdrop.classList.add("is-open");
        profileDrawer.setAttribute("aria-hidden", "false");
        window.setTimeout(function() {
            profileDrawer.querySelector("[data-rc-profile-close]")?.focus();
        }, 120);
    }

    function closeProfile() {
        profileDrawer.classList.remove("is-open");
        profileBackdrop.classList.remove("is-open");
        profileDrawer.setAttribute("aria-hidden", "true");
        activeProfile = null;
    }

    function renderMessages() {
        const threadMessages = activeThread ? (messages[activeThread.receiverId] || []) : [];
        messagesNode.innerHTML = "";

        if (!threadMessages.length) {
            const empty = document.createElement("div");
            empty.className = "rc-dm-empty";
            empty.textContent = "Start the conversation. Mention a session you both attended or what you're working on.";
            messagesNode.appendChild(empty);
            return;
        }

        threadMessages.forEach(function(item) {
            const row = document.createElement("div");
            row.className = "rc-dm-bubble-row" + (item.fromMe ? " is-me" : "");

            const bubble = document.createElement("div");
            bubble.className = "rc-dm-bubble" + (item.fromMe ? " is-me" : "") + (item.system ? " is-system" : "");
            bubble.textContent = item.message;

            row.appendChild(bubble);
            messagesNode.appendChild(row);
        });

        messagesNode.scrollTop = messagesNode.scrollHeight;
    }

    function openDrawer(details) {
        closeProfile();
        activeThread = details;
        nameNode.textContent = details.name || "Attendee";
        roleNode.textContent = details.role || "Attendee";
        createAvatar(avatar, details);
        renderMessages();
        drawer.classList.add("is-open");
        backdrop.classList.add("is-open");
        drawer.setAttribute("aria-hidden", "false");
        window.setTimeout(function() {
            input.focus();
        }, 120);
        loadThread();
    }

    function closeDrawer() {
        drawer.classList.remove("is-open");
        backdrop.classList.remove("is-open");
        drawer.setAttribute("aria-hidden", "true");
        activeThread = null;
    }

    function insertEmoji(emoji) {
        const start = input.selectionStart ?? input.value.length;
        const end = input.selectionEnd ?? start;
        input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
        const cursor = start + emoji.length;
        input.focus();
        input.setSelectionRange(cursor, cursor);
    }

    function loadThread() {
        if (!activeThread) {
            return Promise.resolve();
        }

        const thread = activeThread;
        const receiverId = Number(thread.receiverId || 0);
        if (!receiverId) {
            return Promise.resolve();
        }

        return fetch(messageThreadUrl + "/" + encodeURIComponent(String(receiverId)), {
            credentials: "same-origin"
        })
            .then(function(response) {
                return response.json().catch(function() {
                    return {};
                }).then(function(payload) {
                    if (!response.ok || payload.status !== "success") {
                        throw new Error("thread-load-failed");
                    }

                    messages[receiverId] = (Array.isArray(payload.data) ? payload.data : []).map(function(item) {
                        return {
                            id: item.id,
                            fromMe: Number(item.sender_id) === currentUserId,
                            message: item.message || ""
                        };
                    });

                    if (activeThread && Number(activeThread.receiverId) === receiverId) {
                        renderMessages();
                    }
                });
            })
            .catch(function() {
                // Keep the drawer usable. The next sync will retry automatically.
            });
    }

    function applySearch() {
        const query = (searchInput?.value || "").trim().toLowerCase();
        let visible = 0;

        attendeeCards.forEach(function(card) {
            const searchMatches = !query || card.dataset.search.includes(query);
            const tags = (card.dataset.tags || "").split("|");
            const filterMatches = activeFilter === "All"
                || (activeFilter === "Online now" && card.dataset.profileOnline === "1")
                || tags.includes(activeFilter);
            const matches = searchMatches && filterMatches;
            card.hidden = !matches;
            if (matches) {
                visible++;
            }
        });

        if (attendeeCount) {
            attendeeCount.textContent = visible;
        }
        if (attendeeEmpty) {
            attendeeEmpty.hidden = visible !== 0;
        }
    }

    function isInteractiveTarget(target) {
        return !!target.closest("button, a, input, textarea, select, [data-rc-profile-close], [data-rc-dm-close]");
    }

    if (searchForm) {
        searchForm.addEventListener("submit", function(event) {
            event.preventDefault();
            applySearch();
        });
    }

    if (searchInput) {
        searchInput.addEventListener("input", applySearch);
        applySearch();
    }

    attendeeFilters.forEach(function(button) {
        button.addEventListener("click", function() {
            activeFilter = button.dataset.attendeeFilter || "All";
            attendeeFilters.forEach(function(item) {
                item.classList.toggle("is-active", item === button);
            });
            applySearch();
        });
    });

    document.addEventListener("click", function(event) {
        const connectButton = event.target.closest(".rc-connect-toggle");
        if (connectButton) {
            const card = connectButton.closest("[data-attendee-card]");
            sendConnectionRequest(card, connectButton);
            return;
        }

        const messageButton = event.target.closest(".rc-message-toggle");
        if (messageButton) {
            openDrawer(detailsFromMessageButton(messageButton));
            return;
        }

        if (event.target.closest("[data-rc-dm-close]")) {
            closeDrawer();
            return;
        }

        if (event.target.closest("[data-rc-profile-close]")) {
            closeProfile();
            return;
        }

        if (event.target.closest("[data-profile-message]") && activeProfile) {
            openDrawer({
                receiverId: activeProfile.receiverId,
                senderId: activeProfile.senderId,
                name: activeProfile.name,
                role: activeProfile.role + (activeProfile.company ? " · " + activeProfile.company : ""),
                initial: activeProfile.initial,
                avatar: activeProfile.avatar,
                online: activeProfile.online
            });
            return;
        }

        if (event.target.closest("[data-profile-connect]") && activeProfile) {
            const card = document.querySelector('[data-attendee-card][data-profile-id="' + activeProfile.receiverId + '"]');
            sendConnectionRequest(card, profileConnect);
            return;
        }

        const card = event.target.closest("[data-attendee-card]");
        if (card && !isInteractiveTarget(event.target)) {
            openProfile(detailsFromCard(card));
        }
    });

    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            if (drawer.classList.contains("is-open")) {
                closeDrawer();
            }
            if (profileDrawer.classList.contains("is-open")) {
                closeProfile();
            }
            return;
        }

        if ((event.key === "Enter" || event.key === " ") && event.target.matches("[data-attendee-card]")) {
            event.preventDefault();
            openProfile(detailsFromCard(event.target));
        }
    });

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        if (!activeThread) {
            return;
        }

        const message = input.value.trim();
        if (!message) {
            return;
        }

        const receiverId = activeThread.receiverId;
        const sendButton = form.querySelector('button[type="submit"]');
        sendButton.disabled = true;
        sendButton.textContent = "Sending...";

        fetch(messageSendUrl, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                receiver_id: activeThread.receiverId,
                message: message
            })
        })
            .then(function(response) {
                return response.json().catch(function() {
                    return {};
                }).then(function(payload) {
                    if (!response.ok || (payload.status && payload.status !== "success")) {
                        throw new Error("send-failed");
                    }
                    input.value = "";
                    return loadThread();
                });
            })
            .catch(function() {
                const threadMessages = messages[receiverId] || [];
                threadMessages.push({
                    system: true,
                    message: "Message could not be sent. Please try again."
                });
                messages[receiverId] = threadMessages;
                if (activeThread && activeThread.receiverId === receiverId) {
                    renderMessages();
                }
            })
            .finally(function() {
                sendButton.disabled = false;
                sendButton.textContent = "Send";
            });
    });

    emojiToggle?.addEventListener("click", function() {
        const isOpen = !emojiPicker.hidden;
        emojiPicker.hidden = isOpen;
        emojiPicker.style.display = isOpen ? "none" : "grid";
        emojiToggle.setAttribute("aria-expanded", String(!isOpen));
    });

    emojiPicker?.addEventListener("click", function(event) {
        const button = event.target.closest("[data-rc-dm-emoji]");
        if (!button) return;
        insertEmoji(button.dataset.rcDmEmoji || "");
        emojiPicker.hidden = true;
        emojiPicker.style.display = "none";
        emojiToggle?.setAttribute("aria-expanded", "false");
    });

    document.addEventListener("click", function(event) {
        if (emojiPicker && !emojiPicker.hidden && !emojiPicker.contains(event.target) && !emojiToggle?.contains(event.target)) {
            emojiPicker.hidden = true;
            emojiPicker.style.display = "none";
            emojiToggle?.setAttribute("aria-expanded", "false");
        }
    });

    window.setInterval(function() {
        if (activeThread) {
            loadThread();
        }
    }, 3000);
})();
</script>
</body>
</html>
