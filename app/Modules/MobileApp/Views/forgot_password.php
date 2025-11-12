<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 14:28
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
body {
    background: url('<?php echo asset_url('images/mobile-bg.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    overflow-x: hidden;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: -1;
}

.forgot-container {
    width: 90%;
    max-width: 400px;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 16px;
    padding: 40px 25px;
    box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
    text-align: center;
    backdrop-filter: blur(6px);
    margin-top: 70px;
}

.forgot-container img {
    width: 180px;
    margin-bottom: 25px;
}

.forgot-container h2 {
    font-weight: 700;
    text-transform: uppercase;
    color: #fff;
    margin-bottom: 10px;
    font-size: 20px;
    letter-spacing: 1px;
}

.forgot-container p {
    font-size: 13px;
    color: #ddd;
    margin-bottom: 25px;
}

.forgot-container input {
    width: 100%;
    padding: 12px 14px;
    margin-bottom: 20px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    color: #fff;
    font-size: 13px;
    outline: none;
    transition: 0.3s ease;
}

.forgot-container input:focus {
    border-color: #EFB11E;
    background: rgba(255, 255, 255, 0.15);
}

.epr-btn-four {
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 25px;
    padding: 10px 25px;
    width: 100%;
    font-size: 14px;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(157, 15, 130, 0.3);
}
.epr-btn-four:hover {
    transform: scale(1.05);
    opacity: 0.9;
}

.back-link {
    display: block;
    margin-top: 15px;
    font-size: 13px;
    color: #EFB11E;
    text-decoration: none;
}
.back-link:hover { text-decoration: underline; }

/* Toast notification */
.toast-box {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 3000;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.toast {
    min-width: 250px;
    max-width: 350px;
    color: #fff;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 13px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.25);
    opacity: 0;
    transform: translateY(20px);
    transition: all .3s ease;
}
.toast.show { opacity: 1; transform: translateY(0); }
.toast-success { background: linear-gradient(to right, #9D0F82, #D8198E); }
.toast-error   { background: #EFB11E; color: #000; font-weight: 500; }
</style>

<div class="forgot-container">
    <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Global Logo">
    <h2>Forgot Password</h2>
    <p>Enter your registered email below and we’ll send you a reset link.</p>

    <form id="ForgotForm">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" class="epr-btn-four">Send Reset Link</button>
    </form>
    <a href="<?php echo base_url('mobile/login'); ?>" class="back-link">Back to Login</a>
</div>

<div id="toastBox" class="toast-box"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("ForgotForm");
    const toastBox = document.getElementById("toastBox");

    const showToast = (message, type = "success") => {
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toastBox.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add("show"));
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    };

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form));
        data.platform = "mobile"; // 🔹 Let API know it’s from mobile

        const btn = form.querySelector("button");
        btn.disabled = true;
        btn.textContent = "Sending...";

        try {
            const res = await fetch("<?php echo base_url('api/password/forgot'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();
            if (result.status === "success") {
                showToast(result.message || "Reset link sent to your email.", "success");
                form.reset();
            } else {
                showToast(result.message || "Unable to send reset link.", "error");
            }
        } catch (err) {
            showToast("Network error. Please try again later.", "error");
        } finally {
            btn.disabled = false;
            btn.textContent = "Send Reset Link";
        }
    });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
