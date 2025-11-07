<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 16:31
 */


echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$attendee_id = session('attendee_id');
$country = session('reg_country') ?? 'Nigeria';
$plan = session('plan') ?? 1; // 1 = Free, 2 = Paid
$timezone = session('user_timezone') ?? 'Africa/Lagos';
?>

<style>
    .nav-pills .nav-link.active {
        background-color: #9D0F82;
        color: #fff;
        border-radius: 8px;
    }

    .nav-tabs .nav-link {
        border: 2px solid #9D0F82;
        color: #9D0F82;
        border-radius: 8px;
    }

    .session-item {
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }

    .session-item.locked {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .lock-icon {
        width: 18px;
        margin-left: 5px;
    }

    .btn-epr-purple {
        background-color: #9D0F82;
        color: white;
        border: none;
    }

    .btn-epr-orange {
        background-color: #EFB11E;
        color: #fff;
        border: none;
    }

    #sessionsContainer{
        background-color: #ffffff;
        border: #9d0f82 7px solid;
    }
</style>

<div class="container py-5">
    <h4 class="text-epr mb-4" style="color: #9D0F82;">AGENDA</h4>

    <ul class="nav nav-tabs nav-pills nav-fill mb-4" id="dayTabs"></ul>

    <div class="tab-content" id="sessionsContainer"></div>
</div>

<?php
echo module_view('Web', 'includes/scripts');
?>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
        const apiBase = "<?php echo rtrim(base_url('api'), '/'); ?>";
        const apiKey  = "<?php echo env('api.securityKey'); ?>";
        const attendeeId = "<?php echo $attendee_id; ?>";
        const userTimezone = "<?php echo $timezone; ?>";
        const userPlan = Number("<?php echo $plan; ?>");

        async function apiGet(endpoint) {
            const res = await fetch(`${apiBase}/${endpoint}`, {
                headers: { "X-API-KEY": apiKey }
            });
            return res.json();
        }

        try {
            // ✅ 1. Get live conference
            const confRes = await apiGet("conferences/live");
            const conf = confRes?.data;
            if (!conf) return;

            // ✅ 2. Fetch sessions by conference
            const sessRes = await apiGet(`sessions/${conf.conference_id}`);
            console.log("Raw session response:", sessRes);

            let sessions = [];
            if (Array.isArray(sessRes?.data)) {
                sessions = sessRes.data;
            } else if (Array.isArray(sessRes?.data?.sessions)) {
                sessions = sessRes.data.sessions;
            } else if (sessRes?.data && typeof sessRes.data === "object") {
                sessions = Object.values(sessRes.data).flat();
            }

            console.log("Normalized sessions:", sessions);

            if (!sessions.length) {
                document.getElementById("sessionsContainer").innerHTML =
                    "<p class='text-center text-muted mt-4'>No sessions found for this conference.</p>";
                return;
            }

            // ✅ 3. Group sessions by date
            const grouped = sessions.reduce((acc, s) => {
                const dateKey = (s.event_date || "").split("T")[0];
                (acc[dateKey] ||= []).push(s);
                return acc;
            }, {});
            console.log("Grouped sessions:", grouped);

            // ✅ 4. Build tabs and content dynamically
            const tabList = document.getElementById("dayTabs");
            const content = document.getElementById("sessionsContainer");
            let first = true;

            for (const [date, sessList] of Object.entries(grouped)) {
                const dateLabel = new Date(date).toLocaleDateString("en-US", {
                    weekday: "long", month: "long", day: "numeric", year: "numeric"
                });

                const tab = document.createElement("li");
                tab.className = "nav-item";
                tab.innerHTML = `
                <a class="nav-link ${first ? 'active' : ''}" data-toggle="tab"
                   href="#day-${date.replaceAll('-', '')}" role="tab">${dateLabel}</a>`;
                tabList.appendChild(tab);

                const pane = document.createElement("div");
                pane.className = `tab-pane fade ${first ? 'show active' : ''}`;
                pane.id = `day-${date.replaceAll('-', '')}`;
                pane.innerHTML = sessList.map(renderSession).join("");
                content.appendChild(pane);

                first = false;
            }

            // ✅ 5. Render individual session cards (your HTML styling stays intact)
            function renderSession(s) {
                const startUTC = new Date(`${s.event_date}T${s.start_time}Z`);
                const endUTC   = new Date(`${s.event_date}T${s.end_time}Z`);
                const start = startUTC.toLocaleTimeString([], {
                    hour: '2-digit', minute: '2-digit', timeZone: userTimezone
                });
                const end = endUTC.toLocaleTimeString([], {
                    hour: '2-digit', minute: '2-digit', timeZone: userTimezone
                });

                const isPaidSession = s.access_level == 2;
                const canAccess = userPlan >= s.access_level;
                const lockIcon = isPaidSession && !canAccess
                    ? `<img src="<?= asset_url('images/social-icons-outline/paid-session.svg');?>" class="lock-icon" alt="Locked">`
                    : "";

                const speakerList = (s.speakers ?? []).map(sp => sp.speaker_name).join(", ") || "TBA";

                // 👇 You can style this however you like — only the data logic matters
                return `
                <div class="session-item ${!canAccess ? 'locked' : ''}">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h6><strong>${s.sessions_name}</strong>${lockIcon}</h6>
                            <small>${start} - ${end} (${userTimezone})</small><br>
                            <small>Speakers: ${speakerList}</small>
                        </div>
                        <div class="mt-2 mt-md-0">
                            ${canAccess
                    ? `<button class="btn btn-epr-purple btn-sm" onclick="viewSession(${s.sessions_id}, ${s.access_level}, ${s.is_zoom})">View Session</button>`
                    : `<button class="btn btn-light btn-sm" disabled>View Session</button>`}
                        </div>
                    </div>
                </div>`;
            }

        } catch (err) {
            console.error("Error loading sessions:", err);
        }
    });
</script>

<script>
    function viewSession(id, accessLevel, isZoom) {
        const myPlan = Number("<?php echo $plan; ?>");
        if (myPlan < accessLevel) {
            alert("Please upgrade your ticket to access this session.");
            return;
        }
        if (isZoom == 1)
            location.href = "<?= base_url('attendee/events/zoom/view/'); ?>" + id;
        else
            location.href = "<?= base_url('attendee/events/view/'); ?>" + id;
    }
</script>

</body>
</html>
