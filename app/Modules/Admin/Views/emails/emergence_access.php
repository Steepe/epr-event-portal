<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title><?php echo esc($subject); ?></title>
</head>
<body style="margin:0;padding:0;background:#f5f0f6;color:#2f2133;font-family:Arial,Helvetica,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">Your access details for Emergence Conference 2026.</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0;padding:0;background:#f5f0f6;">
    <tr><td align="center" style="padding:28px 14px;">
      <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 30px rgba(45,10,54,.13);">
        <tr><td style="height:7px;background:linear-gradient(90deg,#7a217f,#e2188f);"></td></tr>
        <tr><td style="padding:30px 36px;background:#28032f;background-image:linear-gradient(135deg,#23002d 0%,#651168 58%,#a91388 100%);color:#ffffff;">
          <p style="margin:0 0 10px;font-size:11px;line-height:16px;font-weight:700;letter-spacing:2px;color:#f4c75a;">EPR GLOBAL PRESENTS</p>
          <h1 style="margin:0;font-size:31px;line-height:37px;font-weight:700;letter-spacing:-.7px;color:#ffffff;">Emergence Conference<br>2026</h1>
          <p style="margin:18px 0 0;font-size:13px;line-height:20px;color:#f3d9ef;">Accelerate: Building Sustainable Systems for Execution</p>
        </td></tr>
        <tr><td style="padding:34px 36px 12px;font-size:16px;line-height:25px;color:#433146;">
          <p style="margin:0 0 20px;">Hello <?php echo esc($name); ?>,</p>
          <h2 style="margin:0 0 18px;font-size:25px;line-height:32px;letter-spacing:-.35px;color:#35003d;"><?php echo esc($subject); ?></h2>
          <div style="margin:0;color:#4f4052;"><?php echo nl2br(esc($body)); ?></div>
        </td></tr>
        <tr><td style="padding:18px 36px 34px;">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0"><tr><td style="border-radius:8px;background:#9d0f82;">
            <a href="<?php echo esc($loginUrl, 'attr'); ?>" style="display:inline-block;padding:15px 24px;border:1px solid #9d0f82;border-radius:8px;color:#ffffff;font-size:15px;font-weight:700;line-height:20px;text-decoration:none;">Log in to the portal&nbsp;&nbsp;→</a>
          </td></tr></table>
          <p style="margin:18px 0 0;font-size:13px;line-height:20px;color:#756778;">If you cannot remember your password, select <strong>Forgot Password</strong> on the login page. Please also check your spam or junk folder for the reset email.</p>
        </td></tr>
        <tr><td style="padding:21px 36px;background:#fbf8fb;border-top:1px solid #eee5ef;color:#7b6c80;font-size:12px;line-height:18px;">This email was sent because you have an EPR Global Portal account. For support, contact <a href="mailto:registration@eprglobal.com" style="color:#851074;text-decoration:underline;">registration@eprglobal.com</a>.</td></tr>
      </table>
      <p style="margin:18px 0 0;color:#8c7e90;font-size:11px;line-height:16px;">© <?php echo date('Y'); ?> EPR Global. All rights reserved.</p>
    </td></tr>
  </table>
</body>
</html>
