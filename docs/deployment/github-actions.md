# GitHub Actions deployment

The workflow at `.github/workflows/ci-deploy.yml` replaces the Buddy pipeline:

| Branch | Target | What happens |
| --- | --- | --- |
| `staging` | Staging | PHP 8.3 and 8.5 tests run, then the portal deploys to staging. |
| `main` | Production | PHP 8.3 and 8.5 tests run, then the portal deploys to production. |
| Pull request to either branch | No server | Tests only. |

It transfers the current commit over SSH with `rsync`. It deliberately preserves server-specific and user-generated data: `.env`, `writable/`, and `public/uploads/`. It does not use `--delete` and it does not reset the server's Git working tree.

## One-time GitHub setup

In the GitHub repository, open **Settings → Secrets and variables → Actions**. Add these repository secrets:

| Staging secret | Production counterpart | Value |
| --- | --- | --- |
| `STAGING_SSH_HOST` | `PRODUCTION_SSH_HOST` | Server hostname or IP address. |
| `STAGING_SSH_PORT` | `PRODUCTION_SSH_PORT` | SSH port; normally `22`. |
| `STAGING_SSH_USER` | `PRODUCTION_SSH_USER` | cPanel/SSH username. |
| `STAGING_DEPLOY_PATH` | `PRODUCTION_DEPLOY_PATH` | Absolute project root that contains `spark`, for example `/home/account/public_html`. |
| `STAGING_PHP_BINARY` | `PRODUCTION_PHP_BINARY` | Full PHP CLI path used by that server, for example `/opt/cpanel/ea-php83/root/usr/bin/php`. |
| `STAGING_SSH_PRIVATE_KEY` | `PRODUCTION_SSH_PRIVATE_KEY` | A dedicated deployment key's private-key contents. |
| `STAGING_SSH_KNOWN_HOSTS` | `PRODUCTION_SSH_KNOWN_HOSTS` | The verified `ssh-keyscan -H SERVER_HOST` output. |

Create a dedicated Ed25519 key pair for GitHub Actions. Add only its public key to the matching cPanel account under **SSH Access → Manage SSH Keys → Authorize**. Store the private key only in the corresponding GitHub secret.

Before trusting the known-hosts value, verify the server fingerprint through your hosting provider or an existing trusted SSH session. Do not disable host-key checking.

## Deployment protection

The workflow labels deployments as the GitHub `staging` and `production` environments. In **Settings → Environments**, create both environments. For `production`, restrict deployments to `main` and add required reviewers if your GitHub plan supports protection rules for the repository.

## First deployment

1. Commit the workflow and the intended portal changes.
2. Add the secrets above.
3. Push `staging` first and review the Actions log and staging site.
4. Push or merge the approved commit to `main` only when it is ready for production.

The migration command runs after the file transfer. If it fails, the workflow fails visibly; do not re-run production blindly—read the migration error first.
