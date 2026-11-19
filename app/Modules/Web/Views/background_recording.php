<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hologram Background Recording</title>
    <link rel="stylesheet" href="<?php echo asset_url('css/accelerate-portal.css'); ?>">
    <script defer src="<?php echo asset_url('js/accelerate-background.js'); ?>"></script>
    <style>
        html, body.rc-body { width: 100%; height: 100%; overflow: hidden; }
        .rc-stage { min-width: 0; min-height: 0; height: 100dvh; }
        .rc-recording-presenter {
            position: absolute;
            inset: 0;
            z-index: 2;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center bottom;
            pointer-events: none;
        }
    </style>
</head>
<body class="rc-body">
    <div class="rc-stage">
        <div class="rc-stage-inner">
            <main data-screen-label="Lobby" aria-label="Hologram background"></main>
            <video class="rc-recording-presenter" autoplay muted playsinline preload="auto" aria-hidden="true">
                <source src="<?php echo asset_url('videos/emergence-presenter-transparent.webm'); ?>" type="video/webm">
            </video>
        </div>
    </div>
</body>
</html>
