<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 00:27
 */

echo module_view('Web', 'includes/auth_header_two');
?>

<style>
.form-control {
    background: transparent !important;
    border-radius: 10px;
    font-size: 13px;
    padding-left: 35px;
    color: #fff;
}
::placeholder { color: #fff !important; opacity: 1; }
.btn-epr-purple {
    background-color: #9D0F82;
    color: white;
    border-radius: 6px;
    padding: 8px 20px;
    font-size: 14px;
    border: none;
}
.btn-epr-purple:hover { background-color: #7a0e69; }
</style>

<body class="login-bg">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5 mx-auto mt-5">
            <div class="text-center mb-5">
                <img src="<?php echo asset_url('images/eventslogo.png'); ?>" width="250">
            </div>

            <form id="ResetPasswordForm" style="width:80%;margin:auto; margin-top:200px;">
                <input type="hidden" name="token" value="<?php echo esc($_GET['token'] ?? ''); ?>">
                <div class="form-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Enter New Password" required>
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn epr-btn-one">RESET PASSWORD</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast" class="alert text-center d-none"
     style="position:fixed;top:20px;left:50%;transform:translateX(-50%);
     z-index:2000;width:300px;"></div>

<?php echo module_view('Web', 'includes/scripts'); ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("ResetPasswordForm");
    const toast = document.getElementById("toast");

    const showToast = (msg, type="success") => {
        toast.className = `alert alert-${type} text-center`;
        toast.textContent = msg;
        toast.classList.remove("d-none");
        setTimeout(() => toast.classList.add("d-none"), 3500);
    };

    form.addEventListener("submit", async e => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(form));
        if (data.password !== data.confirm_password) {
            showToast("Passwords do not match.", "danger");
            return;
        }

        const res = await fetch("<?php echo base_url('api/password/reset'); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-API-KEY": "<?php echo env('api.securityKey'); ?>"
            },
            body: JSON.stringify(data)
        });

        const result = await res.json();
        if (result.status === "success") {
            showToast("Password reset successful! Redirecting...");
            setTimeout(() => window.location.href = "<?php echo base_url('attendees/login'); ?>", 2000);
        } else {
            showToast(result.message || "Reset failed.", "danger");
        }
    });
});
</script>
</body>
</html>
