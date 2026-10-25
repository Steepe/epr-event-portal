const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../../public/assets/js/accelerate-background.js'), 'utf8');

for (const [screen, expectedColor] of [['Past Conferences', '#2a0b2f'], ['Webinars', '#200a38'], ['Lobby', null]]) {
  test(`${screen} renders safely, including a first frame timestamp before setup`, () => {
    const colors = new Set();
    let queued;
    let nodeQueries = 0;
    const context = new Proxy({
      arc(x, y, radius) { assert.ok(radius >= 0 && Number.isFinite(radius), `Invalid radius ${radius}`); },
      createLinearGradient() { return { addColorStop(offset, color) { colors.add(color); } }; },
      createRadialGradient() { return { addColorStop() {} }; },
    }, { get(target, key) { return key in target ? target[key] : () => {}; } });
    const canvas = () => ({ clientWidth: 1440, clientHeight: 900, getContext: () => context,
      setAttribute() {}, getBoundingClientRect: () => ({ left: 0, top: 0 }), remove() {} });
    const stage = {
      prepend() {},
      querySelector(selector) { return selector === '.rc-stage-inner' ? stage : { getAttribute: () => screen }; },
      querySelectorAll() { nodeQueries++; return []; },
    };
    vm.runInNewContext(source, {
      document: { readyState: 'complete', querySelectorAll: () => [stage], createElement: canvas,
        documentElement: { addEventListener() {}, removeEventListener() {} } },
      window: { devicePixelRatio: 1, matchMedia: () => ({ matches: false }), addEventListener() {}, removeEventListener() {} },
      ResizeObserver: class { observe() {} disconnect() {} },
      Path2D: class {}, atob, performance: { now: () => 100 },
      requestAnimationFrame: callback => { queued = callback; return 1; }, cancelAnimationFrame() {},
    });
    queued(95);
    for (let frame = 1; frame <= 12; frame++) queued(100 + frame * 16);
    if (expectedColor) assert.ok(colors.has(expectedColor));
    else { assert.ok(!colors.has('#2a0b2f')); assert.equal(nodeQueries, 0, 'Lobby must not draw bubble connections'); }
    stage.__accelerateBackground.destroy();
    assert.equal(stage.__accelerateBackground, undefined);
  });
}
