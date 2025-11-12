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
<div class="email-wrapper" style="background-color:#f4f4f4;padding:40px 0;font-family:'Poppins',sans-serif;">
    <div class="email-container" style="max-width:600px;margin:auto;background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,0.1);">

        <!-- Header -->
        <div class="email-header" style="padding:30px;text-align:center;">
            <img src="https://portal.eprglobal.com/assets/images/eventslogo.png" alt="EPRGlobal Logo" style="width:180px;margin-bottom:10px;">
            <h1 style="color:#fff;margin:0;font-size:22px;font-weight:700;">Welcome to EPR Global’s Event Portal!</h1>
            <p style="color:#fff;font-size:14px;margin-top:5px;">Your Access Is Confirmed 🎉</p>
        </div>

        <!-- Body -->
        <div class="email-body" style="padding:35px 30px;color:#333333;line-height:1.7;font-size:15px;">
            <h2 style="color:#9D0F82;font-weight:700;font-size:18px;">Hello <?php echo esc($firstname) ?>,</h2>

            <p>Welcome to <strong>EPR Global’s Event Portal</strong>, your digital gateway to inspiration, empowerment, and transformation.</p>

            <p>You now have full access to our library of past <strong>Emergence Conferences</strong>, webinars, and other powerful sessions from our <strong>Women’s Professional Network (WPN)</strong>. Each resource is designed to help you grow spiritually, personally, and professionally.</p>

            <p>Take time to revisit your favourite moments or catch up on sessions you may have missed. Every talk is an opportunity to gain clarity, grow in confidence, and keep your genius in motion.</p>

            <p>If you have any questions or need support while on the platform, please reach out to us at
                <a href="mailto:support@eprglobal.com" style="color:#9D0F82;font-weight:600;text-decoration:none;">support@eprglobal.com</a> — we’re happy to help.
            </p>

            <p>Welcome again to the community of women of African descent who are rising with purpose and unleashing their God-given genius. 💎</p>

            <div style="text-align:center;margin:35px 0;">
                <a href="<?php echo base_url('attendees/login'); ?>"
                   class="cta-button"
                   style="background:#9D0F82;color:#fff;text-decoration:none;padding:12px 28px;border-radius:30px;font-weight:600;display:inline-block;transition:0.3s;">
                    Access Your Account
                </a>
            </div>

            <p style="margin-top:20px;text-align:center;color:#777;">
                With excitement,<br>
                <strong>The EPR Global Team</strong><br>
                <em>Building the Women who will Build Institutions</em>
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer" style="background:#150020;color:#fff;text-align:center;padding:15px 10px;font-size:12px;">
            &copy; <?php echo date('Y'); ?> EPR Global — All Rights Reserved.<br>
            <span style="color:#EFB11E;">Powered by <a href="https://about.me/steepe" target="_blank">Creyatif Technologies</a> </span>
        </div>

    </div>
</div>
</body>
</html>

