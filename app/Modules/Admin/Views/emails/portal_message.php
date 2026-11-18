<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title><?php echo esc($subject); ?></title>
</head>
<body style="margin:0;padding:0;background:#f4f0f5;color:#241526;font-family:Arial,Helvetica,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">A new update from EPR Global is waiting for you.</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;margin:0;padding:0;background:#f4f0f5;">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(48,13,53,.10);">
          <tr>
            <td style="padding:30px 36px 28px;background:#26002d;background-image:linear-gradient(135deg,#26002d 0%,#721063 58%,#b61691 100%);color:#ffffff;">
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                  <td style="font-size:22px;line-height:1.1;font-weight:700;letter-spacing:-.02em;">EPR Global</td>
                  <td align="right" style="font-size:10px;line-height:1.2;font-weight:700;letter-spacing:1.6px;color:#f5c761;">PORTAL UPDATE</td>
                </tr>
              </table>
              <div style="width:48px;height:3px;margin-top:22px;background:#f1b82e;border-radius:3px;"></div>
            </td>
          </tr>
          <tr>
            <td style="padding:36px 36px 28px;font-size:16px;line-height:1.65;color:#39263d;">
              <p style="margin:0 0 20px;font-size:16px;">Hello <?php echo esc($name); ?>,</p>
              <p style="margin:0 0 10px;font-size:11px;line-height:1.4;font-weight:700;letter-spacing:1.4px;color:#9d0f82;">FROM EPR GLOBAL</p>
              <h1 style="margin:0 0 18px;font-size:27px;line-height:1.24;font-weight:700;letter-spacing:-.02em;color:#26002d;"><?php echo esc($subject); ?></h1>
              <div style="margin:0;color:#49394d;"><?php echo nl2br(esc($body)); ?></div>
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-top:30px;">
                <tr>
                  <td style="border-radius:8px;background:#9d0f82;">
                    <a href="<?php echo esc($notificationsUrl, 'attr'); ?>" style="display:inline-block;padding:13px 20px;border:1px solid #9d0f82;border-radius:8px;color:#ffffff;font-size:14px;font-weight:700;line-height:1;text-decoration:none;">View in the portal&nbsp;&nbsp;→</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:22px 36px;background:#fbf9fb;border-top:1px solid #ece5ed;color:#77687b;font-size:12px;line-height:1.55;">
              This message was sent to you because you have an EPR Global Portal account. Please do not reply directly to this automated email.
            </td>
          </tr>
        </table>
        <p style="margin:18px 0 0;color:#8c7e90;font-size:11px;line-height:1.5;">© <?php echo date('Y'); ?> EPR Global. All rights reserved.</p>
      </td>
    </tr>
  </table>
</body>
</html>
