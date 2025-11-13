<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 08/11/2025
 * Time: 23:27
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar_no_menu');
?>

<style>
body {
    background: url('<?php echo asset_url('images/brain-Events-VisionBG.png'); ?>') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    color: #fff;
    overflow-x: hidden;
}

.envision-container {
    padding: 120px 8%;
    text-align: center;
}

.envision-container h4 {
    color: #9D0F82;
    font-weight: 700;
    margin-bottom: 50px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.envision-video {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    height: 0;
    overflow: hidden;
    border-radius: 20px;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
}

.envision-video iframe {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    border: none;
    border-radius: 20px;
}
</style>

<div class="envision-container">
    <h4>Vision Birthing Exercise</h4>

    <div class="envision-video">
        <iframe
            src="https://player.vimeo.com/video/771780852?h=2ad1e8b0f7&color=eebf34&title=0&byline=0&portrait=0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
</div>

<?php echo module_view('Web', 'includes/scripts'); ?>
