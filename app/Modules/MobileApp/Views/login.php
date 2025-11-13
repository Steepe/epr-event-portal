<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 11/11/2025
 * Time: 21:28
 */

 echo module_view('MobileApp', 'includes/header_home'); ?>

<style>
    body {
        background-image: url('<?php echo asset_url('images/mobile-brain-bg.png'); ?>');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
        font-family: 'Inter', 'Open Sans', sans-serif;
        overflow-x: hidden;
        color: #fff;
    }

    .login-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        text-align: center;
        padding: 2rem 1rem;
    }

    .login-card {
        width: 100%;
        max-width: 380px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 20px;
        padding: 2.2rem 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        margin-top: -55px;
        height: 650px;
    }

    .login-card img {
        width: 300px;
        margin-bottom: 4rem;
    }

    .login-card h5 {
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 1.5rem;
    }

    .form-control {
        background: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 30px;
        color: #fff !important;
        font-size: 0.95rem;
        padding: 0.7rem 1rem;
    }

    .form-control::placeholder {
        color: #e5e5e5;
        opacity: 0.8;
    }

    .form-control:focus {
        border-color: #fff;
        box-shadow: none;
    }

    .login-btn {
        color: #fff;
        border: none;
        border-radius: 30px;
        padding: 0.75rem;
        font-weight: 600;
        width: 100%;
        transition: 0.3s;
    }

    .login-btn:hover {
        transform: scale(1.02);
    }

    .remember-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 7.5rem;
        font-size: 0.85rem;
    }

    .remember-row label {
        color: #fff;
    }

    .remember-row a {
        color: #f3bb1a;
        text-decoration: none;
    }

    .remember-row a:hover {
        text-decoration: underline;
    }

    .signup-text {
        color: #e0e0e0;
        margin-top: 1.25rem;
        font-size: 0.9rem;
    }

    .signup-text a {
        color: #f3bb1a;
        font-weight: 600;
        text-decoration: none;
    }

    .signup-text a:hover {
        text-decoration: underline;
    }

    @media (max-width: 575px) {
        .login-card {
            padding: 1.8rem 1.5rem;
            border-radius: 16px;
        }

        .login-card img {
            width: 300px;
        }

        .login-card h5 {
            font-size: 1rem;
        }
    }
</style>

<div class="login-container">
    <div class="login-card">
        <img src="<?php echo asset_url('images/eventslogo.png'); ?>" alt="EPR Logo">

        <h5>Login to Continue</h5>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger small py-2 mb-3">
                <?php echo esc(session()->getFlashdata('error')); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo site_url('mobile/login'); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-5 text-start">
                <input type="email" name="email" id="email" class="form-control"
                    placeholder="User Email" value="<?php echo old('email'); ?>" required>
            </div>

            <div class="mb-5 text-start">
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Password" required>
            </div>

            <div class="remember-row">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" style="cursor:pointer;">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                <a href="<?php echo site_url('mobile/forgot-password'); ?>">Forgot Password</a>
            </div>

            <button type="submit" class="epr-btn-four login-btn">LOGIN</button>

            <div class="signup-text">
                Not a member? <a href="<?php echo site_url('mobile/register'); ?>">Sign Up!</a>
            </div>
        </form>
    </div>
</div>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
