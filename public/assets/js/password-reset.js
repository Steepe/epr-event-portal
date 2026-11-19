(function () {
    'use strict';
    const form = document.querySelector('[data-password-reset]');
    if (!form) return;
    const button = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-reset-status]');
    const token = form.elements.token.value;
    if (!/^[a-f0-9]{64}$/.test(token)) {
        status.textContent = 'This reset link is missing or invalid. Please request a new link.';
        button.disabled = true;
        return;
    }
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (button.disabled) return;
        const data = Object.fromEntries(new FormData(form));
        if (data.password !== data.confirm_password) {
            status.textContent = 'Passwords do not match.';
            return;
        }
        const passwordBytes = new TextEncoder().encode(data.password).length;
        if (passwordBytes < 8 || passwordBytes > 72) {
            status.textContent = 'Use a password between 8 and 72 bytes long.';
            return;
        }
        button.disabled = true;
        button.textContent = 'Saving…';
        status.textContent = '';
        let saved = false;
        try {
            const response = await fetch(form.dataset.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-API-KEY': form.dataset.apiKey },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (response.ok && result.status === 'success') {
                saved = true;
                form.reset();
                status.textContent = 'Password reset successful. Returning to login…';
                setTimeout(() => window.location.assign(form.dataset.loginUrl), 1500);
            } else {
                status.textContent = result.message || 'Unable to reset your password. Please request a new link.';
            }
        } catch (error) {
            status.textContent = 'Unable to reach the server. Please try again.';
        } finally {
            button.disabled = saved;
            button.textContent = saved ? 'Password saved' : 'Reset Password';
        }
    });
})();
