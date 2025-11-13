<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 12/11/2025
 * Time: 07:16
 */

 echo module_view('MobileApp', 'includes/header'); ?>

<style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', 'Poppins', sans-serif;
        overflow-x: hidden;
        color: #fff;
        position: relative;
    }

    /* 🎥 Background Video */
    #bgVideo {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: blur(8px) brightness(0.5);
        z-index: -2;
    }

    /* 🌈 Overlay Gradient */
    #videoOverlay {
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
    }

    .envision-container h4 {
        color: #ffffff;
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
        padding-top: 56.25%; /* 16:9 */
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

<!-- 🎥 Background Video -->
<video autoplay muted loop playsinline id="bgVideo">
    <source src="<?php echo asset_url('videos/mobile-brain-bg.mp4'); ?>" type="video/mp4">
</video>
<div id="videoOverlay"></div>

<div class="envision-container">
    <h4>Vision Birthing Exercise</h4>

    <div class="envision-video">
        <iframe
            src="https://player.vimeo.com/video/771780852?h=2ad1e8b0f7&color=eebf34&title=0&byline=0&portrait=0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>

    <div class="envision-description">
        <p>Take a moment to immerse yourself in the Vision Birthing Experience — a guided visualization designed to help you align clarity with creativity. Watch in a calm space, breathe deeply, and let your purpose unfold.</p>
    </div>
</div>

<?php echo module_view('MobileApp', 'includes/footer_home'); ?>
