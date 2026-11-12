<?= module_view('MobileApp', 'includes/header', ['shell' => 'bare', 'back' => false, 'activeTab' => '', 'title' => 'Reset Password']) ?>

<div class="m-auth">
    <img class="m-auth__logo" src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events">

    <div class="m-auth__intro">
        <span class="m-kicker">Emergence Conference · 2026</span>
        <img class="m-auth__brand" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate">
    </div>

    <section class="m-glass" aria-labelledby="forgotTitle">
        <h1 class="m-glass__title" id="forgotTitle">Reset Password</h1>
        <p class="m-auth__note">Enter your registered email. A reset link will be sent to you.</p>
        <form class="m-auth__form" id="ForgotForm">
            <div class="m-field">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <input class="m-input m-input--48" type="email" name="email" placeholder="Enter Email" autocomplete="email" required>
            </div>
            <button class="m-btn m-btn--plain" type="submit">Send Reset Link</button>
        </form>
        <div class="m-auth__links">
            <a href="<?= site_url('mobile/login') ?>">Back to Login</a>
            <span>No account? <a href="<?= site_url('mobile/register') ?>">Register</a></span>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('ForgotForm');
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        var button = form.querySelector('button');
        button.disabled = true; button.textContent = 'Sending...';
        try {
            var response = await fetch(<?= json_encode(base_url('api/password/forgot')) ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-API-KEY': <?= json_encode((string) env('api.securityKey')) ?> },
                body: JSON.stringify({ email: form.email.value.trim(), platform: 'mobile' })
            });
            var result = await response.json();
            if (result.status === 'success') {
                EPR.toast(result.message || 'Please check your inbox and spam folder for the reset link.');
                form.reset();
            } else {
                EPR.toast(result.message || 'Unable to send reset link.', true);
            }
        } catch (error) {
            EPR.toast('Network error. Please try again later.', true);
        } finally {
            button.disabled = false; button.textContent = 'Send Reset Link';
        }
    });
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
