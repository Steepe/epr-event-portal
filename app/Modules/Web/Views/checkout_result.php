<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment <?php echo $success ? 'Confirmed' : 'Issue'; ?></title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f7f4f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #1E1A2E;
            padding: 24px;
        }
        main {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e9e2ee;
            box-shadow: 0 16px 44px rgba(43, 26, 69, .08);
            padding: 28px;
            text-align: center;
        }
        .mark {
            width: 54px;
            height: 54px;
            margin: 0 auto 18px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: <?php echo $success ? '#E5337B' : '#9b1c38'; ?>;
            color: #fff;
            font-size: 25px;
            font-weight: 800;
        }
        h1 {
            margin: 0 0 10px;
            font-size: 26px;
        }
        p {
            margin: 0 0 22px;
            color: #6B6778;
            line-height: 1.6;
        }
        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            height: 44px;
            border-radius: 5px;
            background: #E5337B;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
<main>
    <div class="mark"><?php echo $success ? '✓' : '!'; ?></div>
    <h1><?php echo $success ? 'Payment confirmed' : 'Payment issue'; ?></h1>
    <p><?php echo esc($message); ?></p>
    <?php
        $target = $metadata['success_url'] ?? site_url('attendees/login');
    ?>
    <a href="<?php echo esc($target); ?>"><?php echo $success ? 'Continue' : 'Go back'; ?></a>
</main>
</body>
</html>
