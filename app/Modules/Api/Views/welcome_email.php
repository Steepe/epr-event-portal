<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 28/10/2025
 * Time: 04:15
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EPRGlobal</title>
    <style>
        /* Core Reset */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 14px;
            color: #333333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f9f9f9;
            padding: 30px 0;
        }
        .email-container {
            max-width: 600px;
            background: #ffffff;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .email-header {
            background: linear-gradient(90deg, #9D0F82, #D8198E);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .email-header img {
            width: 150px;
            margin-bottom: 10px;
        }
        .email-header h1 {
            font-family: "Abril Fatface", serif;
            font-size: 28px;
            margin: 10px 0 0 0;
            color: #ffffff;
        }
        .email-body {
            padding: 30px 40px;
            background-color: #ffffff;
            text-align: left;
            color: #444444;
            line-height: 1.6;
        }
        .email-body h2 {
            color: #9D0F82;
            font-size: 22px;
            margin-top: 0;
        }
        .email-body p {
            margin-bottom: 15px;
        }
        .cta-button {
            display: inline-block;
            background-color: #EFB220;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }
        .cta-button:hover {
            background-color: #f3a938;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #999999;
            padding: 20px;
            background: #f3f3f3;
            border-top: 1px solid #eeeeee;
        }
        @media screen and (max-width: 600px) {
            .email-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">

        <div class="email-header">
            <img src="<?= base_url('assets/images/eventslogo.png'); ?>" alt="EPRGlobal Logo">
            <h1>Welcome to EPRGlobal</h1>
        </div>

        <div class="email-body">
            <h2>Hello <?= esc($firstname) ?>,</h2>
            <p>We’re thrilled to have you join the <strong>EPRGlobal Event Portal</strong> community.</p>
            <p>Your registration was successful — you now have access to our upcoming virtual events, exhibitions, and networking sessions.</p>
            <p>Use your registered email (<strong><?= esc($email ?? '') ?></strong>) to log in and explore.</p>
            <a href="<?= base_url('attendees/login'); ?>" class="cta-button">Sign In to Your Account</a>
            <p style="margin-top: 20px;">If you didn’t create this account, please ignore this message.</p>
        </div>

        <div class="email-footer">
            &copy; <?= date('Y'); ?> EPRGlobal — All rights reserved.<br>
            <span style="color:#9D0F82;">Powered by Creyatif Technologies</span>
        </div>

    </div>
</div>
</body>
</html>

