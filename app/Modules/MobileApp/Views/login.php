<?= module_view('MobileApp', 'includes/header', ['shell' => 'bare', 'back' => false, 'activeTab' => '', 'title' => 'Login', 'screenLabel' => 'Login']) ?>

<div class="m-auth">
    <img class="m-auth__logo" src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events">

    <div class="m-auth__intro">
        <span class="m-kicker">Emergence Conference · 2026</span>
        <img class="m-auth__brand" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate: Building sustainable systems for execution">
    </div>

    <section class="m-glass" aria-labelledby="loginTitle">
        <h1 class="m-glass__title" id="loginTitle">Login to continue</h1>
        <form class="m-auth__form" method="post" action="<?= site_url('mobile/login') ?>" id="loginForm">
            <?= csrf_field() ?>
            <div class="m-field">
                <i class="fa fa-envelope" aria-hidden="true"></i>
                <input class="m-input m-input--48" type="email" name="email" placeholder="Enter Email" value="<?= esc(old('email') ?? '', 'attr') ?>" autocomplete="email" required>
            </div>
            <div class="m-field">
                <i class="fa fa-lock" aria-hidden="true"></i>
                <input class="m-input m-input--48" type="password" name="password" placeholder="Enter Password" autocomplete="current-password" required>
            </div>
            <button class="m-btn" type="submit">Login</button>
        </form>
        <div class="m-auth__links">
            <a href="<?= site_url('mobile/forgot-password') ?>" data-open-sheet="reset">Forgot Password?</a>
            <span>No account? <a href="<?= site_url('mobile/register') ?>">Register</a></span>
        </div>
    </section>
</div>

<aside class="m-sheet" data-sheet="reset" role="dialog" aria-modal="true" aria-label="Reset password" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-sheet__bar"><b>Reset Password</b><button class="m-x" type="button" data-close-sheet aria-label="Close">×</button></div>
    <form class="m-sheet__body" id="forgotForm">
        <p class="m-auth__note">Enter your registered email. A reset link will be sent to you.</p>
        <input class="m-input" type="email" name="email" placeholder="Enter Email" autocomplete="email" required>
        <button class="m-btn m-btn--46" type="submit">Send Reset Link</button>
    </form>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('loginForm').addEventListener('submit', function () {
        var button = this.querySelector('button');
        button.textContent = 'Signing in...';
    });

    var forgot = document.getElementById('forgotForm');
    forgot.addEventListener('submit', async function (event) {
        event.preventDefault();
        var button = forgot.querySelector('button');
        button.disabled = true; button.textContent = 'Sending...';
        try {
            var response = await fetch(<?= json_encode(site_url('api/password/forgot')) ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-API-KEY': <?= json_encode((string) env('api.securityKey')) ?> },
                body: JSON.stringify({ email: forgot.email.value.trim(), platform: 'mobile' })
            });
            var result = await response.json();
            if (result.status === 'success') {
                forgot.reset();
                EPR.closeSheet();
                EPR.toast(result.message || 'Please check your inbox and spam folder for the reset link.');
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
