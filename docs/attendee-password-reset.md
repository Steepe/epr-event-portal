# Attendee password reset deployment checks

These changes are local to the staging branch. Do not push main or deploy production as part of verification.

## Before testing on staging

1. Back up the staging database and review its pending migrations.
2. Apply the new `2026-10-25-000001_EnsureAttendeePasswordResets` migration through the normal deployment process. It creates `password_resets` if missing, or adds the missing timestamps without deleting existing links. Rolling it back intentionally preserves the table and its data.
3. Confirm staging's `app.baseURL` is its externally reachable HTTPS URL. Reset emails use this value.
4. Confirm `email.fromEmail`, `email.fromName`, and the mail transport settings are configured on staging. SMTP deployments need `email.protocol`, `email.SMTPHost`, `email.SMTPUser`, `email.SMTPPass`, `email.SMTPPort`, and the correct `email.SMTPCrypto`. Do not paste credentials into logs or source control.
5. Keep the existing `api.securityKey` configuration consistent with the portal login forms; password endpoints retain the portal's API filter.

## Acceptance test

- Use a dedicated staging test account, not a production account.
- Open **Forgot Password?** while logged out and request a link. Confirm it arrives in the inbox/spam folder with a clickable link to staging.
- Open it on desktop and mobile. The mobile redirect must preserve the token.
- Save matching passwords, then sign in with the new password. The previous password must fail.
- Reopening a consumed link must not change the password. An expired link must offer a way to request another one.
- A failed mail send must not claim success. Passwords, raw tokens, and SMTP credentials must not appear in application logs.

Local tests use an isolated in-memory database and a mocked mail sender. They do not prove delivery from staging's actual mail transport.

## Local checks

```sh
vendor/bin/phpunit --no-configuration --bootstrap system/Test/bootstrap.php tests/unit
node --test tests/js/*.test.cjs
```
