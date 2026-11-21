<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title><?php echo esc($subject); ?></title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;color:#38243d;font-family:'Trebuchet MS','Lucida Grande','Lucida Sans Unicode',Tahoma,Arial,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">Your Emergence Conference 2026 portal access is ready.</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0;padding:0;background:#f4f4f4;">
    <tr><td align="center" style="padding:32px 14px;">
      <table role="presentation" width="660" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:660px;background:#ffffff;overflow:hidden;box-shadow:0 9px 25px rgba(36,15,42,.10);">
        <tr><td style="height:6px;background:#852a8a;"></td></tr>
        <tr><td style="padding:30px 38px 34px;background:#57236d;background-image:linear-gradient(135deg,#3e124f 0%,#57236d 58%,#852a8a 100%);color:#ffffff;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
            <td><img src="<?= esc(base_url('assets/images/eventslogo.png'), 'attr') ?>" width="204" alt="EPR Global Events Portal" style="display:block;width:204px;max-width:100%;height:auto;border:0;"></td>
            <td align="right" style="font-size:10px;line-height:14px;font-weight:700;letter-spacing:1.8px;color:#f5cf70;">EMERGENCE 2026</td>
          </tr></table>
          <p style="margin:36px 0 10px;font-size:11px;line-height:16px;font-weight:700;letter-spacing:2.1px;color:#f5cf70;">WELCOME TO</p>
          <h1 style="margin:0;font-size:35px;line-height:41px;font-weight:700;letter-spacing:-.9px;color:#ffffff;">Emergence Conference<br><span style="color:#f5cf70;">2026</span></h1>
          <p style="margin:18px 0 0;font-size:15px;line-height:22px;color:#f4ddf1;">Accelerate: Building Sustainable Systems for Execution</p>
        </td></tr>
        <tr><td style="padding:34px 36px 8px;font-size:16px;line-height:25px;color:#463149;">
          <p style="margin:0 0 20px;">Hello <?php echo esc($name); ?>,</p>
          <h2 style="margin:0 0 18px;font-size:25px;line-height:32px;letter-spacing:-.4px;color:#57236d;"><?php echo esc($subject); ?></h2>
          <div style="margin:0;color:#554458;"><?php echo nl2br(esc($body)); ?></div>
        </td></tr>
        <tr><td style="padding:24px 36px 8px;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fbf8f3;border:1px solid #eadbbb;border-radius:10px;"><tr>
            <td style="padding:19px 20px 17px;">
              <p style="margin:0 0 13px;font-size:11px;line-height:16px;font-weight:700;letter-spacing:1.35px;color:#852a8a;">YOUR ACCESS IN TWO STEPS</p>
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"><tr>
                <td valign="top" width="34" style="padding:0 10px 10px 0;"><span style="display:inline-block;width:25px;height:25px;border-radius:13px;background:#57236d;color:#ffffff;font-size:12px;font-weight:700;line-height:25px;text-align:center;">1</span></td>
                <td valign="top" style="padding:2px 0 12px;font-size:14px;line-height:20px;color:#4e3c51;"><strong style="color:#57236d;">Open the portal</strong><br>Use the button below to sign in with your email address and password.</td>
              </tr><tr>
                <td valign="top" width="34" style="padding:0 10px 0 0;"><span style="display:inline-block;width:25px;height:25px;border-radius:13px;background:#d79838;color:#57236d;font-size:12px;font-weight:700;line-height:25px;text-align:center;">2</span></td>
                <td valign="top" style="padding:2px 0;font-size:14px;line-height:20px;color:#4e3c51;"><strong style="color:#57236d;">Need a new password?</strong><br>Select <strong>Forgot Password</strong> on the login page and follow the secure email link.</td>
              </tr></table>
            </td>
          </tr></table>
        </td></tr>
        <tr><td align="center" style="padding:24px 36px 17px;">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0"><tr><td style="border-radius:8px;background:#d79838;box-shadow:0 6px 12px rgba(121,79,10,.18);">
            <a href="<?php echo esc($loginUrl, 'attr'); ?>" style="display:inline-block;padding:16px 30px;border:2px solid #57236d;border-radius:8px;color:#57236d;font-size:16px;font-weight:700;line-height:20px;text-decoration:none;letter-spacing:.1px;">ACCESS PORTAL&nbsp;&nbsp;→</a>
          </td></tr></table>
          <p style="margin:16px 0 0;font-size:12px;line-height:18px;color:#7a6b7d;">Your password-reset email may take a few moments. Please check spam or junk if it does not arrive.</p>
        </td></tr>
        <tr><td style="padding:23px 36px;background:#d79838;color:#57236d;font-size:14px;line-height:21px;"><strong style="letter-spacing:.2px;">GET READY TO ACCELERATE.</strong><br>Need help? Contact <a href="mailto:registration@eprglobal.com" style="color:#57236d;font-weight:700;text-decoration:underline;">registration@eprglobal.com</a>.</td></tr>
      </table>
      <p style="margin:18px 0 0;color:#8d7f90;font-size:11px;line-height:16px;">© <?php echo date('Y'); ?> EPR Global. All rights reserved.</p>
    </td></tr>
  </table>
</body>
</html>
