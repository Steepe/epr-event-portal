(() => {
    const setup = () => {
        if (!document.getElementById('epr-password-toggle-styles')) {
            const styles = document.createElement('style');
            styles.id = 'epr-password-toggle-styles';
            styles.textContent = `
                .epr-password-control { position: relative; display: block; width: 100%; min-width: 0; }
                .epr-password-control > input { box-sizing: border-box; padding-right: 52px !important; }
                .epr-password-toggle { position: absolute; top: 0; right: 0; z-index: 3; display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 100%; margin: 0; padding: 0 7px; border: 0; background: transparent; color: inherit; font: inherit; font-size: 12px; font-weight: 700; cursor: pointer; }
                .epr-password-toggle:hover, .epr-password-toggle:focus-visible { color: #efb11e; outline: none; }
                .m-field > .epr-password-control { flex: 1; }
                .m-field > .epr-password-control > .m-input { padding-left: 42px; }
            `;
            document.head.appendChild(styles);
        }

        document.querySelectorAll('input[type="password"]:not([inputmode="numeric"])').forEach((input) => {
            if (input.dataset.passwordToggleReady === '1') return;

            input.dataset.passwordToggleReady = '1';
            const wrapper = document.createElement('span');
            wrapper.className = 'epr-password-control';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'epr-password-toggle';
            button.textContent = 'Show';
            button.setAttribute('aria-label', 'Show password');
            button.setAttribute('aria-pressed', 'false');
            button.addEventListener('click', () => {
                const showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                button.textContent = showing ? 'Show' : 'Hide';
                button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
                button.setAttribute('aria-pressed', showing ? 'false' : 'true');
            });
            wrapper.appendChild(button);
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setup, { once: true });
    } else {
        setup();
    }
})();
