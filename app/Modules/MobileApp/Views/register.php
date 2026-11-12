<?= module_view('MobileApp', 'includes/header', ['shell' => 'bare', 'back' => false, 'activeTab' => '', 'title' => 'Register']) ?>

<div class="m-register">
    <img class="m-register__logo" src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events">

    <div class="m-register__intro">
        <span class="m-kicker">Emergence Conference · 2026</span>
        <img class="m-register__brand" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate">
    </div>

    <form class="m-glass m-register__form" id="RegistrationForm">
        <h1 class="m-register__title m-wide">Create your account</h1>
        <input class="m-input" name="firstname" placeholder="Enter First Name" autocomplete="given-name" required>
        <input class="m-input" name="lastname" placeholder="Enter Last Name" autocomplete="family-name" required>
        <input class="m-input" type="email" name="email" placeholder="Enter Email" autocomplete="email" required>
        <input class="m-input" name="telephone" placeholder="Enter Mobile Number" autocomplete="tel">
        <select class="m-input" name="country" required>
            <option value="">Select Country</option>
            <?php foreach (($countries ?? []) as $country): ?>
                <option value="<?= esc($country['country_name'], 'attr') ?>"><?= esc($country['country_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input class="m-input" name="state" placeholder="Enter State" required>
        <input class="m-input" name="city" placeholder="Enter City" required>
        <input class="m-input" name="zipcode" placeholder="Enter Zipcode">
        <input class="m-input" type="password" id="password" name="password" placeholder="Enter Password" autocomplete="new-password" required>
        <input class="m-input" type="password" id="conf_password" placeholder="Confirm Password" autocomplete="new-password" required>
        <select class="m-input m-wide" name="hear_about">
            <option value="">How did you hear about us?</option>
            <option>Google</option><option>Facebook</option><option>Twitter</option><option>Instagram</option><option>Email</option><option>Word of Mouth</option>
        </select>
        <button class="m-register__submit m-wide" type="submit">Register</button>
        <p class="m-register__signin m-wide">Already have an account? <a href="<?= site_url('mobile/login') ?>">Sign In</a></p>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('RegistrationForm');
    var password = document.getElementById('password');
    var confirm = document.getElementById('conf_password');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (password.value !== confirm.value) { EPR.toast('Passwords do not match.', true); return; }

        var button = form.querySelector('button[type="submit"]');
        button.disabled = true; button.textContent = 'Creating account...';
        try {
            var response = await fetch(<?= json_encode(base_url('api/register')) ?>, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-API-KEY': <?= json_encode((string) env('api.securityKey')) ?> },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            });
            var result = await response.json();
            if (result.status === 'success') {
                EPR.toast('Registration successful. Redirecting to login...');
                setTimeout(function () { window.location.href = <?= json_encode(site_url('mobile/login')) ?>; }, 1200);
            } else {
                EPR.toast(result.message || 'Registration failed. Please try again.', true);
            }
        } catch (error) {
            EPR.toast('Network error. Please try again later.', true);
        } finally {
            button.disabled = false; button.textContent = 'Register';
        }
    });
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
