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
                                        Thank you for registering for <strong>The Emergence Conference 2026</strong>! We are delighted to have you join us for this important gathering of women who are ready to move from vision to purposeful execution.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        Under the theme <strong>&ldquo;Accelerate: Building Sustainable Systems for Execution,&rdquo;</strong> this year&rsquo;s Emergence Conference will explore what it takes to turn ideas, aspirations, and vision into meaningful and sustainable results.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 22px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        As a participant, you can expect to gain practical insights, fresh perspective, and actionable strategies to help you strengthen the systems behind your goals, improve execution, and build sustainable impact across your career, business, organisation, and community. You will also have the opportunity to learn, reflect, and connect with other women on a similar journey.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 22px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        <strong>Details</strong><br><br>
                                        <strong>Saturday, 21st November 2026</strong><br>
                                        EPR Global Event Portal<br>
                                        2:00 PM GMT &nbsp;|&nbsp; 9:00 AM EST &nbsp;|&nbsp; 3:00 PM WAT &nbsp;|&nbsp; 4:00 PM SAST &nbsp;|&nbsp; 5:00 PM EAT
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        We encourage you to save the date in your calendar now so you can be fully present for the conference.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        <strong>Explore the EPR Global Event Portal</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        While you wait for the conference, we invite you to explore the <a href="{$portalUrl}" style="color:#6b1c72;font-weight:700;">EPR Global Event Portal</a> at <a href="{$portalUrl}" style="color:#6b1c72;">https://portal.eprglobal.com/</a>.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 18px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        Take some time to navigate through the portal and discover content from previous webinars and conferences, as well as other resources and learning opportunities from EPR Global. It&rsquo;s a great way to get familiar with the EPR experience and begin engaging with some of the conversations and insights that have shaped our community.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 34px 24px;color:#29212d;font:16px/25px Arial,sans-serif;">
                                        Closer to the conference, we will share additional information, including joining details, program highlights, speakers, and any other important updates. For now, consider this your confirmation that you&rsquo;re registered and we&rsquo;re expecting you.
                                    </td>
                                </tr>
                                {$setupBlock}
                                <tr>
                                    <td style="padding:0 34px 34px;color:#554b58;font:14px/21px Arial,sans-serif;">
                                        For any questions or clarifications, please feel free to reach out to <a href="mailto:registration@eprglobal.com" style="color:#6b1c72;">registration@eprglobal.com</a>.<br><br>
                                        We look forward to learning, growing, and accelerating with you as we explore how to build sustainable systems for execution.<br><br>
                                        Warmly,<br><strong>The Emergence Conference Team</strong><br>EPR Global
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
