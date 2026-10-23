<?php echo module_view('Web', 'includes/auth_header'); ?>

<body class="rc-body">
<div class="rc-stage">
    <div class="rc-stage-inner">
        <img class="rc-event-logo" src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Events">

        <main class="rc-auth-screen" data-screen-label="Reset Password">
            <section class="rc-brand-stack">
                <span class="rc-kicker">Emergence Conference · 2026</span>
                <img class="rc-accelerate-logo"
                     src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>"
                     alt="Accelerate: Building sustainable systems for execution">
            </section>

            <form id="ResetPasswordForm" class="rc-auth-card">
                <input type="hidden" name="token" value="<?php echo esc($_GET['token'] ?? ''); ?>">
                <div class="rc-card-title">Reset password</div>
                <p style="margin:0 0 4px;color:rgba(255,255,255,.72);font-size:13px;line-height:1.5">Choose a new password for your portal access.</p>

                <label class="rc-field">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input class="rc-input" type="password" name="password" placeholder="Enter New Password" required>
                </label>

                <label class="rc-field">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input class="rc-input" type="password" name="confirm_password" placeholder="Confirm Password" required>
                </label>

                <button type="submit" class="rc-btn">Reset Password</button>

                <div class="rc-link-row" style="justify-content:center">
                    <a href="<?php echo base_url('attendees/login'); ?>">Back to login</a>
                </div>
            </form>
        </main>
    </div>
</div>

<div id="toast" class="rc-toast"></div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("ResetPasswordForm");
    const toast = document.getElementById("toast");

    const showToast = (message, isError = false) => {
        toast.textContent = message;
        toast.className = "rc-toast is-open" + (isError ? " is-error" : "");
        setTimeout(() => toast.classList.remove("is-open"), 3500);
    };

    form.addEventListener("submit", async event => {
        event.preventDefault();
        const data = Object.fromEntries(new FormData(form));

        if (data.password !== data.confirm_password) {
            showToast("Passwords do not match.", true);
            return;
        }

        const response = await fetch("<?php echo base_url('api/password/reset'); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.status === "success") {
            showToast("Password reset successful. Redirecting...");
            setTimeout(() => window.location.href = "<?php echo base_url('attendees/login'); ?>", 2000);
        } else {
            showToast(result.message || "Reset failed.", true);
        }
    });
});
</script>
</body>
</html>
