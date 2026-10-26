<?php

namespace App\Services;

use CodeIgniter\Email\Email;

class EmergenceRegistrationMailer
{
    public function __construct(private readonly Email $email)
    {
    }

    /**
     * Sends the supplied registration copy. A password setup link is included
     * only for attendees whose portal account was just created.
     */
    public function send(string $recipient, string $firstName, ?string $passwordSetupUrl = null): bool
    {
        $fromEmail = trim((string) env('email.fromEmail')) ?: 'no-reply@eprglobal.com';
        $fromName = trim((string) env('email.fromName')) ?: 'EPR Global Events';

        $this->email->clear(true);
        $this->email->setTo($recipient);
        $this->email->setFrom($fromEmail, $fromName);
        $this->email->setSubject('Your Emergence Conference 2026 registration is confirmed');
        $this->email->setMailType('html');
        $this->email->setMessage($this->message($firstName, $passwordSetupUrl));

        return $this->email->send();
    }

    private function message(string $firstName, ?string $passwordSetupUrl): string
    {
        $name = esc($firstName ?: 'Participant');
        $portalUrl = esc(site_url('attendees/login'));
        $headerUrl = esc(base_url('assets/images/emergence-2026-registration-header.jpeg'));
        $setupBlock = '';

        if ($passwordSetupUrl !== null) {
            $setupUrl = esc($passwordSetupUrl);
            $setupBlock = <<<HTML
                <tr>
                    <td style="padding:0 34px 8px;color:#29212d;font:16px/25px Arial,sans-serif;">
                        <strong>Your portal account is ready.</strong> Set your password to access it.
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:12px 34px 24px;">
                        <a href="{$setupUrl}" style="display:inline-block;background:#6b1c72;color:#ffffff;text-decoration:none;border-radius:5px;padding:13px 24px;font:700 14px Arial,sans-serif;">Create your password</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0 34px 24px;color:#554b58;font:14px/21px Arial,sans-serif;">
                        This one-time link expires in one hour. If it expires, use <a href="{$portalUrl}" style="color:#6b1c72;">Forgot Password</a> on the portal login page.
                    </td>
                </tr>
            HTML;
        }

        return <<<HTML
            <!doctype html>
            <html lang="en">
            <body style="margin:0;padding:24px;background:#f4f1f4;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center">
                            <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="max-width:640px;width:100%;background:#ffffff;">
                                <tr>
                                    <td>
                                        <img src="{$headerUrl}" alt="The Emergence Conference 2026 — Accelerate" width="640" style="display:block;width:100%;max-width:640px;height:auto;border:0;">
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:34px 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">Dear {$name},</td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        Thank you for registering for <strong>The Emergence Conference 2026</strong>. We are delighted that you will be joining a global community committed to building sustainable systems for execution.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 22px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        <strong>Saturday, 21 November 2026</strong><br>
                                        EPR Global Event Portal<br>
                                        2:00 PM GMT &nbsp;|&nbsp; 9:00 AM EST &nbsp;|&nbsp; 3:00 PM WAT &nbsp;|&nbsp; 4:00 PM SAST &nbsp;|&nbsp; 5:00 PM EAT
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 24px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        Explore the <a href="{$portalUrl}" style="color:#6b1c72;font-weight:700;">EPR Global Event Portal</a> for conference updates and access details.
                                    </td>
                                </tr>
                                {$setupBlock}
                                <tr>
                                    <td style="padding:0 34px 34px;color:#554b58;font:14px/21px Arial,sans-serif;">
                                        For registration questions, contact <a href="mailto:registration@eprglobal.com" style="color:#6b1c72;">registration@eprglobal.com</a>.<br><br>
                                        Warm regards,<br><strong>The Emergence Conference Team</strong><br>EPR Global
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        HTML;
    }
}
