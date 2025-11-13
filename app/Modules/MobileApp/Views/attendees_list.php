<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 09:14
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

    .attendees-container {
        min-height: 100vh;
        padding: 100px 1.2rem 120px;
        text-align: center;
    }

    h3.attendees-title {
        font-weight: 700;
        color: #f3bb1a;
        margin-bottom: 1rem;
        text-shadow: 0 0 10px rgba(255, 216, 77, 0.4);
    }

    .search-box {
        margin-bottom: 1.8rem;
    }

    .search-box input {
        width: 90%;
        max-width: 400px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 30px;
        padding: 10px 18px;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        outline: none;
        font-size: 0.9rem;
    }

    .search-box input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .attendee-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        justify-items: center;
    }

    .attendee-card {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px 10px 35px;
        width: 100%;
        max-width: 180px;
        text-align: center;
        position: relative;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        transition: transform 0.25s ease, box-shadow 0.3s ease;
    }

    .attendee-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.4);
    }

    .attendee-card img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .attendee-name {
        font-weight: 600;
        color: #fff;
        font-size: 0.95rem;
        margin-bottom: 4px;
        text-transform: capitalize;
    }

    .attendee-meta {
        font-size: 0.75rem;
        color: #ddd;
        margin-bottom: 10px;
        opacity: 0.85;
        min-height: 30px;
    }

    .say-hello-btn {
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 25px;
        padding: 6px 18px;
        font-size: 0.8rem;
        transition: all 0.3s ease;
        position: absolute;
        bottom: 12px;
        left: 50%;
        transform: translateX(-50%);
        width: 75%;
    }

    .say-hello-btn:hover {
        transform: translateX(-50%) scale(1.05);
    }

    /* === Bottom Sheet Message Drawer === */
    .message-drawer {
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
    }

    .message-drawer.active {
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
        color: #f3bb1a;
        font-size: 24px;
        font-weight: 700;
        cursor: pointer;
    }

    .message-drawer textarea {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #9D0F82;
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        padding: 12px;
        font-size: 0.9rem;
        margin-top: 40px;
        resize: none;
        height: 120px;
    }

    .drawer-send-btn {
        background: linear-gradient(90deg, #9D0F82, #EFB11E);
        border: none;
        color: #fff;
        border-radius: 25px;
        padding: 10px 30px;
        margin-top: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .drawer-send-btn:hover {
        transform: scale(1.05);
        background: linear-gradient(90deg, #EFB11E, #9D0F82);
    }

    @media (max-width: 480px) {
        .attendee-card img { width: 70px; height: 70px; }
        .attendee-card { max-width: 180px; }
    }
</style>

<div class="attendees-container">
    <h3 class="attendees-title">Attendees</h3>

    <form class="search-box" method="get">
        <input type="text" name="q" placeholder="Search by name, company, or position" value="<?php echo esc($search ?? ''); ?>">
    </form>

    <div class="attendee-grid">
        <?php if (!empty($attendees)): ?>
            <?php foreach ($attendees as $attendee): ?>
                <?php
                    $image = !empty($attendee['profile_picture'])
                        ? base_url('uploads/attendee_pictures/' . $attendee['profile_picture'])
                        : asset_url('images/default_pics/' . strtoupper(substr($attendee['firstname'], 0, 1)) . '3.png');
                ?>
                <div class="attendee-card">
                    <img src="<?php echo $image; ?>" alt="<?php echo esc($attendee['firstname']); ?>">
                    <div class="attendee-name"><?php echo esc($attendee['firstname'] . ' ' . $attendee['lastname']); ?></div>
                    <div class="attendee-meta"><?php echo esc($attendee['position'] ?? ''); ?><?php if (!empty($attendee['company'])) echo ', ' . esc($attendee['company']); ?></div>
                    <button class="say-hello-btn open-drawer epr-btn-two" data-id="<?php echo $attendee['attendee_id']; ?>" data-name="<?php echo esc($attendee['firstname']); ?>">Say Hello</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center mt-5 text-white">No attendees found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Drawer Overlay -->
<div class="drawer-overlay" id="drawerOverlay"></div>

<!-- Drawer -->
<div class="message-drawer" id="messageDrawer">
    <button class="drawer-close" id="closeDrawer">&times;</button>
    <h4 id="drawerTitle" style="margin-bottom:10px; color:#f3bb1a;">Send a Message</h4>
    <textarea id="drawerMessage" placeholder="Type your message..." required></textarea>
    <button class="drawer-send-btn" id="drawerSend">Send</button>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const drawer = document.getElementById("messageDrawer");
        const overlay = document.getElementById("drawerOverlay");
        const closeDrawer = document.getElementById("closeDrawer");
        const sendBtn = document.getElementById("drawerSend");
        const messageInput = document.getElementById("drawerMessage");
        const drawerTitle = document.getElementById("drawerTitle");
        let receiverId = null;

        const apiKey = "<?php echo env('api.securityKey'); ?>";
        const apiUrl = "<?php echo base_url('api/messages/send'); ?>";
        const senderId = "<?php echo session('user_id'); ?>";

        // 🟣 Open Drawer
        document.querySelectorAll(".open-drawer").forEach(btn => {
            btn.addEventListener("click", () => {
                receiverId = btn.dataset.id;
                const name = btn.dataset.name;
                drawerTitle.textContent = `Say Hello to ${name}`;
                drawer.classList.add("active");
                overlay.classList.add("active");
                messageInput.value = "";
            });
        });

        // 🔻 Close Drawer
        function closeMessageDrawer() {
            drawer.classList.remove("active");
            overlay.classList.remove("active");
        }
        closeDrawer.addEventListener("click", closeMessageDrawer);
        overlay.addEventListener("click", closeMessageDrawer);

        // 🚀 Send Message
        sendBtn.addEventListener("click", async () => {
            const message = messageInput.value.trim();
            if (!message) {
                alert("⚠️ Please type a message first.");
                return;
            }

            const data = {
                sender_id: senderId,
                receiver_id: receiverId,
                message: message
            };

            try {
                const res = await fetch(apiUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-API-KEY": apiKey
                    },
                    body: JSON.stringify(data)
                });

                const json = await res.json();

                if (json.status === "success") {
                    alert("✅ Message sent successfully!");
                    closeMessageDrawer();
                } else {
                    alert("⚠️ " + (json.message || "Message could not be sent."));
                }
            } catch (err) {
                alert("❌ Network or server error while sending the message.");
                console.error(err);
            }
        });
    });
</script>
