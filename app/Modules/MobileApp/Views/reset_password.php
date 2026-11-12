<?= module_view('MobileApp', 'includes/header', ['shell' => 'bare', 'back' => false, 'activeTab' => '', 'title' => 'Reset Password']) ?>

<div class="m-auth">
    <img class="m-auth__logo" src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events">

    <div class="m-auth__intro">
        <span class="m-kicker">Emergence Conference · 2026</span>
        <img class="m-auth__brand" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate">
    </div>

    <section class="m-glass" aria-labelledby="resetTitle">
        <h1 class="m-glass__title" id="resetTitle">Reset Password</h1>
        <form class="m-auth__form" id="ResetForm" data-password-reset
              data-endpoint="<?= esc(site_url('api/password/reset'), 'attr') ?>"
              data-api-key="<?= esc(env('api.securityKey', ''), 'attr') ?>"
              data-login-url="<?= esc(site_url('mobile/login'), 'attr') ?>">
            <input type="hidden" name="token" value="<?= esc($token ?? '', 'attr') ?>">
            <div class="m-field">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input class="m-input m-input--48" type="password" name="password" placeholder="New Password" autocomplete="new-password" minlength="8" required>
            </div>
            <div class="m-field">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input class="m-input m-input--48" type="password" name="confirm_password" placeholder="Confirm Password" autocomplete="new-password" minlength="8" required>
            </div>
            <button class="m-btn m-btn--plain" type="submit">Reset Password</button>
            <p class="m-status" data-reset-status role="status" aria-live="polite"></p>
        </form>
        <div class="m-auth__links">
            <a href="<?= site_url('mobile/forgot-password') ?>">Request a new link</a>
            <a href="<?= site_url('mobile/login') ?>">Back to login</a>
        </div>
    </section>
</div>

<script defer src="<?= asset_url('js/password-reset.js') ?>"></script>

<?= module_view('MobileApp', 'includes/footer') ?>
