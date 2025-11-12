<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:52
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        color: #fff;
        background: url('<?php echo asset_url('images/mobile-bg.png'); ?>') no-repeat center center fixed;
        background-size: cover;
        overflow-x: hidden;
    }

    .speakers-container {
        padding: 100px 1.5rem 120px;
        text-align: center;
        min-height: 100vh;
    }

    h3.speakers-title {
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    /* Grid */
    .speakers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 20px;
        justify-items: center;
    }

    /* Card */
    .speaker-card {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 20px 10px 70px;
        max-width: 200px;
        width: 100%;
        text-align: center;
        position: relative;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
        transition: transform 0.25s ease, box-shadow 0.3s ease;
    }

    .speaker-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
    }

    .speaker-photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
        margin-bottom: 10px;
    }

    .speaker-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #fff;
        margin-bottom: 5px;
        text-transform: capitalize;
    }

    .speaker-meta {
        font-size: 0.8rem;
        color: #ddd;
        opacity: 0.85;
        line-height: 1.3;
        min-height: 32px;
    }

    /* Button */
    .view-profile-btn {
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        width: 80%;
        border: none;
        border-radius: 25px;
        padding: 7px 18px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
        transition: transform 0.3s ease;
    }

    .view-profile-btn:hover {
        transform: translateX(-50%) scale(1.05);
    }

    /* Bottom Sheet Drawer */
    .profile-drawer {
        position: fixed;
        bottom: -100%;
        left: 0;
        width: 100%;
        background: #1c0027;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.6);
        transition: bottom 0.4s ease;
        z-index: 1000;
        padding: 25px 20px 40px;
        text-align: center;
        overflow-y: auto;
        max-height: 85vh;
    }

    .profile-drawer.active {
        bottom: 0;
    }

    .drawer-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease;
        z-index: 999;
    }

    .drawer-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .drawer-close {
        position: absolute;
        top: 10px;
        right: 20px;
        background: none;
        border: none;
        color: #FFD84D;
        font-size: 26px;
        font-weight: 700;
        cursor: pointer;
    }

    .drawer-photo {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        margin-top: 10px;
    }

    .drawer-name {
        font-weight: 700;
        color: #FFD84D;
        margin-top: 15px;
        font-size: 1.1rem;
    }

    .drawer-title {
        color: #ccc;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .drawer-bio {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        padding: 15px;
        font-size: 0.9rem;
        color: #eee;
        text-align: left;
        line-height: 1.6;
    }

    .drawer-sessions {
        margin-top: 20px;
        text-align: left;
    }

    .drawer-sessions h5 {
        color: #FFD84D;
        margin-bottom: 8px;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .drawer-sessions ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .drawer-sessions li {
        font-size: 0.85rem;
        color: #fff;
        margin-bottom: 6px;
        padding-left: 10px;
        position: relative;
    }

    .drawer-sessions li::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #EFB11E;
    }
</style>

<div class="speakers-container">
    <h3 class="speakers-title epr-text-purple">Speakers</h3>

    <div class="speakers-grid">
        <?php if (!empty($speakers)): ?>
            <?php foreach ($speakers as $speaker): ?>
                <?php
                    $image = base_url('uploads/speaker_pictures/' . $speaker['speaker_photo']);
                    $speakerId = $speaker['speaker_id'];
                ?>
                <div class="speaker-card">
                    <img src="<?php echo $image; ?>"
                         alt="<?php echo esc($speaker['speaker_name']); ?>"
                         class="speaker-photo"
                         onerror="this.src='<?php echo asset_url('images/user.png'); ?>';">
                    <div class="speaker-name"><?php echo esc($speaker['speaker_name']); ?></div>
                    <div class="speaker-meta">
                        <?php echo esc($speaker['speaker_title']); ?><br>
                        <small><?php echo esc($speaker['speaker_company']); ?></small>
                    </div>
                    <button class="view-profile-btn open-drawer epr-btn-two"
                            data-name="<?php echo esc($speaker['speaker_name']); ?>"
                            data-title="<?php echo esc($speaker['speaker_title']); ?>"
                            data-company="<?php echo esc($speaker['speaker_company']); ?>"
                            data-bio="<?php echo nl2br(esc($speaker['bio'] ?? 'No biography available.')); ?>"
                            data-sessions='<?php echo json_encode($speaker['sessions'] ?? []); ?>'
                            data-photo="<?php echo $image; ?>">
                        View Profile
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center mt-5 text-white">No speakers found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Drawer Overlay -->
<div class="drawer-overlay" id="drawerOverlay"></div>

<!-- Drawer -->
<div class="profile-drawer" id="profileDrawer">
    <button class="drawer-close" id="closeDrawer">&times;</button>
    <img id="drawerPhoto" class="drawer-photo" src="" alt="">
    <h4 id="drawerName" class="drawer-name"></h4>
    <p id="drawerTitle" class="drawer-title"></p>
    <div id="drawerBio" class="drawer-bio"></div>
    <div id="drawerSessions" class="drawer-sessions"></div>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const drawer = document.getElementById("profileDrawer");
    const overlay = document.getElementById("drawerOverlay");
    const closeDrawer = document.getElementById("closeDrawer");
    const nameEl = document.getElementById("drawerName");
    const titleEl = document.getElementById("drawerTitle");
    const bioEl = document.getElementById("drawerBio");
    const sessionsEl = document.getElementById("drawerSessions");
    const photoEl = document.getElementById("drawerPhoto");

    document.querySelectorAll(".open-drawer").forEach(btn => {
        btn.addEventListener("click", () => {
            nameEl.textContent = btn.dataset.name;
            titleEl.textContent = `${btn.dataset.title}, ${btn.dataset.company}`;
            bioEl.innerHTML = btn.dataset.bio;
            photoEl.src = btn.dataset.photo;

            const sessions = JSON.parse(btn.dataset.sessions);
            sessionsEl.innerHTML = "";
            if (sessions.length > 0) {
                let html = "<h5>Sessions:</h5><ul>";
                sessions.forEach(s => {
                    html += `<li>${s.sessions_name}</li>`;
                });
                html += "</ul>";
                sessionsEl.innerHTML = html;
            }

            drawer.classList.add("active");
            overlay.classList.add("active");
        });
    });

    function closeProfileDrawer() {
        drawer.classList.remove("active");
        overlay.classList.remove("active");
    }

    closeDrawer.addEventListener("click", closeProfileDrawer);
    overlay.addEventListener("click", closeProfileDrawer);
});
</script>
