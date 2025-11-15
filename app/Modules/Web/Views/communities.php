<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 14/11/2025
 * Time: 17:30
 */

echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');
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
        color: #fff;
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
    <h4>Our Community</h4>

    <div class="envision-video">
        <div style="position:relative;padding-top:56.25%;"><iframe src="https://iframe.mediadelivery.net/embed/542640/74e30304-6556-4aff-bf05-5c5ae2cb3b40?autoplay=false&loop=false&muted=false&preload=true&responsive=true" loading="lazy" style="border:0;position:absolute;top:0;height:100%;width:100%;" allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true"></iframe></div></div>

<?php echo module_view('Web', 'includes/scripts'); ?>
