<!doctype html>
<html lang="en">
<body style="margin:0;padding:0;background:#f5f1f6;font-family:Arial,Helvetica,sans-serif;color:#2d2131;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f5f1f6;">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 8px 28px rgba(41,18,49,.12);">
          <tr><td style="padding:7px 36px;background:linear-gradient(90deg,#76187c,#df168c);font-size:0;line-height:0;">&nbsp;</td></tr>
          <tr><td align="center" style="padding:30px 32px 22px;background:#250b33;color:#ffffff;"><img src="<?= esc(base_url('assets/images/eventslogo.png'), 'attr') ?>" width="156" alt="EPR Global" style="display:block;width:156px;max-width:100%;height:auto;border:0;"></td></tr>
          <tr><td style="padding:34px 38px 10px;"><h1 style="margin:0;font-size:26px;line-height:34px;color:#54205e;font-weight:700;">Reset your admin password</h1></td></tr>
          <tr><td style="padding:6px 38px 18px;font-size:16px;line-height:25px;color:#4e4351;">Hello <?= esc($name) ?>, we received a request to reset your EPR Event Portal admin password.</td></tr>
          <tr><td align="center" style="padding:10px 38px 30px;"><a href="<?= esc($resetLink, 'attr') ?>" style="display:inline-block;padding:14px 26px;background:#771a7d;border-radius:6px;color:#ffffff;font-size:15px;font-weight:700;line-height:20px;text-decoration:none;">Reset password</a></td></tr>
          <tr><td style="padding:0 38px 34px;font-size:14px;line-height:22px;color:#6d606f;">This secure link expires in <strong style="color:#4e4351;">one hour</strong> and can be used once. If you did not request it, you can safely ignore this email.</td></tr>
          <tr><td align="center" style="padding:18px 24px;background:#faf8fa;border-top:1px solid #eee7ef;font-size:12px;line-height:18px;color:#817484;">EPR Global Events</td></tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
