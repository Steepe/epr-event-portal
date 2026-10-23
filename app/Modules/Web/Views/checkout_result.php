<?php
$target = $metadata['success_url'] ?? site_url('attendees/login');
$statusTitle = $success ? 'Payment confirmed' : 'Payment issue';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc($statusTitle); ?></title>
    <link rel="shortcut icon" href="<?php echo asset_url('images/favicon.png'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo asset_url('font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo asset_url('css/accelerate-portal.css'); ?>" rel="stylesheet" type="text/css">
    <script defer src="<?php echo asset_url('js/accelerate-background.js'); ?>"></script>
</head>
<body class="rc-body">
<div class="rc-stage">
    <div class="rc-stage-inner" style="display:flex;align-items:center;justify-content:center;padding:24px;box-sizing:border-box">
        <main data-screen-label="Checkout Result" class="rc-auth-card" style="justify-self:auto;align-items:center;text-align:center;max-width:480px">
            <span style="width:72px;height:72px;border-radius:50%;background:<?php echo $success ? 'rgba(74,222,128,.14)' : 'rgba(239,177,30,.14)'; ?>;color:<?php echo $success ? '#4ade80' : '#efb11e'; ?>;display:flex;align-items:center;justify-content:center;font-size:30px;box-shadow:0 0 40px rgba(0,0,0,.25)">
                <i class="fa <?php echo $success ? 'fa-check' : 'fa-exclamation'; ?>" aria-hidden="true"></i>
            </span>
            <span class="rc-card-title" style="font-size:24px;margin:0"><?php echo esc($statusTitle); ?></span>
            <p style="margin:0;color:rgba(255,255,255,.72);font-size:13px;line-height:1.6;max-width:340px"><?php echo esc($message); ?></p>
            <a href="<?php echo esc($target); ?>" class="rc-btn" style="width:100%;display:flex;align-items:center;justify-content:center;text-decoration:none;margin-top:8px">
                <?php echo $success ? 'Continue' : 'Go back'; ?>
            </a>
        </main>
    </div>
</div>
</body>
</html>
