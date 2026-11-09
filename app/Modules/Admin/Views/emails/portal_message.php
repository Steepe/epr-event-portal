<!doctype html>
<html lang="en">
<body style="margin:0;background:#f5f3f6;padding:32px 16px;font-family:Arial,sans-serif;color:#28212d;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">
    <table role="presentation" width="600" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden;">
      <tr><td style="padding:24px 32px;background:#28002e;color:#fff;"><strong style="font-size:20px;">EPR Global</strong></td></tr>
      <tr><td style="padding:32px;line-height:1.6;font-size:15px;">
        <p>Hello <?php echo esc($name); ?>,</p>
        <p>You have a new message in the EPR Global Portal.</p>
        <h1 style="font-size:21px;line-height:1.3;color:#28002e;"><?php echo esc($subject); ?></h1>
        <div><?php echo nl2br(esc($body)); ?></div>
        <p style="margin-top:28px;"><a href="<?php echo esc($notificationsUrl, 'attr'); ?>" style="display:inline-block;background:#9d0f82;color:#fff;padding:12px 18px;border-radius:6px;text-decoration:none;font-weight:bold;">View in the portal</a></p>
      </td></tr>
      <tr><td style="padding:18px 32px;background:#f5f3f6;color:#695f70;font-size:12px;">This email was sent because you have an EPR Global Portal account.</td></tr>
    </table>
  </td></tr></table>
</body>
</html>
