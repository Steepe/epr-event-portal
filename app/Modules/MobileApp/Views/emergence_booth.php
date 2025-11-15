<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 12:01
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
        text-align: center;
    }

    .emergence-container {
        min-height: 100vh;
        padding: 100px 1.5rem 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .emergence-title {
        font-weight: 700;
        color: #f3bb1a;
        margin-bottom: 2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-shadow: 0 0 10px rgba(255, 216, 77, 0.5);
    }

    .emergence-grid {
        display: flex;
        flex-direction: column;
        gap: 35px;
        align-items: center;
        width: 100%;
    }

    .emergence-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        width: 160px;
    }

    .emergence-item:hover {
        transform: scale(1.05);
    }

    .emergence-item img {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.4);
        transition: all 0.3s ease;
    }

    .emergence-item:hover img {
        box-shadow: 0 0 25px rgba(239, 177, 30, 0.6);
    }

    .emergence-item span {
        margin-top: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #fff;
    }

    @media (min-width: 480px) {
        .emergence-grid {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
        }

        .emergence-item {
            width: 180px;
        }

        .emergence-item img {
            width: 150px;
            height: 150px;
        }

        .emergence-item span {
            font-size: 0.9rem;
        }
    }

    /* 🔽 Bottom Slide Modal */
    .support-modal {
        position: fixed;
        bottom: -100%;
        left: 0;
        width: 100%;
        background: #150020;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.5);
        color: #fff;
        transition: bottom 0.4s ease;
        z-index: 9999;
        padding: 25px 20px;
        text-align: left;
    }

    .support-modal.active {
        bottom: 0;
    }

    .support-modal h4 {
        color: #EFB11E;
        text-align: center;
        margin-bottom: 15px;
    }

    .support-modal form input,
    .support-modal form textarea {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #9D0F82;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        padding: 10px;
        margin-bottom: 10px;
        font-size: 0.9rem;
        outline: none;
    }

    .support-modal form textarea {
        resize: none;
    }

    .support-modal form button {
        border: none;
        border-radius: 25px;
        color: #fff;
        padding: 10px 20px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .support-modal form button:hover {
        transform: scale(1.05);
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 18px;
        color: #f3bb1a;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="emergence-container">
    <h3 class="emergence-title">Emergence Booth</h3>

    <div class="emergence-grid">
        <!-- Need Assistance -->
        <button id="openSupportModal" class="emergence-item" style="background:none;border:none;">
            <img src="<?php echo asset_url('images/assistance.png'); ?>" alt="Need Assistance">
            <span>Need Assistance?</span>
        </button>


        <!-- Join The Community -->
        <a href="https://community.eprglobal.com" target="_blank" class="emergence-item">
            <img src="<?php echo asset_url('images/community.png'); ?>" alt="Join the Community">
            <span>Join the Community</span>
        </a>
    </div>
</div>

<!-- 💌 Support Modal -->
<div id="supportModal" class="support-modal">
    <span class="close-modal">&times;</span>
    <h4>Contact Support</h4>
    <form id="supportForm">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" rows="4" placeholder="Describe your issue..." required></textarea>
        <button class="epr-btn-two" type="submit">Send Message</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("supportModal");
    const openBtn = document.getElementById("openSupportModal");
    const closeBtn = document.querySelector(".close-modal");
    const form = document.getElementById("supportForm");

    openBtn.addEventListener("click", () => modal.classList.add("active"));
    closeBtn.addEventListener("click", () => modal.classList.remove("active"));
    window.addEventListener("click", (e) => {
        if (e.target === modal) modal.classList.remove("active");
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const res = await fetch("<?php echo site_url('mobile/emergence/sendSupportEmail'); ?>", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        const json = await res.json();
        if (json.status === "success") {
            alert("✅ Your message has been sent successfully!");
            form.reset();
            modal.classList.remove("active");
        } else {
            alert("⚠️ Failed to send message. Please try again.");
        }
    });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
