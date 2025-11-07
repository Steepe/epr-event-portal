<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 17:41
 */

echo module_view('Web', 'includes/auth_header'); ?>
<style>
    .form-control::-webkit-input-placeholder,
    .form-control::-moz-placeholder,
    .form-control::placeholder {
        color: #ffffff;
        font-size: 10px;
    }

    /* Placeholder text color (cross-browser support) */
    ::placeholder {
        color: #ffffff !important;
        opacity: 1; /* Ensure full visibility in Firefox */
    }

    /* For older browsers */
    ::-webkit-input-placeholder {
        color: #ffffff !important;
    }

    ::-moz-placeholder {
        color: #ffffff !important;
    }

    :-ms-input-placeholder {
        color: #ffffff !important;
    }

    :-moz-placeholder {
        color: #ffffff !important;
    }


    .form-control {
        background-color: transparent;
        outline: none;
        box-shadow: none;
        color: white;
        border: solid 1px #ffffff;
        border-radius: 10px;
        font-size: 10px !important;
        height: 40px;
    }

    .form-control:focus {
        border: solid 1px #c9c9c9;
        color: #c9c9c9;
    }

    .text-black {
        color: #0b0b0b !important;
    }

    .login-bg {
        min-height: 100vh;
    }
</style>

<body class="login-bg">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="mt-5 text-center">
                <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="logo" width="250">
            </div>
            <div class="mt-3">
                <form id="RegistrationForm" style="width: 80%; margin: auto;">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter First Name" required>
                        </div>
                        <div class="col-md-12 mt-2">
                            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Enter Last Name" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Enter Mobile Number">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <select class="form-control" id="country" name="country" required>
                                <option value="">Select Country</option>
                                <?php if (isset($countries) && !empty($countries)): ?>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country['country_name']; ?>">
                                            <?php echo $country['country_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No countries available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="state" name="state" placeholder="Enter State" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter City" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="Enter Zipcode">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                        </div>
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="conf_password" placeholder="Confirm Password" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <select id="hear_about" name="hear_about" class="form-control">
                                <option value="">How did you hear about us?</option>
                                <option value="Google">Google</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Twitter">Twitter</option>
                                <option value="Instagram">Instagram</option>
                                <option value="Email">Email</option>
                                <option value="Word of Mouth">Word of Mouth</option>
                            </select>
                        </div>
                    </div>

                    <p class="text-center text-white mb-0">
                        Already have an account? <a href="<?php echo base_url('attendees/login'); ?>" style="color: #EFB11E;">Sign In</a>
                    </p>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-epr-pink">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<!-- ✅ Toast Notification -->
<div id="toastBox" class="toast-box"></div>

<style>
    .toast-box {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 3000;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
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
    .toast.show {
        opacity: 1;
        transform: translateY(0);
    }
    .toast-success { background: linear-gradient(to right, #9D0F82, #D8198E); }
    .toast-error   { background: #EFB11E; color: #000; font-weight: 500; }
</style>

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

            // Trigger animation
            requestAnimationFrame(() => toast.classList.add("show"));

            // Auto remove after 4s
            setTimeout(() => {
                toast.classList.remove("show");
                setTimeout(() => toast.remove(), 400);
            }, 4000);
        }

        function validatePassword() {
            confirmPassword.setCustomValidity(
                password.value !== confirmPassword.value ? "Passwords don't match" : ""
            );
        }
        password.onchange = validatePassword;
        confirmPassword.onkeyup = validatePassword;

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = Object.fromEntries(new FormData(form));
            const btn = form.querySelector("button[type='submit']");
            btn.disabled = true;
            btn.textContent = "Registering...";

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
                    showToast("Registration successful! Redirecting to login...", "success");
                    setTimeout(() => {
                        window.location.href = "<?php echo base_url('attendees/login'); ?>";
                    }, 3500);
                } else {
                    showToast(result.message || "Registration failed. Please try again.", "error");
                }
            } catch (err) {
                console.error(err);
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