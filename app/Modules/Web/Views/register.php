<?php echo module_view('Web', 'includes/auth_header'); ?>

<body class="rc-body">
<div class="rc-stage">
    <div class="rc-stage-inner">
        <form id="RegistrationForm" class="rc-auth-screen rc-register-screen" data-screen-label="Register">
            <img class="rc-event-logo" src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Events">

            <div class="rc-brand-stack">
                <span class="rc-kicker">Emergence Conference &middot; 2026</span>
                <h1 style="margin:0;line-height:0">
                    <img class="rc-accelerate-logo"
                         src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>"
                         alt="Accelerate: Building sustainable systems for execution">
                </h1>
            </div>

            <div class="rc-auth-card rc-register-card">
                <div class="rc-card-title">Create your account</div>
                <div id="accountExistsNotice" class="rc-account-notice" role="alert" hidden>
                    <div class="rc-account-notice__icon" aria-hidden="true">!</div>
                    <div>
                        <strong>An account already exists with this email.</strong>
                        <p id="accountExistsMessage">Sign in to continue, or reset your password if you cannot remember it.</p>
                        <div class="rc-account-notice__actions">
                            <a href="<?php echo site_url('attendees/login'); ?>">Sign in</a>
                            <a href="<?php echo site_url('attendees/forgot-password'); ?>">Reset password</a>
                        </div>
                    </div>
                </div>

                <input class="rc-input" type="text" id="firstname" name="firstname" placeholder="Enter First Name" required>
                <input class="rc-input" type="text" id="lastname" name="lastname" placeholder="Enter Last Name" required>
                <input class="rc-input" type="email" id="email" name="email" placeholder="Enter Email" required>
                <input class="rc-input" type="text" id="telephone" name="telephone" placeholder="Enter Mobile Number" required>

                <select class="rc-select" id="country" name="country" required>
                    <option value="">Select Country</option>
                    <?php if (isset($countries) && !empty($countries)): ?>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo esc($country['country_name']); ?>">
                                <?php echo esc($country['country_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">No countries available</option>
                    <?php endif; ?>
                </select>

                <input class="rc-input" type="text" id="state" name="state" placeholder="Enter State" required>
                <input class="rc-input" type="text" id="city" name="city" placeholder="Enter City" required>
                <input class="rc-input" type="text" id="zipcode" name="zipcode" placeholder="Enter Zipcode">
                <input class="rc-input" type="password" id="password" name="password" placeholder="Enter Password" required>
                <input class="rc-input" type="password" id="conf_password" placeholder="Confirm Password" required>

                <select class="rc-select rc-span-all" id="hear_about" name="hear_about" required>
                    <option value="">How did you hear about us?</option>
                    <option value="Google">Google</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Twitter">Twitter</option>
                    <option value="Instagram">Instagram</option>
                    <option value="Email">Email</option>
                    <option value="Word of Mouth">Word of Mouth</option>
                </select>

                <button type="submit" class="rc-btn rc-span-all">Register</button>

                <p class="rc-register-note">Already have an account? <a href="<?php echo base_url('attendees/login'); ?>">Sign In</a></p>
                <p class="rc-auth-support rc-span-all">Need help with your registration? Contact <a href="mailto:registration@eprglobal.com">registration@eprglobal.com</a>.</p>
            </div>
        </form>
    </div>
</div>

<div id="toast" class="rc-toast"></div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("RegistrationForm");
    const toast = document.getElementById("toast");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("conf_password");
    const email = document.getElementById("email");
    const accountExistsNotice = document.getElementById("accountExistsNotice");
    const accountExistsMessage = document.getElementById("accountExistsMessage");

    function showToast(message, type = "success") {
        toast.className = "rc-toast is-open" + (type === "error" || type === "danger" ? " is-error" : "");
        toast.textContent = message;
        setTimeout(() => toast.classList.remove("is-open"), 4000);
    }

    function validatePassword() {
        confirmPassword.setCustomValidity(
            password.value !== confirmPassword.value ? "Passwords do not match" : ""
        );
    }

    function showAccountExists(message) {
        accountExistsMessage.textContent = message || "Sign in to continue, or reset your password if you cannot remember it.";
        accountExistsNotice.hidden = false;
        accountExistsNotice.scrollIntoView({ behavior: "smooth", block: "nearest" });
    }

    password.addEventListener("change", validatePassword);
    confirmPassword.addEventListener("keyup", validatePassword);
    email.addEventListener("input", () => { accountExistsNotice.hidden = true; });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        validatePassword();
        if (!form.reportValidity()) return;
        accountExistsNotice.hidden = true;

        const formData = Object.fromEntries(new FormData(form));
        const btn = form.querySelector("button[type='submit']");
        btn.disabled = true;
        btn.textContent = "Creating account...";

        try {
            const response = await fetch("<?php echo base_url('api/register'); ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
                },
                body: JSON.stringify(formData),
            });
            const result = await response.json();

            if (result.status === "success") {
                showToast("Registration successful. Redirecting to login...", "success");
                setTimeout(() => {
                    window.location.href = "<?php echo base_url('attendees/login'); ?>";
                }, 1800);
            } else {
                if (response.status === 409) {
                    showAccountExists(result.message);
                } else {
                    showToast(result.message || "Registration failed. Please try again.", "error");
                }
            }
        } catch (err) {
            showToast("Connection error. Please try again later.", "error");
        } finally {
            btn.disabled = false;
            btn.textContent = "Register";
        }
    });
});
</script>

</body>
</html>
