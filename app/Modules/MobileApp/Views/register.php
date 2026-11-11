<?php echo module_view('MobileApp', 'includes/header_home'); ?>

<style>
    body.epr-mobile-auth { padding: 0 !important; }
    .mobile-register-screen { min-height: 100svh; padding: 72px 20px 40px; color: #fff; background: radial-gradient(circle at 15% 12%,rgba(239,177,30,.16),transparent 30%),radial-gradient(circle at 88% 22%,rgba(216,25,142,.22),transparent 34%),radial-gradient(ellipse 90% 80% at 45% 55%,#62365c 0%,#422846 48%,#261528 100%); }
    .mobile-register-wrap { display: flex; max-width: 390px; margin: 0 auto; flex-direction: column; gap: 22px; }
    .mobile-register-epr { width: 145px; height: auto; object-fit: contain; object-position: left; }
    .mobile-register-intro { display: flex; flex-direction: column; gap: 12px; }
    .mobile-register-kicker { color: #efb11e; font-family: 'JetBrains Mono',monospace; font-size: 11px; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; }
    .mobile-register-accelerate { width: 220px; height: auto; object-fit: contain; object-position: left; }
    .mobile-register-card { display: grid; padding: 24px 18px; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; border: 1px solid rgba(239,150,190,.32); border-radius: 20px; background: rgba(22,8,26,.30); box-shadow: 0 0 18px rgba(216,25,142,.2), inset 0 0 12px rgba(216,25,142,.06); backdrop-filter: blur(5px); }
    .mobile-register-card h1 { grid-column: 1 / -1; margin: 0 0 6px; color: #fff !important; font-size: 20px !important; font-weight: 600 !important; letter-spacing: 0 !important; }
    .mobile-register-card h1::after { display: none; }
    .mobile-register-card .form-control { width: 100%; min-width: 0; height: 46px; margin: 0 !important; padding: 0 14px !important; border: 1px solid rgba(255,255,255,.35) !important; border-radius: 10px !important; background: rgba(255,255,255,.04) !important; color: #fff !important; font-size: 13px !important; }
    .mobile-register-card select.form-control { color: rgba(255,255,255,.72) !important; }
    .mobile-register-card select.form-control option { color: #fff; background: #260029; }
    .mobile-register-card .form-control:focus { border-color: #efb11e !important; }
    .mobile-register-wide { grid-column: 1 / -1; }
    .mobile-register-submit { grid-column: 1 / -1; height: 46px; border: 0; border-radius: 10px; background: #d8198e; color: #fff; font-size: 15px; font-weight: 600; letter-spacing: .04em; }
    .mobile-register-signin { grid-column: 1 / -1; margin: 4px 0 0; color: rgba(255,255,255,.85); font-size: 13px; text-align: center; }
    .mobile-register-signin a { color: #fff; font-weight: 600; text-decoration: none; }
    #toastBox { position: fixed; z-index: 3000; bottom: 24px; left: 50%; display: flex; flex-direction: column; gap: 10px; transform: translateX(-50%); }
    .toast { min-width: 250px; padding: 12px 16px; border-radius: 8px; background: #9d0f82; color: #fff; font-size: 14px; text-align: center; opacity: 0; transform: translateY(20px); transition: all .3s ease; }
    .toast.show { opacity: 1; transform: translateY(0); }.toast-error { background: #efb11e; color: #260029; font-weight: 600; }
    @media(max-width:360px) { .mobile-register-screen { padding-right: 14px; padding-left: 14px; } .mobile-register-card { padding: 20px 13px; gap: 9px; } }
</style>

<main class="mobile-register-screen">
    <div class="mobile-register-wrap">
        <img class="mobile-register-epr" src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events">
        <div class="mobile-register-intro">
            <span class="mobile-register-kicker">Emergence Conference · 2026</span>
            <img class="mobile-register-accelerate" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate">
        </div>
        <form id="RegistrationForm" class="mobile-register-card">
            <h1>Create your account</h1>
            <input class="form-control" name="firstname" placeholder="Enter First Name" required>
            <input class="form-control" name="lastname" placeholder="Enter Last Name" required>
            <input class="form-control" type="email" name="email" placeholder="Enter Email" required>
            <input class="form-control" name="telephone" placeholder="Enter Mobile Number">
            <select class="form-control" name="country" required><option value="">Select Country</option><?php foreach (($countries ?? []) as $country): ?><option value="<?= esc($country['country_name']) ?>"><?= esc($country['country_name']) ?></option><?php endforeach; ?></select>
            <input class="form-control" name="state" placeholder="Enter State" required>
            <input class="form-control" name="city" placeholder="Enter City" required>
            <input class="form-control" name="zipcode" placeholder="Enter Zipcode">
            <input class="form-control" type="password" id="password" name="password" placeholder="Enter Password" required>
            <input class="form-control mobile-register-wide" type="password" id="conf_password" placeholder="Confirm Password" required>
            <select class="form-control mobile-register-wide" name="hear_about"><option value="">How did you hear about us?</option><option>Google</option><option>Facebook</option><option>Twitter</option><option>Instagram</option><option>Email</option><option>Word of Mouth</option></select>
            <button class="mobile-register-submit" type="submit">Register</button>
            <p class="mobile-register-signin">Already have an account? <a href="<?= site_url('mobile/login') ?>">Sign In</a></p>
        </form>
    </div>
</main>
<div id="toastBox"></div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('RegistrationForm'); const box = document.getElementById('toastBox');
    const show = (message, error = false) => { const t = document.createElement('div'); t.className = 'toast' + (error ? ' toast-error' : ''); t.textContent = message; box.appendChild(t); requestAnimationFrame(() => t.classList.add('show')); setTimeout(() => t.remove(), 3500); };
    document.getElementById('conf_password').addEventListener('input', e => e.target.setCustomValidity(e.target.value !== document.getElementById('password').value ? 'Passwords do not match' : ''));
    form.addEventListener('submit', async event => { event.preventDefault(); const button = form.querySelector('button'); button.disabled = true; button.textContent = 'Creating account...'; try { const response = await fetch('<?= base_url('api/register') ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-API-KEY': '<?= env('api.securityKey') ?>' }, body: JSON.stringify(Object.fromEntries(new FormData(form))) }); const result = await response.json(); if (result.status === 'success') { show('Registration successful! Redirecting...'); setTimeout(() => window.location.href = '<?= site_url('mobile/login') ?>', 1200); } else show(result.message || 'Registration failed. Please try again.', true); } catch { show('Network error. Please try again later.', true); } finally { button.disabled = false; button.textContent = 'Register'; } });
});
</script>
<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
