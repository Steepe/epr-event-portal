const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../../public/assets/js/password-reset.js'), 'utf8');

function setup(overrides = {}, response = { ok: true, json: async () => ({ status: 'success' }) }) {
  const data = { token: 'a'.repeat(64), password: 'NewPassword123', confirm_password: 'NewPassword123', ...overrides };
  const button = { disabled: false, textContent: 'Reset Password' };
  const status = { textContent: '' };
  let submit, sent = 0, navigated, reset = false;
  const form = {
    elements: { token: { value: data.token } },
    dataset: { endpoint: '/api/password/reset', apiKey: 'test-only', loginUrl: '/attendees/login' },
    querySelector: selector => selector === '[data-reset-status]' ? status : button,
    addEventListener(event, callback) { submit = callback; }, reset() { reset = true; },
  };
  vm.runInNewContext(source, {
    document: { querySelector: () => form }, TextEncoder,
    FormData: class { [Symbol.iterator]() { return Object.entries(data)[Symbol.iterator](); } },
    fetch: async () => { sent++; if (response instanceof Error) throw response; return response; },
    setTimeout: callback => callback(), window: { location: { assign: url => { navigated = url; } } },
  });
  return { button, status, submit: () => submit({ preventDefault() {} }), sent: () => sent,
    navigated: () => navigated, reset: () => reset };
}

test('missing token disables password submission', () => {
  const form = setup({ token: '' });
  assert.equal(form.button.disabled, true);
  assert.match(form.status.textContent, /request a new link/i);
});
test('password confirmation is checked before any request', async () => {
  const form = setup({ confirm_password: 'DoesNotMatch123' });
  await form.submit();
  assert.equal(form.sent(), 0);
  assert.match(form.status.textContent, /do not match/);
});
test('expired link displays the server message and allows retry', async () => {
  const form = setup({}, { ok: false, json: async () => ({ status: 'error', message: 'This link has expired.' }) });
  await form.submit();
  assert.equal(form.button.disabled, false);
  assert.equal(form.status.textContent, 'This link has expired.');
  assert.equal(form.navigated(), undefined);
});
test('network failure restores the button and explains the problem', async () => {
  const form = setup({}, new Error('offline'));
  await form.submit();
  assert.equal(form.button.disabled, false);
  assert.match(form.status.textContent, /reach the server/);
});
test('successful reset clears password fields and returns to login', async () => {
  const form = setup();
  await form.submit();
  assert.equal(form.sent(), 1);
  assert.equal(form.button.disabled, true);
  assert.equal(form.reset(), true);
  assert.equal(form.navigated(), '/attendees/login');
});
