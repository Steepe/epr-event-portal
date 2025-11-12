<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 14:58
 */

echo module_view('MobileApp', 'includes/header'); ?>

<style>
body {
    background: url('<?php echo asset_url('images/mobile-bg.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    text-align: center;
    padding: 120px 20px;
}
form {
    background: rgba(255, 255, 255, 0.1);
    padding: 25px;
    border-radius: 12px;
    max-width: 360px;
    margin: auto;
}
input {
    width: 100%;
    border: 1px solid rgba(255,255,255,0.3);
    background: transparent;
    color: #fff;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
}
button {
    border: none;
    border-radius: 25px;
    color: #fff;
    padding: 10px 30px;
    font-weight: 600;
    background: linear-gradient(90deg,#9D0F82,#EFB11E);
}
button:hover {
    transform: scale(1.05);
}
</style>

<h2>Reset Password</h2>
<form id="ResetForm">
    <input type="hidden" name="token" value="<?php echo esc($_GET['token'] ?? ''); ?>">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
    <button type="submit">Reset Password</button>
</form>

<script>
document.getElementById("ResetForm").addEventListener("submit", async e => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    if (data.password !== data.confirm_password) {
        alert("Passwords do not match.");
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
        alert("Password reset successful! Redirecting...");
        setTimeout(() => window.location.href = "<?php echo base_url('mobile/login'); ?>", 2000);
    } else {
        alert(result.message || "Reset failed.");
    }
});
</script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
