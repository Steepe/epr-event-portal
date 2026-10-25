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
<form data-password-reset data-endpoint="<?= esc(site_url('api/password/reset'), 'attr') ?>" data-api-key="<?= esc(env('api.securityKey', ''), 'attr') ?>" data-login-url="<?= esc(site_url('mobile/login'), 'attr') ?>" id="ResetForm">
    <input type="hidden" name="token" value="<?php echo esc($token ?? '', 'attr'); ?>">
    <input type="password" name="password" placeholder="New Password" autocomplete="new-password" minlength="8" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" autocomplete="new-password" minlength="8" required>
    <button type="submit">Reset Password</button>
    <p data-reset-status role="status" aria-live="polite"></p>
    <p><a href="<?= site_url('mobile/forgot-password') ?>">Request a new link</a></p>
    <p><a href="<?= site_url('mobile/login') ?>">Back to login</a></p>
</form>

<script defer src="<?= asset_url('js/password-reset.js') ?>"></script>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
