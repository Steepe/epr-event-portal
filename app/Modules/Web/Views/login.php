<?php echo module_view('Web', 'includes/auth_header'); ?>

<body class="rc-body">
<div class="rc-stage">
    <div class="rc-stage-inner">
        <form id="LoginForm" class="rc-auth-screen" data-screen-label="Login">
            <img class="rc-event-logo" src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Events">

            <div class="rc-brand-stack">
                <span class="rc-kicker">Emergence Conference &middot; 2026</span>
                <h1 style="margin:0;line-height:0">
                    <img class="rc-accelerate-logo"
                         src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>"
                         alt="Accelerate: Building sustainable systems for execution">
                </h1>
            </div>

            <div class="rc-auth-card">
                <div class="rc-card-title">Login to continue</div>

                <div class="rc-field">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <input class="rc-input" type="email" id="email" name="email" placeholder="Enter Email" required>
                </div>

                <div class="rc-field">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input class="rc-input" type="password" id="password" name="password" placeholder="Enter Password" required>
                </div>

                <button type="submit" class="rc-btn">Login</button>

                <div class="rc-link-row">
                    <a id="openReset" href="<?php echo site_url('attendees/forgot-password'); ?>" role="button">Forgot Password?</a>
                    <span>No account? <a href="<?php echo base_url('attendees/register'); ?>">Register</a></span>
                </div>
            </div>
        </form>

        <div id="resetPasswordModal" class="rc-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="resetPasswordTitle" aria-hidden="true">
            <div class="rc-reset-card">
                <div class="rc-reset-head">
                    <span id="resetPasswordTitle" class="rc-reset-title">Reset Password</span>
                    <button type="button" class="rc-modal-close" id="closeReset" data-rc-close-overlay aria-label="Close">&times;</button>
                </div>
                <form id="ResetForm" class="rc-reset-body">
                    <p>Enter your registered email. A reset link will be sent to you.</p>
                    <input class="rc-input" type="email" name="email" placeholder="Enter Email" aria-label="Email for password reset" autocomplete="email" required>
                    <button type="submit" class="rc-btn" style="align-self:center;width:220px;height:44px;font-size:14px;letter-spacing:0;text-transform:none;">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="rc-toast"></div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toast = document.getElementById("toast");
    const resetModal = document.getElementById("resetPasswordModal");
    const resetForm = document.getElementById("ResetForm");
    const closeReset = () => {
        resetModal.classList.remove("is-open");
        resetModal.setAttribute("aria-hidden", "true");
        document.getElementById('openReset').focus();
    };

    resetModal.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            event.preventDefault();
            event.stopPropagation();
            closeReset();
        } else if (event.key === 'Tab') {
            const controls = [...resetModal.querySelectorAll('button:not(:disabled), input:not(:disabled)')];
            const first = controls[0], last = controls[controls.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    const showToast = (msg, type = "success") => {
        toast.className = "rc-toast is-open" + (type === "danger" || type === "error" ? " is-error" : "");
        toast.textContent = msg;
        setTimeout(() => toast.classList.remove("is-open"), 3500);
    };

    document.getElementById("openReset").addEventListener("click", (event) => {
        event.preventDefault();
        resetModal.classList.add("is-open");
        resetModal.setAttribute("aria-hidden", "false");
        resetForm.querySelector('input').focus();
    });

    if (new URLSearchParams(window.location.search).get('reset') === '1') {
        document.getElementById('openReset').click();
    }

    document.getElementById("closeReset").addEventListener("click", closeReset);

    resetModal.addEventListener("click", (event) => {
        if (event.target === resetModal) {
            closeReset();
        }
    });

    document.getElementById("LoginForm").addEventListener("submit", async e => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target));
        const btn = e.target.querySelector("button[type='submit']");
        btn.disabled = true;
        btn.textContent = "Signing in...";

        try {
            const res = await fetch("<?php echo base_url('api/login'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();
            if (result.status === "success") {
                showToast("Login successful.");
                setTimeout(() => window.location.href = "<?php echo base_url('attendees/start'); ?>", 900);
            } else {
                showToast(result.message || "Invalid credentials.", "danger");
            }
        } catch (error) {
            showToast("Connection error. Please try again.", "danger");
        } finally {
            btn.disabled = false;
            btn.textContent = "Login";
        }
    });

    resetForm.addEventListener("submit", async e => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target));
        const btn = e.target.querySelector("button[type='submit']");
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
                showToast(result.message || "Please check your inbox and spam folder for the reset link.");
                closeReset();
                e.target.reset();
            } else {
                showToast(result.message || "Unable to send reset link.", "danger");
            }
        } catch (error) {
            showToast("Connection error. Please try again.", "danger");
        } finally {
            btn.disabled = false;
            btn.textContent = "Send Reset Link";
        }
    });
});
</script>
</body>
</html>
