<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emergence Registration</title>
    <style>
        :root {
            color-scheme: light;
            --pink: #E5337B;
            --pink-dark: #C41F63;
            --ink: #1E1A2E;
            --muted: #6B6778;
            --line: #E1DCD3;
            --paper: #FFFFFF;
            --deep: #2B1A45;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            background: transparent;
        }

        body {
            color: var(--ink);
            font-family: Poppins, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            padding: 4px;
        }

        .funnel {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            background: var(--paper);
            border: 1px solid #E7E2DA;
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(43, 26, 69, .06);
            padding: clamp(22px, 5vw, 36px);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
        }

        form,
        .stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        input,
        select {
            height: 46px;
            border: 1px solid var(--line);
            border-radius: 4px;
            background: #FFFFFF;
            color: var(--ink);
            font: inherit;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: 0;
            outline: none;
            padding: 0 14px;
            text-transform: none;
        }

        input:focus,
        select:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(229, 51, 123, .12);
        }

        input::placeholder {
            color: #A8A3B3;
        }

        .check-row {
            align-items: flex-start;
            cursor: pointer;
            display: flex;
            flex-direction: row;
            gap: 10px;
            letter-spacing: 0;
            line-height: 1.5;
            text-transform: none;
        }

        .check-row input {
            accent-color: var(--pink);
            flex: none;
            height: 18px;
            margin-top: 1px;
            width: 18px;
        }

        button,
        .button {
            align-items: center;
            border: 0;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            gap: 10px;
            height: 48px;
            justify-content: center;
            text-decoration: none;
        }

        button[type="submit"],
        .upgrade {
            background: var(--pink);
            color: #FFFFFF;
        }

        button[type="submit"]:hover,
        .upgrade:hover {
            background: var(--pink-dark);
            color: #FFFFFF;
        }

        button[disabled] {
            cursor: wait;
            opacity: .75;
        }

        .error {
            color: #C41F3A;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0;
            text-transform: none;
        }

        .alert {
            background: #FCEBEF;
            border-radius: 4px;
            color: #C41F3A;
            font-size: 13px;
            padding: 12px 14px;
        }

        .spinner {
            animation: spin .7s linear infinite;
            border: 2px solid rgba(255, 255, 255, .4);
            border-radius: 50%;
            border-top-color: #FFFFFF;
            display: none;
            height: 15px;
            width: 15px;
        }

        .is-submitting .spinner {
            display: inline-block;
        }

        .upsell {
            display: none;
            flex-direction: column;
            gap: 22px;
        }

        .success-line {
            align-items: center;
            color: var(--pink);
            display: flex;
            font-size: 12px;
            font-weight: 700;
            gap: 8px;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .success-dot {
            align-items: center;
            background: var(--pink);
            border-radius: 50%;
            color: #FFFFFF;
            display: flex;
            font-size: 10px;
            height: 18px;
            justify-content: center;
            letter-spacing: 0;
            width: 18px;
        }

        h2 {
            color: var(--ink);
            font-size: clamp(22px, 4.5vw, 28px);
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        .offer {
            background: var(--deep);
            border-radius: 6px;
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: clamp(20px, 4vw, 28px);
        }

        .offer-head {
            align-items: flex-start;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
        }

        .eyebrow {
            color: #FF6FA8;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .ticket {
            font-size: 22px;
            font-weight: 700;
        }

        .price {
            font-size: 34px;
            font-weight: 800;
            line-height: 1;
        }

        .benefits {
            color: #E9E4F2;
            display: flex;
            flex-direction: column;
            font-size: 14px;
            gap: 10px;
            line-height: 1.5;
        }

        .benefits div {
            display: flex;
            gap: 10px;
        }

        .benefits span:first-child {
            color: #FF6FA8;
        }

        .plain {
            align-self: center;
            background: none;
            color: var(--muted);
            font-size: 13px;
            height: auto;
            padding: 10px;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .done {
            display: none;
            flex-direction: column;
            gap: 12px;
            padding: 12px 0;
        }

        .done p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<main class="funnel">
    <form id="funnelForm" novalidate>
        <div class="grid">
            <label>First name
                <input name="first_name" autocomplete="given-name" placeholder="Your first name" required>
                <span class="error" data-error="first_name"></span>
            </label>
            <label>Last name
                <input name="last_name" autocomplete="family-name" placeholder="Your last name" required>
                <span class="error" data-error="last_name"></span>
            </label>
            <label>Email
                <input type="email" name="email" autocomplete="email" placeholder="you@email.com" required>
                <span class="error" data-error="email"></span>
            </label>
            <label>Phone
                <input type="tel" name="phone" autocomplete="tel" placeholder="+234 800 000 0000">
                <span class="error" data-error="phone"></span>
            </label>
            <label>City
                <input name="city" autocomplete="address-level2" placeholder="e.g. Lagos">
            </label>
            <label>Country
                <input name="country" autocomplete="country-name" placeholder="e.g. Nigeria">
            </label>
        </div>

        <label>How did you hear about us?
            <select name="referral_source">
                <option value="">Select one</option>
                <option value="instagram">Instagram</option>
                <option value="facebook">Facebook</option>
                <option value="linkedin">LinkedIn</option>
                <option value="friend">Friend or colleague</option>
                <option value="email">Email newsletter</option>
                <option value="search">Google search</option>
                <option value="other">Other</option>
            </select>
        </label>

        <label class="check-row">
            <input type="checkbox" name="marketing_consent">
            <span>Send me updates on speakers, registration and the agenda.</span>
        </label>

        <div class="alert" id="submitError" hidden></div>

        <button type="submit" id="submitButton">
            <span class="spinner"></span>
            <span id="submitLabel">Submit</span>
        </button>
    </form>

    <section class="upsell" id="upsell" aria-live="polite">
        <div class="stack">
            <div class="success-line">
                <span class="success-dot">✓</span>
                <span id="confirmLine">You're registered</span>
            </div>
            <h2 id="upsellTitle">One more thing.</h2>
        </div>

        <div class="offer">
            <div class="offer-head">
                <div>
                    <div class="eyebrow">Upgrade your seat</div>
                    <div class="ticket">VIP Ticket</div>
                </div>
                <div class="price"><?php echo esc($upsellPrice); ?></div>
            </div>
            <div class="benefits">
                <div><span>✓</span><span>Hands-on workshop access</span></div>
                <div><span>✓</span><span>Full session recordings, yours to keep</span></div>
            </div>
            <a class="button upgrade" id="upgradeLink" target="_top" href="<?php echo esc($checkoutUrl); ?>">
                Upgrade for <?php echo esc($upsellPrice); ?>
            </a>
        </div>

        <button class="plain" type="button" id="declineButton">No thanks, keep my free registration</button>
    </section>

    <section class="done" id="done" aria-live="polite">
        <span class="success-dot" style="height:40px;width:40px;font-size:18px;">✓</span>
        <h2>You're registered.</h2>
        <p>Confirmation sent to <strong id="doneEmail"></strong>.</p>
    </section>
</main>

<script>
(() => {
    const apiEndpoint = <?php echo json_encode($apiEndpoint); ?>;
    const checkoutUrl = <?php echo json_encode($checkoutUrl); ?>;
    const mailchimpTags = <?php echo json_encode($mailchimpTags); ?>;
    const form = document.getElementById('funnelForm');
    const submitButton = document.getElementById('submitButton');
    const submitLabel = document.getElementById('submitLabel');
    const submitError = document.getElementById('submitError');
    const upsell = document.getElementById('upsell');
    const done = document.getElementById('done');
    const upgradeLink = document.getElementById('upgradeLink');
    const declineButton = document.getElementById('declineButton');
    const confirmLine = document.getElementById('confirmLine');
    const upsellTitle = document.getElementById('upsellTitle');
    const doneEmail = document.getElementById('doneEmail');
    let currentPayload = null;

    const postHeight = () => {
        try {
            window.parent.postMessage({
                type: 'emergence-funnel:height',
                height: document.documentElement.scrollHeight
            }, '*');
        } catch (e) {}
    };

    new ResizeObserver(postHeight).observe(document.body);
    window.addEventListener('load', postHeight);

    const emit = (type, data = {}) => {
        try {
            window.parent.postMessage({ type: 'emergence-funnel:' + type, ...data }, '*');
        } catch (e) {}
    };

    const setSubmitting = (submitting) => {
        submitButton.disabled = submitting;
        submitButton.classList.toggle('is-submitting', submitting);
        submitLabel.textContent = submitting ? 'Submitting...' : 'Submit';
    };

    const clearErrors = () => {
        document.querySelectorAll('[data-error]').forEach(el => el.textContent = '');
        submitError.hidden = true;
        submitError.textContent = '';
    };

    const setErrors = (errors) => {
        Object.entries(errors).forEach(([key, value]) => {
            const el = document.querySelector(`[data-error="${key}"]`);
            if (el) el.textContent = value;
        });
    };

    const validate = (payload) => {
        const errors = {};
        if (!payload.first_name) errors.first_name = 'Required';
        if (!payload.last_name) errors.last_name = 'Required';
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) errors.email = 'Enter a valid email';
        if (payload.phone && payload.phone.replace(/\D/g, '').length < 7) errors.phone = 'Enter a valid phone number';
        return errors;
    };

    const buildPayload = () => {
        const data = new FormData(form);
        const params = new URLSearchParams(window.location.search);
        return {
            email: String(data.get('email') || '').trim().toLowerCase(),
            first_name: String(data.get('first_name') || '').trim(),
            last_name: String(data.get('last_name') || '').trim(),
            phone: String(data.get('phone') || '').trim(),
            city: String(data.get('city') || '').trim(),
            country: String(data.get('country') || '').trim(),
            referral_source: String(data.get('referral_source') || '').trim(),
            marketing_consent: data.has('marketing_consent'),
            mailchimp: {
                tags: mailchimpTags.split(',').map(tag => tag.trim()).filter(Boolean),
                status_if_new: data.has('marketing_consent') ? 'subscribed' : 'transactional'
            },
            utm: {
                source: params.get('utm_source'),
                medium: params.get('utm_medium'),
                campaign: params.get('utm_campaign')
            },
            submitted_at: new Date().toISOString()
        };
    };

    const showUpsell = (result) => {
        const firstName = currentPayload.first_name || 'there';
        form.style.display = 'none';
        upsell.style.display = 'flex';
        confirmLine.textContent = result.status === 'updated' ? "Welcome back - you're registered" : "You're registered";
        upsellTitle.textContent = `One more thing, ${firstName}.`;

        try {
            const url = new URL(checkoutUrl, window.location.href);
            url.searchParams.set('email', currentPayload.email);
            url.searchParams.set('name', `${currentPayload.first_name} ${currentPayload.last_name}`.trim());
            upgradeLink.href = url.toString();
        } catch (e) {
            upgradeLink.href = checkoutUrl || '#';
        }

        emit('registered', { status: result.status, email: currentPayload.email });
        postHeight();
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();
        currentPayload = buildPayload();

        const errors = validate(currentPayload);
        if (Object.keys(errors).length) {
            setErrors(errors);
            postHeight();
            return;
        }

        setSubmitting(true);

        try {
            const response = await fetch(apiEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(currentPayload)
            });
            const result = await response.json();

            if (!response.ok) {
                if (result.messages && typeof result.messages === 'object') {
                    setErrors(result.messages);
                }
                throw new Error(result.message || 'Unable to submit registration.');
            }

            showUpsell(result);
        } catch (error) {
            submitError.textContent = 'Something went wrong. Please try again.';
            submitError.hidden = false;
            postHeight();
        } finally {
            setSubmitting(false);
        }
    });

    upgradeLink.addEventListener('click', () => {
        if (currentPayload) emit('upsell_click', { email: currentPayload.email });
    });

    declineButton.addEventListener('click', () => {
        if (currentPayload) emit('upsell_decline', { email: currentPayload.email });
        upsell.style.display = 'none';
        done.style.display = 'flex';
        doneEmail.textContent = currentPayload ? currentPayload.email : '';
        postHeight();
    });
})();
</script>
</body>
</html>
