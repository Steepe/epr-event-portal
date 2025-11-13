<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 12:45
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        background: url('<?php echo asset_url('images/mobile-bg.png'); ?>') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Inter', 'Poppins', sans-serif;
        color: #fff;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .register-container {
        width: 90%;
        max-width: 420px;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 16px;
        padding: 30px 25px;
        text-align: center;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.4);
        margin-top: 70px;
    }

    .register-container img {
        width: 160px;
        margin-bottom: 1rem;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        padding: 10px 15px;
        width: 100%;
        color: #fff;
        font-size: 0.9rem;
        margin-bottom: 12px;
        outline: none;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .form-control:focus {
        border-color: #f3bb1a;
        background: rgba(255, 255, 255, 0.15);
    }

    select.form-control {
        color: #fff;
    }

    .register-container button {
        border: none;
        border-radius: 30px;
        padding: 12px;
        width: 100%;
        font-weight: 600;
        color: #fff;
        font-size: 1rem;
        margin-top: 10px;
        transition: all 0.3s ease;
    }

    .register-container button:hover {
        transform: scale(1.05);
    }

    .alt-link {
        margin-top: 1rem;
        font-size: 0.85rem;
        color: #fff;
    }

    .alt-link a {
        color: #f3bb1a;
        text-decoration: none;
        font-weight: 600;
    }

    .alt-link a:hover {
        text-decoration: underline;
    }

    /* Toast notifications */
    #toastBox {
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
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        opacity: 0;
        transform: translateY(20px);
        transition: all .3s ease;
        text-align: center;
    }

    .toast.show { opacity: 1; transform: translateY(0); }
    .toast-success { background: linear-gradient(90deg, #9D0F82, #EFB11E); }
    .toast-error { background: #EFB11E; color: #000; font-weight: 600; }

    @media (max-width: 480px) {
        .register-container { padding: 25px 18px; margin-top: 70px;}
        .form-control { font-size: 0.85rem; padding: 8px 12px; }
        .register-container button { font-size: 0.9rem; padding: 10px; }
    }
</style>

<div class="register-container">
    <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Logo">

    <form id="RegistrationForm">
        <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
        <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
        <input type="email" class="form-control" name="email" placeholder="Email Address" required>
        <input type="text" class="form-control" name="telephone" placeholder="Mobile Number">

        <select class="form-control" name="country" required>
            <option value="">Select Country</option>
            <?php if (!empty($countries)): ?>
                <?php foreach ($countries as $country): ?>
                    <option value="<?php echo $country['country_name']; ?>">
                        <?php echo $country['country_name']; ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <input type="text" class="form-control" name="state" placeholder="State" required>
        <input type="text" class="form-control" name="city" placeholder="City" required>
        <input type="password" id="password" class="form-control" name="password" placeholder="Password" required>
        <input type="password" id="conf_password" class="form-control" placeholder="Confirm Password" required>

        <select class="form-control" name="hear_about">
            <option value="">How did you hear about us?</option>
            <option>Google</option>
            <option>Facebook</option>
            <option>Twitter</option>
            <option>Instagram</option>
            <option>Email</option>
            <option>Word of Mouth</option>
        </select>

        <button type="submit" class="epr-btn-four">Register</button>
    </form>

    <p class="alt-link">
        Already have an account? <a href="<?php echo site_url('mobile/login'); ?>">Sign In</a>
    </p>
</div>

<div id="toastBox"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("RegistrationForm");
    const toastBox = document.getElementById("toastBox");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("conf_password");

    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toastBox.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add("show"));
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    }

    confirmPassword.addEventListener("input", () => {
        if (password.value !== confirmPassword.value)
            confirmPassword.setCustomValidity("Passwords do not match");
        else confirmPassword.setCustomValidity("");
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = Object.fromEntries(new FormData(form));
        const btn = form.querySelector("button");
        btn.disabled = true;
        btn.textContent = "Registering...";

        try {
            const response = await fetch("<?php echo base_url('api/register'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            if (result.status === "success") {
                showToast("Registration successful! Redirecting...", "success");
                setTimeout(() => window.location.href = "<?php echo site_url('mobile/login'); ?>", 3000);
            } else {
                showToast(result.message || "Registration failed. Please try again.", "error");
            }
        } catch {
            showToast("Network error. Please try again later.", "error");
        } finally {
            btn.disabled = false;
            btn.textContent = "Register";
        }
    });
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
