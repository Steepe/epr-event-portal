<?php echo module_view('Web', 'includes/auth_header', ['token' => $token ?? '']); ?>

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

            <form data-password-reset data-endpoint="<?= esc(site_url('api/password/reset'), 'attr') ?>" data-api-key="<?= esc(env('api.securityKey', ''), 'attr') ?>" data-login-url="<?= esc(site_url('attendees/login'), 'attr') ?>" id="ResetPasswordForm" class="rc-auth-card">
                <input type="hidden" name="token" value="<?php echo esc($token ?? '', 'attr'); ?>">
                <div class="rc-card-title">Reset password</div>
                <p style="margin:0 0 4px;color:rgba(255,255,255,.72);font-size:13px;line-height:1.5">Choose a new password for your portal access.</p>

                <label class="rc-field">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input class="rc-input" type="password" name="password" placeholder="Enter New Password" autocomplete="new-password" minlength="8" required>
                </label>

                <label class="rc-field">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input class="rc-input" type="password" name="confirm_password" placeholder="Confirm Password" autocomplete="new-password" minlength="8" required>
                </label>

                <button type="submit" class="rc-btn">Reset Password</button>
                <p data-reset-status role="status" aria-live="polite" style="margin:0;font-size:13px"></p>

                <div class="rc-link-row" style="justify-content:center">
                    <a href="<?php echo base_url('attendees/login'); ?>">Back to login</a>
                    <a href="<?php echo site_url('attendees/forgot-password'); ?>">Request a new link</a>
                </div>
            </form>
        </main>
    </div>
</div>

<div id="toast" class="rc-toast"></div>

<?php echo module_view('Web', 'includes/scripts'); ?>
<script defer src="<?= asset_url('js/password-reset.js') ?>"></script>
</body>
</html>
