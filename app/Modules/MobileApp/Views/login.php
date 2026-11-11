<?php echo module_view('MobileApp', 'includes/header_home'); ?>

<style>
    body.epr-mobile-auth { padding: 0 !important; }
    .mobile-login-screen {
        position: relative;
        display: flex;
        min-height: 100svh;
        padding: 84px 22px 44px;
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(circle at 15% 12%, rgba(239,177,30,.16), transparent 30%),
            radial-gradient(circle at 88% 22%, rgba(216,25,142,.22), transparent 34%),
            radial-gradient(ellipse 90% 80% at 45% 55%, #62365c 0%, #422846 48%, #261528 100%);
    }
    .mobile-login-screen::before {
        position: absolute;
        inset: 0;
        content: '';
        pointer-events: none;
        opacity: .52;
        background-image: linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
        background-size: 42px 42px;
        mask-image: linear-gradient(to bottom, rgba(0,0,0,.75), transparent 76%);
    }
    .mobile-login-content { position: relative; z-index: 1; display: flex; width: 100%; min-height: calc(100svh - 128px); max-width: 390px; margin: auto; flex-direction: column; }
    .mobile-login-epr { width: 152px; height: auto; object-fit: contain; object-position: left; }
    .mobile-login-intro { display: flex; flex-direction: column; gap: 14px; margin-top: 54px; }
    .mobile-login-kicker { color: #efb11e; font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; letter-spacing: .22em; text-transform: uppercase; }
    .mobile-login-accelerate { width: min(270px, 86%); height: auto; object-fit: contain; object-position: left center; }
    .mobile-login-card {
        display: flex;
        margin-top: auto;
        padding: 28px 22px;
        flex-direction: column;
        gap: 16px;
        border: 1px solid rgba(239,150,190,.32);
        border-radius: 20px;
        background: rgba(22,8,26,.30);
        box-shadow: 0 0 18px rgba(216,25,142,.20), inset 0 0 12px rgba(216,25,142,.06);
        backdrop-filter: blur(5px);
    }
    .mobile-login-card h1 { margin: 0 0 4px; color: #fff !important; font-size: 20px !important; font-weight: 600 !important; letter-spacing: 0 !important; text-align: left; }
    .mobile-login-card h1::after { display: none; }
    .mobile-login-field { position: relative; display: flex; align-items: center; }
    .mobile-login-field i { position: absolute; z-index: 1; left: 16px; color: rgba(255,255,255,.60); }
    .mobile-login-field .form-control {
        width: 100%; height: 48px; padding: 0 14px 0 42px !important;
        border: 1px solid rgba(255,255,255,.35) !important; border-radius: 10px !important;
        background: rgba(255,255,255,.04) !important; color: #fff !important; font-size: 14px !important;
    }
    .mobile-login-field .form-control:focus { border-color: #efb11e !important; }
    .mobile-login-submit { width: 100%; height: 48px; border: 0; border-radius: 15px; background: linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585); color: #fff; font-size: 15px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }
    .mobile-login-links { display: flex; justify-content: space-between; gap: 12px; margin-top: 4px; color: rgba(255,255,255,.85); font-size: 13px; }
    .mobile-login-links a { color: inherit; text-decoration: none; }
    .mobile-login-links a:last-child { color: #fff; font-weight: 600; }
    .mobile-login-error { margin: 0; border: 1px solid rgba(239,177,30,.6); border-radius: 10px; background: rgba(239,177,30,.14); color: #fff4cc; font-size: 12px; }
    @media (max-height: 720px) { .mobile-login-screen { padding-top: 48px; padding-bottom: 28px; } .mobile-login-intro { margin-top: 28px; } .mobile-login-card { margin-top: 34px; } }
</style>

<main class="mobile-login-screen">
    <div class="mobile-login-content">
        <img class="mobile-login-epr" src="<?= mobile_asset_url('images/eventslogo.png') ?>" alt="EPR Events">

        <div class="mobile-login-intro">
            <span class="mobile-login-kicker">Emergence Conference · 2026</span>
            <img class="mobile-login-accelerate" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="Accelerate: Building sustainable systems for execution">
        </div>

        <section class="mobile-login-card" aria-labelledby="mobile-login-title">
            <h1 id="mobile-login-title">Login to continue</h1>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert mobile-login-error"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('mobile/login') ?>">
                <?= csrf_field() ?>
                <div class="mobile-login-field mb-3">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" value="<?= old('email') ?>" required autocomplete="email">
                </div>
                <div class="mobile-login-field mb-3">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required autocomplete="current-password">
                </div>
                <button type="submit" class="mobile-login-submit">Login</button>
            </form>

            <div class="mobile-login-links">
                <a href="<?= site_url('mobile/forgot-password') ?>">Forgot Password?</a>
                <span>No account? <a href="<?= site_url('mobile/register') ?>">Register</a></span>
            </div>
        </section>
    </div>
</main>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
