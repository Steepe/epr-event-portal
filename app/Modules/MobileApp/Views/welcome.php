<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 05:13
 */

 echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        overflow-x: hidden;
        color: #fff;
        background: url('<?php echo asset_url('images/mobile-brain-light.png'); ?>') no-repeat center center fixed;
        background-size: cover;
        position: relative;
    }

    /* 🌈 Overlay Gradient */
    #imageOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    /* 🧠 Container */
    .envision-container {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 100px 1.5rem 100px;
        z-index: 2;
        position: relative;
        backdrop-filter: blur(2px);
    }

    .envision-container h4 {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 30px;
        text-shadow: 0 0 10px rgba(255, 216, 77, 0.5);
        font-size: 1.3rem;
    }

    /* 🎬 Responsive Video Wrapper */
    .envision-video {
        position: relative;
        width: 100%;
        max-width: 700px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.6);
        background: rgba(0, 0, 0, 0.4);
    }

    .envision-video iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 20px;
    }

    /* 💡 Text / Description Section */
    .envision-description {
        margin-top: 25px;
        max-width: 700px;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #eee;
        opacity: 0.9;
    }

    /* 🚪 CTA Button */
    .enter-lobby {
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 14px 0;
        font-weight: 600;
        width: 80%;
        max-width: 350px;
        margin-top: 2.5rem;
        transition: 0.3s;
        text-decoration: none;
        text-align: center;
        font-size: 16px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.3);
    }

    .enter-lobby:hover {
        transform: scale(1.05);
    }

    /* 📱 Responsive */
    @media (max-width: 600px) {
        .envision-container {
            padding: 80px 1rem 90px;
        }
        .envision-container h4 {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        .envision-video {
            border-radius: 14px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.5);
        }
    }
</style>

<!-- 🌈 Overlay Layer -->
<div id="imageOverlay"></div>

<!-- 🧠 Main Content -->
<div class="envision-container">
    <h4 class="epr-text-purple">Welcome To Unleash 2025</h4>

    <div class="envision-video">
        <div style="position:relative;padding-top:56.25%;"><iframe src="https://iframe.mediadelivery.net/embed/405384/60589a7b-3fe7-40fb-a2ae-3c024f23e01e?autoplay=true&loop=false&muted=false&preload=true&responsive=true" loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true"></iframe></div>
    </div>

    <a href="<?php echo site_url('mobile/lobby'); ?>" class="enter-lobby epr-btn-four">ENTER LOBBY</a>
</div>

<?php echo module_view('MobileApp', 'includes/footer'); ?>
