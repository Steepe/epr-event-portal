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
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #fafafa;
    }

    /* 🔹 Heading */
    h4.text-epr {
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* 🔹 Tabs / Pills Styling */
    .nav-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        border: none;
        margin-left: 3px;
        position: relative;
        top: 5px;
        z-index: -99999;
    }

    .nav-pills .nav-link {
        border: 2px solid #9e3383;
        color: #9D0F82;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.25s ease;
    }

    .nav-pills .nav-link.active {
        background: url("<?php echo asset_url('images/button-bg-short.png'); ?>") repeat-y;
        color: #fff;
        box-shadow: 0 3px 10px rgba(157, 15, 130, 0.3);
    }

    .nav-pills .nav-link:hover {
        background-color: #f8e9f5;
    }

    /* 🔹 Sessions Container */
    #sessionsContainer {
        background-color: #fff;
        border: 6px solid #9D0F82;
        border-radius: 12px;
        padding: 25px;
        min-height: 400px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    /* 🔹 Each Session */
    .session-item {
        padding: 15px 10px;
        border-bottom: 1px solid #cacaca;
        transition: all 0.2s ease;
    }

    .session-item:last-child {
        border-bottom: none;
    }

    .session-item:hover {
        background-color: #faf3f9;
    }

    .session-item.locked {
        opacity: 0.7;
        cursor: not-allowed;
        border-bottom: 1px solid #cacaca;
    }

    .session-item h6 {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .session-item small {
        color: #555;
    }

    .lock-icon {
        width: 18px;
        margin-left: 6px;
        vertical-align: middle;
    }

    /* 🔹 Buttons */
    .btn-epr-purple {
        background-color: #9D0F82;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        padding: 6px 12px;
    }

    .btn-epr-purple:hover {
        background-color: #7a0e69;
    }

    .btn-light {
        color: #aaa;
        border-radius: 6px;
        font-size: 13px;
    }

    .text-muted {
        color: #888 !important;
    }

    #dayTabs .nav-item{
    }

</style>

<div class="container py-5">
    <h4 class="text-epr mb-4 text-left" style="color: #9D0F82;">AGENDA</h4>

    <!-- Date Tabs -->
    <ul class="nav nav-pills" id="dayTabs"></ul>

    <!-- Session List -->
    <div class="tab-content" id="sessionsContainer"></div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>

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
        // 1️⃣ Get live conference
        const confRes = await apiGet("conferences/live");
        const conf = confRes?.data;
        if (!conf) return;

        // 2️⃣ Fetch sessions by conference
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

        // 3️⃣ Group sessions by date
        const grouped = sessions.reduce((acc, s) => {
            const dateKey = (s.event_date || "").split("T")[0];
            (acc[dateKey] ||= []).push(s);
            return acc;
        }, {});
        console.log("Grouped sessions:", grouped);

        // 4️⃣ Build date tabs dynamically
        const tabList = document.getElementById("dayTabs");
        const content = document.getElementById("sessionsContainer");
        let first = true;
        console.log()

        for (const [date, sessList] of Object.entries(grouped)) {
            const dateLabel = new Date(date).toLocaleDateString("en-US", {
                month: "long", day: "numeric", year: "numeric"
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

        // 5️⃣ Session rendering
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

            return `
                <div class="session-item ${!canAccess ? 'locked' : ''}">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <div>
                            <h6><strong>${s.sessions_name}</strong>${lockIcon}</h6>
                            <span class="font-12">${start} - ${end} (${userTimezone})</span><br>
                            <span class="font-12">Speakers: ${speakerList}</span>
                        </div>
                        <div class="mt-2 mt-md-0">
                            ${canAccess
                    ? `<a href="<?= base_url('attendees/sessions/'); ?>${s.sessions_id}"
   class="btn btn-epr-purple btn-sm">
   View Session
</a>
`
                    : `<button class="btn btn-light btn-sm" disabled>Locked</button>`}
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
