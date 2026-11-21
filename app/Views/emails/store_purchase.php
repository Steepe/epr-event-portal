<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Your EPR Global purchase is confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f5f1f5;color:#37243d;font-family:Arial,Helvetica,sans-serif;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">Your payment is complete and your purchase is ready in the portal.</div>
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f1f5;">
    <tr><td align="center" style="padding:28px 12px;">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#ffffff;">
        <tr><td style="height:6px;background:#d79838;"></td></tr>
        <tr><td style="padding:26px 30px;background:#57236d;color:#ffffff;">
          <img src="<?= esc(base_url('assets/images/eventslogo.png'), 'attr') ?>" width="190" alt="EPR Global Events Portal" style="display:block;width:190px;max-width:100%;height:auto;border:0;">
          <p style="margin:25px 0 6px;font-size:11px;font-weight:bold;letter-spacing:2px;color:#f5cf70;">EMERGENCE 2026</p>
          <h1 style="margin:0;font-size:28px;line-height:36px;color:#ffffff;">Purchase confirmed</h1>
        </td></tr>
        <tr><td style="padding:30px;color:#47334b;font-size:15px;line-height:24px;">
          <p style="margin:0 0 16px;">Hello <?= esc($name) ?>,</p>
          <p style="margin:0 0 22px;">Thank you. Your payment was successful, and your purchase has been added to your portal account.</p>
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbf8f3;border:1px solid #eadbbb;">
            <tr><td style="padding:20px;">
              <p style="margin:0 0 6px;font-size:11px;font-weight:bold;letter-spacing:1px;color:#852a8a;">YOUR PURCHASE</p>
              <p style="margin:0 0 14px;font-size:19px;line-height:26px;font-weight:bold;color:#57236d;"><?= esc($productName) ?></p>
              <p style="margin:0 0 5px;"><strong>Paid:</strong> <?= esc($amount) ?></p>
              <p style="margin:0;word-break:break-all;"><strong>Reference:</strong> <?= esc($reference) ?></p>
            </td></tr>
          </table>
          <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:25px 0 18px;"><tr><td style="background:#d79838;border-radius:6px;">
            <a href="<?= esc($accessUrl, 'attr') ?>" style="display:inline-block;padding:14px 23px;color:#57236d;font-size:15px;font-weight:bold;text-decoration:none;">VIEW YOUR PURCHASE&nbsp;→</a>
          </td></tr></table>
          <p style="margin:0 0 16px;">Sign in with the email address used for your purchase. You can also open <a href="<?= esc($accessUrl, 'attr') ?>" style="color:#57236d;">the portal store</a> directly.</p>
          <p style="margin:0;">Need help? Email <a href="mailto:registration@eprglobal.com" style="color:#57236d;">registration@eprglobal.com</a>.</p>
        </td></tr>
        <tr><td style="padding:18px 30px;background:#f7f3f7;color:#796d7b;font-size:12px;line-height:18px;">EPR Global Events · Emergence 2026</td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
