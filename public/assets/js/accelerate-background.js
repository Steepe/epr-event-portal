(function () {
  "use strict";

  // Canvas artwork and motion ported from EPR Accelerate Portal RC.html.
  // Archive and webinar screens use the reference's separate Aurora variants.
  const SCREEN_KEYS = {
    "landing": "start",
    "past conferences": "past",
    "past conference agenda": "past",
    "webinars": "webinars",
    "networking center": "networking",
    "live session": "session",
    "emergence booth": "emergence",
    "exhibitor booth": "exhibitors",
    "reset password": "login",
    "checkout": "profile",
    "checkout result": "profile"
  };

  class PortalBackground {
    constructor(stage) {
      this.stage = stage;
      const canvas = document.createElement("canvas");
      canvas.className = "rc-bg-canvas";
      canvas.setAttribute("aria-hidden", "true");
      stage.prepend(canvas);
      this.canvasRef = { current: canvas };
      this.stageRef = { current: stage.querySelector(".rc-stage-inner") || stage };
      this.props = { bgMode: "Hologram", motion: "Bold", cursorReactive: true };
      this.mount();
    }

    get state() {
      const screen = this.stage.querySelector("main[data-screen-label], form[data-screen-label], [data-screen-label]");
      const label = (screen?.getAttribute("data-screen-label") || "").toLowerCase();
      return { screen: SCREEN_KEYS[label] || label };
    }

    mount() {
    const cv = this.canvasRef.current; if (!cv) return;
    const ctx = cv.getContext('2d');
    const TAU = Math.PI * 2;
    let W = 0, H = 0, lanes = [], parts = [], gates = [], builtFor = null;
    const links = new Map(), rings = [];
    const mouse = { x: -9999, y: -9999, on: false };
    const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const COLS = [['#D8198E', .4], ['#bd159e', .25], ['#EFB11E', .15], ['#ffffff', .2]];
    const pickCol = () => { let r = Math.random(); for (const [c, w] of COLS) { if ((r -= w) <= 0) return c; } return '#D8198E'; };
    const intensity = () => { const m = this.props.motion ?? 'Medium'; const v = m === 'Subtle' ? .55 : m === 'Bold' ? 1.6 : 1; return reduced ? v * .35 : v; };
    const reactive = () => this.props.cursorReactive ?? true;
    const mode = () => this.props.bgMode ?? 'Hologram';
    let E = 0; const ripples = [];
    const rr = (x, y, w, h, r) => { ctx.beginPath(); if (ctx.roundRect) ctx.roundRect(x, y, w, h, r); else ctx.rect(x, y, w, h); };
    const line = (a, b, c, d) => { ctx.beginPath(); ctx.moveTo(a, b); ctx.lineTo(c, d); ctx.stroke(); };

    // ---- Computers ----
    let machines = [], cables = [];
    const CODE = ['rgba(216,25,142,0.6)', 'rgba(239,177,30,0.55)', 'rgba(255,255,255,0.4)', 'rgba(189,21,158,0.55)'];
    const buildPCs = () => {
      machines = []; cables = [];
      const cw = 290, ch = 220, cols = Math.ceil(W / cw) + 1, rows = Math.ceil(H / ch) + 1;
      for (let r = 0; r < rows; r++) for (let c = 0; c < cols; c++) {
        if (Math.random() < 0.16) continue;
        const t = ['mon', 'mon', 'lap', 'rack'][Math.floor(Math.random() * 4)];
        const m = { t, x: c * cw + (r % 2 ? cw / 2 : 0) - cw * 0.25 + (Math.random() - 0.5) * 70, y: r * ch + 40 + (Math.random() - 0.5) * 50, s: 0.75 + Math.random() * 0.45,
          sc: Math.random() * 200, sp: 5 + Math.random() * 12, glow: 0, flash: 0, blink: Math.random() * 10,
          lines: Array.from({ length: 16 }, () => ({ ind: Math.floor(Math.random() * 4), len: 0.15 + Math.random() * 0.6, c: CODE[Math.floor(Math.random() * CODE.length)] })) };
        m.px = m.x; m.py = m.y + (t === 'mon' ? 44 : t === 'lap' ? 26 : 60) * m.s;
        machines.push(m);
      }
      const seen = new Set();
      machines.forEach((a, i) => {
        machines.map((b, j) => [j, Math.hypot(b.x - a.x, b.y - a.y)]).filter(([j, d]) => j !== i && d < cw * 1.5).sort((p, q) => p[1] - q[1]).slice(0, 2).forEach(([j]) => {
          const key = Math.min(i, j) + '-' + Math.max(i, j); if (seen.has(key)) return; seen.add(key);
          const b = machines[j], sag = Math.max(a.py, b.py) + 30 + Math.random() * 40;
          cables.push({ a: i, b: j, P: [a.px, a.py, a.px + (b.px - a.px) * 0.2, sag, a.px + (b.px - a.px) * 0.8, sag, b.px, b.py], pulses: [], next: Math.random() * 3 });
        });
      });
    };
    const drawPCs = (dt, k) => {
      ctx.lineCap = 'round'; ctx.lineJoin = 'round';
      for (const cb of cables) {
        const P = cb.P; ctx.beginPath(); ctx.moveTo(P[0], P[1]); ctx.bezierCurveTo(P[2], P[3], P[4], P[5], P[6], P[7]);
        ctx.strokeStyle = 'rgba(255,255,255,0.08)'; ctx.lineWidth = 1.2; ctx.stroke();
        cb.next -= dt * k * (1 + E * 0.5);
        if (cb.next <= 0) { cb.pulses.push({ t: 0, dir: Math.random() < 0.5 ? 1 : -1, c: Math.random() < 0.35 ? '#EFB11E' : '#D8198E' }); cb.next = 2 + Math.random() * 4; }
      }
      ctx.globalCompositeOperation = 'lighter';
      for (const cb of cables) for (let i = cb.pulses.length - 1; i >= 0; i--) {
        const pu = cb.pulses[i]; pu.t += dt * (0.28 + 0.12 * k) * (1 + E * 0.4);
        const dst = machines[pu.dir > 0 ? cb.b : cb.a], src = machines[pu.dir > 0 ? cb.a : cb.b];
        if (pu.t >= 1) { if (dst) { dst.flash = 1; dst.fc = pu.c; } cb.pulses.splice(i, 1); continue; }
        if (dst && pu.t > 0.75) { dst.near = Math.max(dst.near || 0, (pu.t - 0.75) / 0.25); dst.fc = pu.c; }
        if (src && pu.t < 0.12) { src.near = Math.max(src.near || 0, 1 - pu.t / 0.12); src.fc = src.fc || pu.c; }
        { let ht = pu.t; if (pu.dir < 0) ht = 1 - ht; const hq = bez(cb.P, ht); const hg = ctx.createRadialGradient(hq.x, hq.y, 0, hq.x, hq.y, 18); hg.addColorStop(0, pu.c === '#EFB11E' ? 'rgba(239,177,30,0.55)' : 'rgba(216,25,142,0.55)'); hg.addColorStop(1, 'rgba(0,0,0,0)'); ctx.globalAlpha = 1; ctx.fillStyle = hg; ctx.fillRect(hq.x - 18, hq.y - 18, 36, 36); }
        for (let j = 0; j < 8; j++) {
          let tt = Math.max(0, pu.t - j * 0.014); if (pu.dir < 0) tt = 1 - tt; const q = bez(cb.P, tt);
          ctx.globalAlpha = (1 - j / 8) * 0.85; ctx.beginPath(); ctx.arc(q.x, q.y, 2.6 - j * 0.22, 0, TAU); ctx.fillStyle = pu.c; ctx.fill();
        }
      }
      ctx.globalAlpha = 1; ctx.globalCompositeOperation = 'source-over';
      for (const m of machines) {
        const near = mouse.on && reactive() && Math.hypot(m.x - mouse.x, m.y - mouse.y) < 190;
        m.glow += ((near ? 1 : 0) - m.glow) * Math.min(1, dt * 6); m.flash *= Math.pow(0.3, dt);
        m.nearS = (m.nearS || 0) + ((m.near || 0) - (m.nearS || 0)) * Math.min(1, dt * 10); m.near = 0;
        const g = Math.min(1, m.glow + m.flash + m.nearS * 0.6);
        m.sc += m.sp * dt * k * (1 + g * 3 + E * 0.5); m.blink += dt;
        const gold = (m.fc || '#D8198E') === '#EFB11E' || (m.glow > m.flash && m.glow > m.nearS);
        const rgb = gold ? '239,177,30' : '216,25,142';
        if (g > 0.02) {
          const R = 120 * m.s, cy = m.y - (m.t === 'rack' ? 0 : 10 * m.s);
          const hg = ctx.createRadialGradient(m.x, cy, 0, m.x, cy, R); hg.addColorStop(0, `rgba(${rgb},${0.32 * g})`); hg.addColorStop(0.5, `rgba(${rgb},${0.12 * g})`); hg.addColorStop(1, `rgba(${rgb},0)`);
          ctx.globalCompositeOperation = 'lighter'; ctx.fillStyle = hg; ctx.fillRect(m.x - R, cy - R, R * 2, R * 2); ctx.globalCompositeOperation = 'source-over';
        }
        ctx.save(); ctx.translate(m.x, m.y); ctx.scale(m.s, m.s);
        ctx.strokeStyle = g > 0.02 ? `rgba(${rgb},${0.2 + g * 0.8})` : 'rgba(255,255,255,0.16)'; ctx.lineWidth = (1.3 + g * 1.2) / m.s;
        ctx.shadowColor = `rgba(${rgb},${g})`; ctx.shadowBlur = g * 18;
        let sx = null, sy, sw, sh;
        if (m.t === 'mon') { rr(-56, -40, 112, 70, 5); ctx.stroke(); line(0, 30, 0, 44); line(-20, 44, 20, 44); sx = -50; sy = -34; sw = 100; sh = 58; }
        else if (m.t === 'lap') { rr(-46, -40, 92, 58, 4); ctx.stroke(); ctx.beginPath(); ctx.moveTo(-50, 18); ctx.lineTo(50, 18); ctx.lineTo(60, 26); ctx.lineTo(-60, 26); ctx.closePath(); ctx.stroke(); sx = -41; sy = -35; sw = 82; sh = 48; }
        else {
          rr(-28, -60, 56, 120, 4); ctx.stroke();
          for (let u = 0; u < 6; u++) {
            const yy = -52 + u * 18; rr(-22, yy, 44, 13, 2); ctx.stroke();
            const on = Math.sin(m.blink * (2 + u * 1.3) * k + u * 2) > 0.2 || g > 0.5;
            ctx.beginPath(); ctx.arc(15, yy + 6.5, 1.8, 0, TAU); ctx.fillStyle = on ? (u % 3 === 0 ? '#EFB11E' : '#D8198E') : 'rgba(255,255,255,0.12)'; ctx.fill();
            for (let v = 0; v < 3; v++) line(-16 + v * 5, yy + 4, -16 + v * 5, yy + 9);
          }
        }
        if (sx !== null) {
          ctx.shadowBlur = 0; ctx.fillStyle = `rgba(${rgb},${0.035 + g * 0.3})`; ctx.fillRect(sx, sy, sw, sh);
          ctx.save(); ctx.beginPath(); ctx.rect(sx, sy, sw, sh); ctx.clip();
          const LH = 6, total = m.lines.length * LH;
          m.lines.forEach((ln, i) => { const y = sy + 4 + (((i * LH - m.sc) % total) + total) % total; if (y > sy + sh) return; ctx.fillStyle = ln.c; ctx.globalAlpha = 0.5 + g * 0.5; ctx.fillRect(sx + 5 + ln.ind * 6, y, (sw - 14 - ln.ind * 6) * ln.len, 2); });
          ctx.globalAlpha = 1; ctx.restore();
        }
        ctx.restore();
      }
    };

    // ---- Hologram map ----
    const HLM = 'AAAAAAAAAAAAAAAAAAAAAADw/wcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADg//8/AOf//38AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+///v////////AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPj///////////8fAAAAwD8AAHwAAAAAAH8AAAAAAAAAAAAAAAAAAAAAAAAAAPD//w/+//////8DAADA/38AAAAAAAAAAP4GAAAAAAAAAAAAAAAAAAAAAAA4+M7///n///////8BAACA/wYAAAAAAAAAAIAfAAAAAAAAAAAAAAAAAAAAAPA4ADj4P/D///////8BAAAAPA4AAAAAAAAAAAA8AAAAAAAAAAAAAAAAAAAAAP7H4Pv9P+D///////8BAAAAAAAAAAAA/gEAAMD/HwAAAAAAAAAAAAAAAAAAAMD/w7v/DwAA//////8BAAAAAAAAAADwHwAA8P//PwAA4N8BAAAAAAAAAAAAgA88APD/DwAA/P////8AAAAAAAAAAAD4AAAA/P//DwAAwAACAAAAAAAAAAAAgP8Bhve5fwAA8P////8AAAAAAAAAAAA+AADw/////4cPAA8AAAAAAAAAAAAAwP//53/8/wEA4P///z8AAAAAAAAAAAAPAN78//////8fgP8BAAAAAwCAAAAAwO//D3/8/w8A8P///z8AAAAAAAAAAIAPgP////////8///8PAACAAAD8/wAAEMD/P/j4//8A4P///z8AAAAAAPgHAAAcgN//////////////DwAAAAD////z/5//f//Bx/8B4P///x8AAAAAwP8/AAAAn////////////////99/A+D///////+/H+7Ph/8BgP///wMAAAAA8P//B4P/////////////////////H4D/////////////h/kPwP//BwAAAAAA/P//H/f//9///////////////////8P/////////////A/w/wP//AzAYAAAA/v////////////////////////////P/////////////wP8dgP8/APA/AACA//d/8P//////////////////////4OD/////////////4/8HAP8PAPA/AADA//v/////////////////////////AAf+///////////jDvAPAP8HAIAHAADw//z///////////////////////9/AMD///////////8A0+cHAP4DAAAAAAD+P/7/////////////////////////AMD//////////38AwB8AAPwDAAAAAAD+P/7/////////////////////f/8HAOD///7//////z8AwH8AAMABAAAAAAD+f/7/////////////////////8P8AAAD8d4D//////z8AwH8cAAAAAAAAAAD+f/j///////////////////9/8AAAAAD0BwD8/////38AwP8/AAAAAAAAwAGcH/j//////////////////wMAeAAAAADADwDA//////8AgP8/AAAAAAAAwAPgH/b//////////////////wAAfwAAAABwAACA//////8PgP9/AAAAAAAAwANwD/7/////////////////fwAAfwAAAAAeAAAA////////wP//AQAAAAAAwAewA/7/////////////////HwCAPwAAAIABAAAA/v//////8///BwAAAAAAuA/w+f//////////////////HwCAPwAAAAAAAACA/P//////4///DwAAAAAAvA/+/////////////////////wcADwAAAAAAAAAA+f//////5///HwAAAAAAvD///////////////////////wcABwAAAAAAAAAA8P//////////HwAAAAAAiL///////////////////////w8AAwAAAAAAAAAA+P//////////DQAAAAAAwOf//////////////////////w0AAAAAAAAAAAAA4P////////8xPAAAAAAAAPz//////////////////////x0AAAAAAAAAAAAAgP////////8PfgAAAAAAgP///////////////////////wQAAAAAAAAAAAAAgP////////8H/gAAAAAAAP//////////////////////fwQAAAAAAAAAAAAAAP////////+/QAAAAAAAAPz//////f//////////////fwwAAAAAAAAAAAAAAP//////////AAAAAAAAAPj////j////////////////PwAAAAAAAAAAAAAAAP////////8dAAAAAAAAAPz/+f9h/v//////////////Hw4AAAAAAAAAAAAAgP///////z8EAAAAAAAA+P/P8/8B+P//////////////Bz8AAAAAAAAAAAAAgP///////z8AAAAAAAAA+H+wx/8A4P////////////9/AA8AAAAAAAAAAAAAAP///////z8AAAAAAAAA+H8gn//54f////////////8/AAMAAAAAAAAAAAAAgP///////wcAAAAAAAAA+B8wvN////////////////8fAAMAAAAAAAAAAAAAAP///////wMAAAAAAAAA/A8wGM///////////////+8PAAMAAAAAAAAAAAAAAP///////wEAAAAAAAAA/A8AGM7//////////////wMegAMAAAAAAAAAAAAAAP7//////wAAAAAAAAAA+AcgD47//////////////z8ewAEAAAAAAAAAAAAAAPz//////wAAAAAAAAAA4PN/AAT//////////////z888AEAAAAAAAAAAAAAAPj//////wAAAAAAAAAAwP1/ADAg/////////////w88/wEAAAAAAAAAAAAAAPj/////fwAAAAAAAAAA4P9/AAAw/////////////w+MfwAAAAAAAAAAAAAAAMD/////PwAAAAAAAAAA+P9/AACA/////////////x/ADwAAAAAAAAAAAAAAAID/////DwAAAAAAAAAA/P//Bw+A/////////////z/AAAAAAAAAAAAAAAAAAID/////BwAAAAAAAAAA/P//D//M/////////////z/AAAAAAAAAAAAAAAAAAAD7////BwAAAAAAAAAA/P///////////////////z8AAAAAAAAAAAAAAAAAAAD7//8HBwAAAAAAAAAA/P///////9///////////z8AAAAAAAAAAAAAAAAAAAD2/x8ADgAAAAAAAAAA/////////5///////////z8AAAAAAAAAAAAAAAAAAADu/wcADgAAAAAAAADA//////+//x///////////x8AAAAAAAAAAAAAAAAAAADc/wcAbAAAAAAAAADA//////8//z/k/////////w8AAAAAAAAAAAAAAAAAAACQ/wcADAAAAAAAAADg//////9//v/4/////////w8AAAAAAAAAAAAAAAAAAAAw/wcAYAAAAAAAAADw/////////v8cgP///////zcAAAAAAAAAAAAAAAAAAABg/gMAAAAAAAAAAADw/////////P9/AP///////zMAAAAAAAAAAAAAAAAAAAAA/AOAPwAAAAAAAAD4////////+f//AP7//////xAAAAAAAAAAAABAAAAAAAAA/Acc8AAAAAAAAAD4////////+f//AP7/H///HwAAAAAAAAAAAACAAAAAAAAA/AcewAMAAAAAAAD4////////+f9/AOD/B/9/BAAAAAAAAAAAAAAAAQAAAAAA+A8eAHgAAAAAAAD4////////4/8/AOD/A/4/BwAAAAAAAAAAAAAAAAAAAAAA8P8PQP4CAAAAAADw////////4/8fAOD/APw/AyAAAAAAAAAAAAAAAAAAAAAAwP8PAAAAAAAAAAD4////////x/8PAOB/APx/AHAAAAAAAAAAAAAAAAAAAAAAAP8PAAAAAAAAAAD4////////x/8DAOA/APz/AHAAAAAAAAAAAAAAAAAAAAAAAMj/AQAAAAAAAAD4////////z/8AAMAfAOD/ATAAAAAAAAAAAAAAAAAAAAAAAID/AQAAAAAAAAD8////////vz8AAMAPAMD/AXAAAAAAAAAAAAAAAAAAAAAAAAD8AQAAAAAAAAD4////////vw8AAMAPAMD/A/AAAAAAAAAAAAAAAAAAAAAAAADwARAAAAAAAAD4/////////wEAAIAPAMD+AyADAAAAAAAAAAAAAAAAAAAAAADgAX4AAAAAAADw/////////3AAAIAPAMD4AUADAAAAAAAAAAAAAAAAAAAAAADAAf9/AAAAAADg/////////38AAAAPAMD4AMgBAAAAAAAAAAAAAAAAAAAAAACAM/9/AAAAAADA/////////38AAAAXAMBgAMQCAAAAAAAAAAAAAAAAAAAAAAAA7///AQAAAACA/////////38AAAA6AMABAIIHAAAAAAAAAAAAAAAAAAAAAAAAzP//AwAAAACA/////////z8AAAAwAIABAMAHAAAAAAAAAAAAAAAAAAAAAAAAwP//BwAAAAAA/////////z8AAAAwAAADAAMHAAAAAAAAAAAAAAAAAAAAAAAAwP//fwAAAAAA/B/+/////x8AAAAAABgHgAcAAAAAAAAAAAAAAAAAAAAAAAAAwP///wEAAAAAOAD+/////w8AAAAAAHgPwAcAAAAAAAAAAAAAAAAAAAAAAAAAwP///wEAAAAAAADg/////wcAAAAAAPAO4AMAAAAAAAAAAAAAAAAAAAAAAAAA4P///wMAAAAAAADg/////wcAAAAAAOAP+AMAAAAAAAAAAAAAAAAAAAAAAAAA4P///wMAAAAAAADg/////wEAAAAAAMAP/gcZAAAAAAAAAAAAAAAAAAAAAAAA8P///wMAAAAAAADg/////wAAAAAAAIAP/vMZAAAAAAAAAAAAAAAAAAAAAAAA+P///x8AAAAAAADg////fwAAAAAAAAAP/luQAwAAAAAAAAAAAAAAAAAAAAAA+P///38AAAAAAADg////PwAAAAAAAAAf/nmAYwAAAAAAAAAAAAAAAAAAAAAA+P////8DAAAAAADg////HwAAAAAAAAA+/DkA9wMAAAAAAAAAAAAAAAAAAAAA+P////8/AAAAAADA////DwAAAAAAAAA88Hl0/w8QAAAAAAAAAAAAAAAAAAAA/P////9/AAAAAACA////DwAAAAAAAAA4AHgA+D8YAAAAAAAAAAAAAAAAAAAA+P//////AQAAAAAA////BwAAAAAAAAAwAFgAwH9PAAAAAAAAAAAAAAAAAAAA+P//////AQAAAAAA////BwAAAAAAAADgDQAAxP+GAAAAAAAAAAAAAAAAAAAA8P//////AQAAAAAA/v//DwAAAAAAAADAPwAAwP8ABAAAAAAAAAAAAAAAAAAA4P//////AQAAAAAA/v//DwAAAAAAAAAA+HYGQM8BGAAAAAAAAAAAAAAAAAAA4P//////AQAAAAAA/v//DwAAAAAAAAAAABgDAIQDOAAAAAAAAAAAAAAAAAAAwP//////AAAAAAAA/v//HwAAAAAAAAAAAAAAAAAGIAAAAAAAAAAAAAAAAAAAwP////9/AAAAAAAA/v//HwAAAAAAAAAAAAAAAwQAAAAAAAAAAAAAAAAAAAAAgP////8/AAAAAAAA/v//HyAAAAAAAAAAAADAHw4AAAAAAAAAAAAAAAAAAAAAgP////8/AAAAAAAA////HzAAAAAAAAAAAADADw4AAAAAAAAAAAAAAAAAAAAAAP////8fAAAAAAAA////H3gAAAAAAAAAAAD+Dx4AAAAAAAAAAAAAAAAAAAAAAP7///8fAAAAAAAA////H3wAAAAAAAAAAAD/Hz4AAAQAAAAAAAAAAAAAAAAAAPz///8fAAAAAACA////Dz8AAAAAAAAAAID/fz4AAAiAAAAAAAAAAAAAAAAAAPD///8fAAAAAACA////Az8AAAAAAAAAAMD//z8AAABgAAAAAAAAAAAAAAAAAOD///8fAAAAAAAA////AT8AAAAAAAAAAMD//38AAAAAAAAAAAAAAAAAAAAAAMD///8fAAAAAAAA////AB8AAAAAAAAAAPD///8AAAAAAAAAAAAAAAAAAAAAAMD///8PAAAAAAAA/v9/AB8AAAAAAAAAAP////8BAAEAAAAAAAAAAAAAAAAAAMD///8HAAAAAAAA/v//gB8AAAAAAAAAgP////8DAAYAAAAAAAAAAAAAAAAAAOD///8HAAAAAAAA/P//gA8AAAAAAAAAwP////8HAAQAAAAAAAAAAAAAAAAAAOD///8AAAAAAAAA/P//gA8AAAAAAAAA4P////8HAAAAAAAAAAAAAAAAAAAAAOD//x8AAAAAAAAA/P9/AA8AAAAAAAAA4P////8PAAAAAAAAAAAAAAAAAAAAAOD//w8AAAAAAAAA/P8fAAIAAAAAAAAAwP////8fAAAAAAAAAAAAAAAAAAAAAOD//w8AAAAAAAAA+P8fAAAAAAAAAAAA4P////8fAAAAAAAAAAAAAAAAAAAAAOD//w8AAAAAAAAA+P8fAAAAAAAAAAAAwP////8/AAAAAAAAAAAAAAAAAAAAAPD//wcAAAAAAAAA8P8fAAAAAAAAAAAAwP////8/AAAAAAAAAAAAAAAAAAAAAPD//wcAAAAAAAAA4P8PAAAAAAAAAAAAgP////8/AAAAAAAAAAAAAAAAAAAAAPD//wMAAAAAAAAA4P8HAAAAAAAAAAAAgP////8fAAAAAAAAAAAAAAAAAAAAAPD//wEAAAAAAAAAwP8DAAAAAAAAAAAAgP////8fAAAAAAAAAAAAAAAAAAAAAPD//wAAAAAAAAAAwP8BAAAAAAAAAAAAgP8D/v8fAAAAAAAAAAAAAAAAAAAAAPD/fwAAAAAAAAAAwH8AAAAAAAAAAAAAgP8A/P8PAAAAAAAAAAAAAAAAAAAAAPD/PwAAAAAAAAAAgAMAAAAAAAAAAAAAgA8A6P8HAAAAAAAAAAAAAAAAAAAAAPj/BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAwP8HAAAGAAAAAAAAAAAAAAAAAPj/BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgP8DAAAMAAAAAAAAAAAAAAAAAPz/BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8DAAA8AAAAAAAAAAAAAAAAAPz/AwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH4AAAB8AAAAAAAAAAAAAAAAAPw/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAcAAAAAAAAAAAAAAAAAPw/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAZAAAAAAAAAAAAAAAAAPwHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOABAAAPAAAAAAAAAAAAAAAAAPwfAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOAAAIADAAAAAAAAAAAAAAAAAPgHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEAAAOABAAAAAAAAAAAAAAAAAP4HAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPgAAAAAAAAAAAAAAAAAAP4DAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHwAAAAAAAAAAAAAAAAAAP8BAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADgAAAAAAAAAAAAAAAAAAP4HAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8DAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8BAAAAAAAAAAAAAAAAAAAAAAYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH6AAwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP4AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPgHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
    const hBits = (() => { const b = atob(HLM), u = new Uint8Array(b.length); for (let i = 0; i < b.length; i++) u[i] = b.charCodeAt(i); return u; })();
    const hLand = (lo, la) => { const r = Math.floor(84 - la); if (r < 0 || r >= 142) return 0; const c = ((Math.floor(lo + 180) % 360) + 360) % 360, i = r * 360 + c; return (hBits[i >> 3] >> (i & 7)) & 1; };
    const HG = '255,182,70', HV = '206,92,224', HW = '255,232,196';
    const hNet = { ny: [-74, 40.7], sp: [-46.6, -23.5], ld: [-0.1, 51.5], lg: [3.4, 6.5], cr: [31.2, 30], jb: [28, -26.2], nb: [36.8, -1.3], db: [55.3, 25.2], mb: [72.9, 19.1], sg: [103.8, 1.3], bj: [116.4, 39.9], tk: [139.7, 35.7], sy: [151.2, -33.9] };
    const hMore = [[-118.2,34.1],[-87.6,41.9],[-79.4,43.7],[-99.1,19.4],[-95.4,29.8],[-123.1,49.3],[-74.1,4.7],[-77,-12],[-58.4,-34.6],[-43.2,-22.9],[-70.7,-33.4],[2.35,48.9],[-3.7,40.4],[13.4,52.5],[12.5,41.9],[37.6,55.8],[29,41],[18.1,59.3],[-0.2,5.6],[7.5,9.1],[15.3,-4.3],[-17.4,14.7],[38.7,9],[-7.6,33.6],[46.7,24.7],[51.4,35.7],[67,24.9],[77.2,28.6],[77.6,13],[90.4,23.8],[100.5,13.8],[106.8,-6.2],[121,14.6],[121.5,31.2],[114.2,22.3],[127,37.6],[135.5,34.7],[115.9,-31.9],[145,-37.8],[174.8,-36.8],[18.4,-33.9],[13.2,-8.8],[30.1,-1.9],[39.3,-6.8]];
    const hEdges = 'ny-ld ny-sp ny-lg sp-lg sp-jb ld-lg ld-cr ld-db ld-bj lg-cr lg-nb lg-jb lg-db cr-db cr-nb nb-jb nb-mb db-mb db-sg mb-sg mb-bj sg-bj sg-sy bj-tk tk-sy jb-sy'.split(' ').map(s => s.split('-'));
    const hGear = (() => { let s = ''; for (let i = 0; i < 32; i++) { const a = i / 32 * TAU - TAU / 64, r = i % 4 < 2 ? 9.6 : 7.3; s += (i ? 'L' : 'M') + (12 + Math.cos(a) * r).toFixed(2) + ' ' + (12 + Math.sin(a) * r).toFixed(2); } return s + 'Z M15 12a3 3 0 1 0-6 0a3 3 0 1 0 6 0'; })();
    const hIcons = [
      'M19.5 13.5c1.4-1.4 2.5-3 2.5-5A5 5 0 0 0 17 3.5c-1.8 0-3.2.8-5 2.6C10.2 4.3 8.8 3.5 7 3.5a5 5 0 0 0-5 5c0 2 1.1 3.6 2.5 5L12 21z M3 12h5l1.5-2.5 2.5 5 2-6 1.5 3.5H21',
      'M12 4 2 9l10 5 10-5z M6 11v5c2.5 2.5 9.5 2.5 12 0v-5 M22 9v6',
      'M6.5 3.2a2.3 2.3 0 1 0 0.01 0z M17.5 3.2a2.3 2.3 0 1 0 0.01 0z M2.5 20v-5.5a4 4 0 0 1 8 0v1 M13.5 15.5v-1a4 4 0 0 1 8 0V20 M12 10.3a1.7 1.7 0 1 0 0.01 0z M9.5 20.5v-2a2.5 2.5 0 0 1 5 0v2',
      'M12 21v-8.5 M6.5 21h11 M12 13.5C8 13.5 5.5 11 5.5 7c4 0 6.5 2.5 6.5 6.5z M12 11.5c0-4.2 2.6-7 6.8-7 0 4.2-2.6 7-6.8 7z',
      'M4.5 5.5h15v10h-15z M2 19.5h20l-2-4h-16z M9.6 13h4.9a2 2 0 0 0 .2-4 2.8 2.8 0 0 0-5.3.7A1.7 1.7 0 0 0 9.6 13z',
      'M5 6a7 2.6 0 1 0 14 0a7 2.6 0 1 0-14 0 M5 6v3.5c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V6 M5 9.5V13c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V9.5 M5 13v3.5c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V13',
      'M3 20.5h18 M5 20.5v-5h3v5 M10.5 20.5v-8h3v8 M16 20.5v-11h3v11 M4 11.5l5-4.5 3.5 2.5 7-6 M15.5 3.5h4v4',
      'M3 21h18 M4.5 18h15 M6 18v-7 M10 18v-7 M14 18v-7 M18 18v-7 M3.5 11h17L12 4z',
      'M12 3v17.5 M7.5 20.5h9 M4 7h16 M4 7l-2.5 7c0 1.6 5 1.6 5 0z M20 7l-2.5 7c0 1.6 5 1.6 5 0z',
      hGear,
      'M9 18h6 M10 21.5h4 M9 14.5V18 M15 14.5V18 M15 14.5c.2-1.1.8-2 1.6-2.8A6 6 0 1 0 7.4 11.7c.8.8 1.4 1.7 1.6 2.8',
      'M12 9.5a5.5 5.5 0 1 0 0.01 0z M6.5 15h11 M12 9.5c-1.7 1.5-2.5 3.4-2.5 5.5s.8 4 2.5 5.5c1.7-1.5 2.5-3.4 2.5-5.5s-.8-4-2.5-5.5z M12 1.6a1.5 1.5 0 1 0 .01 0z M9.6 7.6a2.4 2.4 0 0 1 4.8 0 M4.3 6.3a1.5 1.5 0 1 0 .01 0z M1.9 12.4a2.4 2.4 0 0 1 4.8 0 M19.7 6.3a1.5 1.5 0 1 0 .01 0z M17.3 12.4a2.4 2.4 0 0 1 4.8 0'
    ];
    const hSpr = (w, hh, fn) => { const c = document.createElement('canvas'); c.width = w; c.height = hh; fn(c.getContext('2d')); return c; };
    const hGlow = (rgb) => hSpr(64, 64, (g) => { const r = g.createRadialGradient(32, 32, 0, 32, 32, 32); r.addColorStop(0, 'rgba(' + rgb + ',1)'); r.addColorStop(0.22, 'rgba(' + rgb + ',0.42)'); r.addColorStop(1, 'rgba(' + rgb + ',0)'); g.fillStyle = r; g.fillRect(0, 0, 64, 64); });
    const hBok = (rgb) => hSpr(64, 64, (g) => { const r = g.createRadialGradient(32, 32, 0, 32, 32, 31); r.addColorStop(0, 'rgba(' + rgb + ',0.3)'); r.addColorStop(0.75, 'rgba(' + rgb + ',0.42)'); r.addColorStop(0.9, 'rgba(' + rgb + ',0.6)'); r.addColorStop(1, 'rgba(' + rgb + ',0)'); g.fillStyle = r; g.fillRect(0, 0, 64, 64); });
    const hStreak = (rgb) => hSpr(512, 128, (g) => { g.setTransform(1, 0, 0, 0.125, 0, 64); const r = g.createRadialGradient(0, 0, 0, 0, 0, 512); r.addColorStop(0, 'rgba(' + rgb + ',1)'); r.addColorStop(0.3, 'rgba(' + rgb + ',0.3)'); r.addColorStop(1, 'rgba(' + rgb + ',0)'); g.fillStyle = r; g.fillRect(0, -512, 512, 1024); });
    const HS = { g: hGlow(HG), v: hGlow(HV), w: hGlow(HW), c: hGlow('255,214,150'), bg: hBok(HG), bv: hBok(HV), so: hStreak('255,138,46'), sv: hStreak('176,72,232') };
    const hQ = (A, C, B, n) => { const pts = [], L = [0]; for (let i = 0; i <= n; i++) { const t = i / n, u = 1 - t; pts.push([u * u * A[0] + 2 * u * t * C[0] + t * t * B[0], u * u * A[1] + 2 * u * t * C[1] + t * t * B[1]]); if (i) L.push(L[i - 1] + Math.hypot(pts[i][0] - pts[i - 1][0], pts[i][1] - pts[i - 1][1])); } return { pts, L, len: L[n] }; };
    const hAt = (p, d) => { let lo = 0, hi = p.L.length - 1; while (hi - lo > 1) { const m = (lo + hi) >> 1; if (p.L[m] <= d) lo = m; else hi = m; } const f = (d - p.L[lo]) / ((p.L[hi] - p.L[lo]) || 1); return [p.pts[lo][0] + (p.pts[hi][0] - p.pts[lo][0]) * f, p.pts[lo][1] + (p.pts[hi][1] - p.pts[lo][1]) * f]; };
    const hStroke = (g, p) => { g.beginPath(); p.pts.forEach((q, i) => i ? g.lineTo(q[0], q[1]) : g.moveTo(q[0], q[1])); g.stroke(); };
    const hComet = (p, d, dir, tail, rgb, a, spr) => {
      const N = 14, ok = (x) => x >= 0 && x <= p.len; let pd = d;
      for (let j = 1; j <= N; j++) { const dd = d - dir * tail * j / N; if (ok(pd) && ok(dd)) { const q0 = hAt(p, pd), q1 = hAt(p, dd), f = 1 - (j - 0.5) / N; ctx.strokeStyle = 'rgba(' + rgb + ',' + (f * a).toFixed(3) + ')'; ctx.lineWidth = 0.5 + f * 1.7; ctx.beginPath(); ctx.moveTo(q0[0], q0[1]); ctx.lineTo(q1[0], q1[1]); ctx.stroke(); } pd = dd; }
      if (ok(d)) { const q = hAt(p, d); ctx.globalAlpha = a; ctx.drawImage(spr, q[0] - 11, q[1] - 11, 22, 22); ctx.drawImage(HS.w, q[0] - 3.5, q[1] - 3.5, 7, 7); ctx.globalAlpha = 1; }
    };
    const mkBadge = (i, R, rgb, dpr) => {
      const S = Math.ceil(R * 2 + 12), c = document.createElement('canvas'); c.width = c.height = Math.round(S * dpr); const g = c.getContext('2d'); g.setTransform(dpr, 0, 0, dpr, S / 2 * dpr, S / 2 * dpr);
      const body = g.createRadialGradient(0, -R * 0.35, R * 0.1, 0, 0, R); body.addColorStop(0, 'rgba(64,22,92,0.8)'); body.addColorStop(1, 'rgba(16,4,28,0.88)');
      g.beginPath(); g.arc(0, 0, R, 0, TAU); g.fillStyle = body; g.fill(); g.lineWidth = 1; g.strokeStyle = 'rgba(' + rgb + ',0.4)'; g.stroke();
      g.beginPath(); g.arc(0, 0, R * 0.8, 0, TAU); g.strokeStyle = 'rgba(255,255,255,0.07)'; g.stroke();
      const hl = g.createLinearGradient(0, -R, 0, -R * 0.1); hl.addColorStop(0, 'rgba(255,255,255,0.14)'); hl.addColorStop(1, 'rgba(255,255,255,0)'); g.beginPath(); g.ellipse(0, -R * 0.45, R * 0.66, R * 0.4, 0, 0, TAU); g.fillStyle = hl; g.fill();
      const s = R * 1.12 / 24; g.save(); g.scale(s, s); g.translate(-12, -12.2); const p = new Path2D(hIcons[i]);
      g.lineWidth = 1.55; g.lineCap = 'round'; g.lineJoin = 'round'; g.strokeStyle = 'rgba(255,208,128,0.95)'; g.shadowColor = 'rgba(255,160,60,0.85)'; g.shadowBlur = 5 * dpr; g.stroke(p); g.shadowBlur = 0; g.stroke(p); g.restore();
      return { c, S };
    };
    const HM = { key: '' };
    const hRip = (p, rgb, big) => { if (HM.rip && HM.rip.length < 24) HM.rip.push({ p, rgb, big, t: 0 }); };
    const hSpawn = (ei, dir, d0) => { const e = HM.edges[ei]; HM.pp.push({ e: ei, dir, d: d0 ?? (dir > 0 ? 0 : e.len), sp: HM.mw * (0.06 + Math.random() * 0.035), rgb: Math.random() < 0.58 ? HG : HV }); };
    const holoClick = (x, y) => { if (!HM.nodes) return; let best = null, bd = 1e9; for (const n in HM.nodes) { const q = HM.nodes[n], dd = Math.hypot(q[0] - x, q[1] - y); if (dd < bd) { bd = dd; best = n; } } hRip(HM.nodes[best], HG, 1); HM.edges.forEach((e, i) => { if (e.a === best) hSpawn(i, 1); else if (e.b === best) hSpawn(i, -1); }); };
    const buildHolo = (key) => {
      const dpr = Math.min(2, window.devicePixelRatio || 1), mk = (w, hh, sc) => { const c = document.createElement('canvas'); c.width = Math.max(1, Math.round(w * sc)); c.height = Math.max(1, Math.round(hh * sc)); const g = c.getContext('2d'); g.setTransform(sc, 0, 0, sc, 0, 0); return [c, g]; };
      const mw = Math.min(W * 0.8, H * 0.62 * 360 / 142), mh = mw * 142 / 360, R = Math.max(16, Math.min(32, mw * 0.027)), by = mh / 2 + mw * 0.08, bx = mw * 0.55;
      const cx = W / 2, cy = Math.max(H * 0.47, 72 + R + by), mx0 = cx - mw / 2, my0 = cy - mh / 2, hz = Math.max(my0 + mh + 28, H * 0.775), rh = H - hz, sp = Math.max(3.2, Math.min(6, mw / 230));
      const P = (lo, la) => [mx0 + (lo + 180) / 360 * mw, my0 + (84 - la) / 142 * mh];
      const [st, g] = mk(W, H, dpr), [dl, d] = mk(W, H, dpr);
      let gr = g.createRadialGradient(cx, cy + mh * 0.1, 0, cx, cy + mh * 0.1, Math.max(W, H) * 0.75);
      gr.addColorStop(0, 'rgb(50,16,76)'); gr.addColorStop(0.45, 'rgb(24,7,40)'); gr.addColorStop(1, 'rgb(5,1,10)'); g.fillStyle = gr; g.fillRect(0, 0, W, H);
      g.globalCompositeOperation = 'lighter';
      [[W * 0.16, H * 0.5, W * 0.4, '255,140,50', 0.05], [W * 0.85, H * 0.42, W * 0.42, '190,60,220', 0.06], [cx, hz, mw * 0.6, '230,90,200', 0.06]].forEach(([x, y, r, c, a]) => { const q = g.createRadialGradient(x, y, 0, x, y, r); q.addColorStop(0, 'rgba(' + c + ',' + a + ')'); q.addColorStop(1, 'rgba(' + c + ',0)'); g.fillStyle = q; g.fillRect(x - r, y - r, r * 2, r * 2); });
      g.globalCompositeOperation = 'source-over';
      g.fillStyle = 'rgba(220,190,255,0.07)';
      for (let la = -30; la <= 60; la += 30) { const y = P(0, la)[1]; for (let x = mx0; x <= mx0 + mw; x += 5) g.fillRect(x, y, 1, 1); }
      for (let lo = -150; lo <= 150; lo += 30) { const x = P(lo, 0)[0]; for (let y = my0; y <= my0 + mh; y += 5) g.fillRect(x, y, 1, 1); }
      const orb = [[cx, cy + mh * 0.04, mw * 0.585, mh * 0.66, -0.05], [cx, cy + mh * 0.02, mw * 0.63, mh * 0.75, 0.04]];
      g.lineWidth = 1; g.strokeStyle = 'rgba(' + HG + ',0.08)'; g.beginPath(); g.ellipse(...orb[0], 0, TAU); g.stroke();
      g.setLineDash([1.5, 6]); g.strokeStyle = 'rgba(' + HV + ',0.16)'; g.beginPath(); g.ellipse(...orb[1], 0, TAU); g.stroke(); g.setLineDash([]);
      const dk = Math.min(1.4, sp / 4);
      for (let row = 0, y = my0 + sp * 0.5; y < my0 + mh; y += sp * 0.88, row++) {
        const la = 84 - (y - my0) / mh * 142;
        for (let x = mx0 + (row & 1 ? sp * 0.75 : sp * 0.25); x < mx0 + mw; x += sp) {
          const lo = (x - mx0) / mw * 360 - 180;
          if (!hLand(lo, la)) continue;
          const coast = !hLand(lo + 1.4, la) || !hLand(lo - 1.4, la) || !hLand(lo, la + 1.4) || !hLand(lo, la - 1.4);
          const m0 = Math.max(0, Math.min(1, 0.45 + 0.3 * Math.sin(lo * 0.021 + 0.4) + 0.22 * Math.sin(la * 0.055 - lo * 0.012 + 2.1) + (Math.random() - 0.5) * 0.4)), m = m0 < 0.5 ? 2 * m0 * m0 : 1 - 2 * (1 - m0) * (1 - m0);
          const a = (coast ? 0.42 : 0.2) + Math.random() * 0.22, s = (coast ? 1.45 : 1.15) * dk, j = Math.random() < 0.12 ? sp * 0.35 : 0;
          d.fillStyle = 'rgba(' + Math.round(255 - 45 * m) + ',' + Math.round(178 - 92 * m) + ',' + Math.round(62 + 160 * m) + ',' + a.toFixed(2) + ')';
          d.fillRect(x - s / 2 + (Math.random() - 0.5) * j, y - s / 2 + (Math.random() - 0.5) * j, s, s);
        }
      }
      for (let i = 0; i < 1400; i++) { const lo = Math.random() * 360 - 180, la = 84 - Math.random() * 142; if (!hLand(lo, la) && Math.random() > 0.08) continue; const q = P(lo, la); d.fillStyle = 'rgba(' + (Math.random() < 0.5 ? HG : HV) + ',' + (0.12 + Math.random() * 0.3).toFixed(2) + ')'; d.fillRect(q[0], q[1], 0.8, 0.8); }
      const cities = [], nodes = {};
      Object.keys(hNet).forEach(n => { nodes[n] = P(...hNet[n]); cities.push({ k: n, p: nodes[n], big: 1 }); });
      hMore.forEach(c => cities.push({ p: P(c[0], c[1]), big: 0 }));
      d.globalCompositeOperation = 'lighter';
      const cs = Math.min(sp, mw / 230);
      cities.forEach(c => { const n = c.big ? 12 : 6, rad = cs * (c.big ? 1.7 : 1.15);
        for (let j = 0; j < n; j++) { const an = Math.random() * TAU, r2 = Math.sqrt(Math.random()) * rad; d.fillStyle = 'rgba(' + HW + ',' + (0.5 + Math.random() * 0.45).toFixed(2) + ')'; d.fillRect(c.p[0] + Math.cos(an) * r2 - 0.7, c.p[1] + Math.sin(an) * r2 - 0.7, 1.4, 1.4); }
        c.ph = Math.random() * TAU; c.f = 0.6 + Math.random() * 1.4; });
      d.globalCompositeOperation = 'source-over';
      const [q1, qg1] = mk(W / 3, H / 3, 1), [q2, qg2] = mk(W / 9, H / 9, 1);
      qg1.drawImage(dl, 0, 0, q1.width, q1.height); qg2.drawImage(q1, 0, 0, q2.width, q2.height);
      g.globalCompositeOperation = 'lighter'; g.globalAlpha = 0.5; g.drawImage(q2, 0, 0, W, H); g.globalAlpha = 0.32; g.drawImage(q1, 0, 0, W, H); g.globalAlpha = 1; g.globalCompositeOperation = 'source-over'; g.drawImage(dl, 0, 0, W, H); g.globalCompositeOperation = 'lighter';
      cities.forEach(c => { const r3 = cs * (c.big ? 3 : 2); g.globalAlpha = c.big ? 0.3 : 0.14; g.drawImage(HS.c, c.p[0] - r3, c.p[1] - r3, r3 * 2, r3 * 2); }); g.globalAlpha = 1;
      const edges = hEdges.map(([a, b], i) => { const A = nodes[a], B = nodes[b], dx = B[0] - A[0], dy = B[1] - A[1], dd = Math.hypot(dx, dy); let nx = -dy / dd, ny = dx / dd; if (ny > 0 || (Math.abs(ny) < 0.15 && nx < 0)) { nx = -nx; ny = -ny; } const C = [(A[0] + B[0]) / 2 + nx * dd * 0.24, (A[1] + B[1]) / 2 + ny * dd * 0.24]; return Object.assign(hQ(A, C, B, 36), { a, b, rgb: i % 3 ? HG : HV }); });
      g.lineWidth = 1; edges.forEach(e => { g.strokeStyle = 'rgba(' + e.rgb + ',0.11)'; hStroke(g, e); });
      const se = (th) => { const c = Math.cos(th), s = Math.sin(th); return [cx + bx * Math.sign(c) * Math.pow(Math.abs(c), 0.62), cy - by * Math.sign(s) * Math.pow(Math.abs(s), 0.62)]; };
      const curve = [], cl = [0]; for (let i = 0; i <= 600; i++) { curve.push(se((200 - 220 * i / 600) * Math.PI / 180)); if (i) cl.push(cl[i - 1] + Math.hypot(curve[i][0] - curve[i - 1][0], curve[i][1] - curve[i - 1][1])); }
      const badges = hIcons.map((ic, i) => { const tg = cl[600] * i / 11; let j = 0; while (j < 600 && cl[j] < tg) j++; return { x: curve[j][0], y: curve[j][1], r: R, rgb: i % 2 ? HV : HG, i, send: 0, hov: 0 }; });
      const pairs = []; badges.forEach((b, bi) => cities.forEach((c, ci) => { if (c.k !== 'lg') pairs.push([Math.hypot(c.p[0] - b.x, c.p[1] - b.y), bi, ci]); }));
      pairs.sort((u, v) => u[0] - v[0]); const used = new Set();
      pairs.forEach(([dd, bi, ci]) => { const b = badges[bi]; if (b.city || used.has(ci)) return; b.city = cities[ci]; used.add(ci); });
      badges.forEach(b => { const C = b.city.p, ux = C[0] - b.x, uy = C[1] - b.y, dd = Math.hypot(ux, uy) || 1, S = [b.x + ux / dd * (b.r + 4), b.y + uy / dd * (b.r + 4)], M = [(S[0] + C[0]) / 2 - uy * 0.08, (S[1] + C[1]) / 2 + ux * 0.08];
        b.path = hQ(S, M, C, 24); g.strokeStyle = 'rgba(' + b.rgb + ',0.22)'; g.lineWidth = 1; hStroke(g, b.path);
        g.beginPath(); g.arc(C[0], C[1], 2.6, 0, TAU); g.strokeStyle = 'rgba(' + b.rgb + ',0.55)'; g.stroke(); g.fillStyle = 'rgba(' + HW + ',0.9)'; g.fillRect(C[0] - 0.8, C[1] - 0.8, 1.6, 1.6);
        b.spr = mkBadge(b.i, b.r, b.rgb, dpr); });
      g.globalCompositeOperation = 'source-over';
      const fl = g.createLinearGradient(0, hz, 0, H); fl.addColorStop(0, 'rgba(8,2,14,0.15)'); fl.addColorStop(1, 'rgba(4,1,8,0.75)'); g.fillStyle = fl; g.fillRect(0, hz, W, rh);
      const rs = dpr / 2, [rc, rg] = mk(W, rh, rs); rg.setTransform(rs, 0, 0, -0.36 * rs, 0, 0.36 * hz * rs); rg.drawImage(dl, 0, 0, W, H);
      rg.setTransform(rs, 0, 0, rs, 0, 0); rg.globalCompositeOperation = 'destination-in'; const mg = rg.createLinearGradient(0, 0, 0, rh); mg.addColorStop(0, 'rgba(0,0,0,0.55)'); mg.addColorStop(1, 'rgba(0,0,0,0)'); rg.fillStyle = mg; rg.fillRect(0, 0, W, rh);
      g.globalCompositeOperation = 'lighter'; g.drawImage(rc, 0, hz, W, rh);
      const fcx = cx, fcy = hz + rh * 0.46, rings = [0, 1, 2, 3, 4].map(i => ({ rx: mw * (0.15 + 0.12 * i), ry: mw * (0.15 + 0.12 * i) * 0.12, rgb: i % 2 ? HV : HG, w: (i % 2 ? -1 : 1) * (0.12 + i * 0.02) }));
      g.save(); g.beginPath(); g.rect(0, hz + 1, W, rh); g.clip();
      g.save(); g.translate(fcx, fcy); g.scale(1, 0.12); const pr = rings[2].rx, pg = g.createRadialGradient(0, 0, 0, 0, 0, pr); pg.addColorStop(0, 'rgba(255,170,90,0.16)'); pg.addColorStop(1, 'rgba(216,25,142,0)'); g.fillStyle = pg; g.fillRect(-pr, -pr, pr * 2, pr * 2); g.restore();
      rings.forEach((r, i) => { g.strokeStyle = 'rgba(' + r.rgb + ',' + (0.2 - i * 0.03).toFixed(2) + ')'; g.lineWidth = 1.1; g.beginPath(); g.ellipse(fcx, fcy, r.rx, r.ry, 0, 0, TAU); g.stroke(); });
      g.restore();
      const hl = g.createLinearGradient(cx - W * 0.42, 0, cx + W * 0.42, 0); hl.addColorStop(0, 'rgba(255,190,110,0)'); hl.addColorStop(0.5, 'rgba(255,210,150,0.55)'); hl.addColorStop(1, 'rgba(255,190,110,0)'); g.fillStyle = hl; g.fillRect(cx - W * 0.42, hz - 0.75, W * 0.84, 1.5);
      g.save(); g.translate(cx, hz); g.scale(1, 0.09); const hr = g.createRadialGradient(0, 0, 0, 0, 0, mw * 0.55); hr.addColorStop(0, 'rgba(255,180,110,0.35)'); hr.addColorStop(0.4, 'rgba(226,80,190,0.14)'); hr.addColorStop(1, 'rgba(216,25,142,0)'); g.fillStyle = hr; g.fillRect(-mw * 0.55, -mw * 0.55, mw * 1.1, mw * 1.1); g.restore();
      const fr = g.createRadialGradient(cx, hz, 0, cx, hz, mw * 0.06); fr.addColorStop(0, 'rgba(255,236,210,0.16)'); fr.addColorStop(1, 'rgba(255,236,210,0)'); g.fillStyle = fr; g.fillRect(cx - mw * 0.06, hz - mw * 0.06, mw * 0.12, mw * 0.12);
      g.globalCompositeOperation = 'source-over';
      const vg = g.createRadialGradient(cx, H * 0.48, Math.min(W, H) * 0.35, cx, H * 0.48, Math.hypot(W, H) * 0.62); vg.addColorStop(0, 'rgba(0,0,0,0)'); vg.addColorStop(1, 'rgba(0,0,0,0.6)'); g.fillStyle = vg; g.fillRect(0, 0, W, H);
      const bok = Array.from({ length: 24 }, () => ({ x: Math.random() * W, y: Math.random() * H, r: 2 + Math.random() * 7, vy: 5 + Math.random() * 10, ph: Math.random() * TAU, v: Math.random() < 0.5, a: 0.05 + Math.random() * 0.13 }));
      Object.assign(HM, { key, st, dl, mw, mh, cx, cy, mx0, my0, hz, rh, sp, cs, cities, nodes, edges, badges, rings, fcx, fcy, bok, orb, pp: [], bp: [], rip: [], pNext: 0.3, bNext: 1, lastB: -1 });
      for (let i = 0; i < 9; i++) { const ei = Math.floor(Math.random() * edges.length); hSpawn(ei, 1, Math.random() * edges[ei].len * 0.7); }
    };
    const drawHolo = (dt, k) => {
      const dpr = Math.min(2, window.devicePixelRatio || 1), key = W + 'x' + H + '@' + dpr;
      if (HM.key !== key) buildHolo(key);
      const t = T, hm = HM, sk = 0.8 + 0.2 * k;
      ctx.drawImage(hm.st, 0, 0, W, H);
      ctx.lineCap = 'round'; ctx.globalCompositeOperation = 'lighter';
      [[0, H * 0.3, 0.1, W * 0.5, H * 0.11, HS.so, 0.11, 0], [0, H * 0.64, -0.13, W * 0.36, H * 0.06, HS.so, 0.08, 1.7], [W, H * 0.26, -0.09, W * 0.52, H * 0.12, HS.sv, 0.12, 0.9], [W, H * 0.6, 0.14, W * 0.38, H * 0.07, HS.sv, 0.08, 2.6]].forEach(([x, y, an, len, th, spr, a, ph]) => {
        ctx.save(); ctx.translate(x + (x ? 1 : -1) * 30 * (1 - Math.sin(t * 0.12 + ph)), y); ctx.rotate(an + 0.03 * Math.sin(t * 0.09 + ph)); if (x) ctx.scale(-1, 1);
        ctx.globalAlpha = a * (0.75 + 0.25 * Math.sin(t * 0.17 + ph * 2)); ctx.drawImage(spr, 0, -th / 2, len, th); ctx.restore(); });
      ctx.globalAlpha = 1;
      const bw = hm.mw * 0.09, scx = hm.mx0 - bw * 2 + ((t * 0.035) % 1) * (hm.mw + bw * 4);
      [[bw * 2, 0.08], [bw, 0.1], [bw * 0.45, 0.12]].forEach(([w2, a]) => { ctx.save(); ctx.beginPath(); ctx.rect(scx - w2 / 2, hm.my0, w2, hm.mh); ctx.clip(); ctx.globalAlpha = a; ctx.drawImage(hm.dl, 0, 0, W, H); ctx.restore(); });
      hm.orb.forEach((o, i) => { const a = t * (i ? -0.07 : 0.09) + i * 2, co = Math.cos(o[4]), so = Math.sin(o[4]); for (let j = 0; j < 6; j++) { const aa = a - (i ? -1 : 1) * j * 0.012, ca = Math.cos(aa), sa = Math.sin(aa), x = o[0] + o[2] * ca * co - o[3] * sa * so, y = o[1] + o[2] * ca * so + o[3] * sa * co, r = (6 - j) * 1.6; ctx.globalAlpha = 0.5 * (1 - j / 6); ctx.drawImage(i ? HS.v : HS.g, x - r, y - r, r * 2, r * 2); } });
      ctx.globalAlpha = 1;
      ctx.save(); ctx.beginPath(); ctx.rect(0, hm.hz + 1, W, hm.rh); ctx.clip();
      hm.rings.forEach((r, i) => { const a0 = t * r.w + i * 1.3; ctx.strokeStyle = 'rgba(' + r.rgb + ',' + (0.42 - i * 0.05).toFixed(2) + ')'; ctx.lineWidth = 1.5; ctx.beginPath(); ctx.ellipse(hm.fcx, hm.fcy, r.rx, r.ry, 0, a0, a0 + 0.9); ctx.stroke(); ctx.beginPath(); ctx.ellipse(hm.fcx, hm.fcy, r.rx, r.ry, 0, a0 + Math.PI, a0 + Math.PI + 0.45); ctx.stroke(); });
      ctx.setLineDash([2, 8]); ctx.lineDashOffset = -t * 8; ctx.strokeStyle = 'rgba(' + HG + ',0.18)'; ctx.lineWidth = 1; ctx.beginPath(); ctx.ellipse(hm.fcx, hm.fcy, hm.rings[1].rx * 1.12, hm.rings[1].ry * 1.12, 0, 0, TAU); ctx.stroke(); ctx.setLineDash([]);
      ctx.translate(((t * 0.05) % 1.4 - 0.2) * W, hm.hz + hm.rh * 0.3); ctx.scale(1, 0.18); const sg = ctx.createRadialGradient(0, 0, 0, 0, 0, W * 0.16); sg.addColorStop(0, 'rgba(255,220,180,0.07)'); sg.addColorStop(1, 'rgba(255,220,180,0)'); ctx.fillStyle = sg; ctx.fillRect(-W * 0.16, -W * 0.16, W * 0.32, W * 0.32);
      ctx.restore();
      hm.cities.forEach(c => { const v = 0.5 + 0.5 * Math.sin(t * c.f + c.ph), r = hm.cs * (c.big ? 2.4 : 1.6); ctx.globalAlpha = (c.big ? 0.12 : 0.05) + (c.big ? 0.3 : 0.2) * v * v * v; ctx.drawImage(HS.c, c.p[0] - r, c.p[1] - r, r * 2, r * 2); });
      ctx.globalAlpha = 1;
      hm.pNext -= dt * sk * (1 + E * 0.25);
      if (hm.pNext <= 0) { if (hm.pp.length < 13) hSpawn(Math.floor(Math.random() * hm.edges.length), Math.random() < 0.5 ? 1 : -1); hm.pNext = 0.3 + Math.random() * 0.4; }
      for (let i = hm.pp.length - 1; i >= 0; i--) {
        const p = hm.pp[i], e = hm.edges[p.e], tail = Math.min(hm.mw * 0.09, e.len * 0.5);
        p.d += p.dir * p.sp * dt * sk;
        const prog = p.dir > 0 ? p.d / e.len : 1 - p.d / e.len;
        if (prog >= 1 && !p.hit) { p.hit = 1; hRip(hm.nodes[p.dir > 0 ? e.b : e.a], p.rgb, 1); }
        if (prog > 1 + tail / e.len) { hm.pp.splice(i, 1); continue; }
        ctx.strokeStyle = 'rgba(' + p.rgb + ',' + (0.16 * Math.sin(Math.PI * Math.min(1, Math.max(0, prog)))).toFixed(3) + ')'; ctx.lineWidth = 1.3; hStroke(ctx, e);
        hComet(e, p.d, p.dir, tail, p.rgb, 0.8, p.rgb === HG ? HS.g : HS.v);
      }
      hm.bNext -= dt * sk;
      if (hm.bNext <= 0) { if (hm.bp.length < 8) { let bi; do bi = Math.floor(Math.random() * hm.badges.length); while (bi === hm.lastB); hm.lastB = bi; hm.badges[bi].send = 1; hm.bp.push({ b: bi, d: 0 }); } hm.bNext = 0.4 + Math.random() * 0.5; }
      for (let i = hm.bp.length - 1; i >= 0; i--) { const q = hm.bp[i], b = hm.badges[q.b], pa = b.path, tail = Math.min(hm.mw * 0.06, pa.len * 0.6);
        q.d += hm.mw * 0.1 * dt * sk;
        if (q.d >= pa.len && !q.hit) { q.hit = 1; hRip(b.city.p, b.rgb, 0); }
        if (q.d > pa.len + tail) { hm.bp.splice(i, 1); continue; }
        ctx.strokeStyle = 'rgba(' + b.rgb + ',' + (0.22 * Math.sin(Math.PI * Math.min(1, q.d / pa.len))).toFixed(3) + ')'; ctx.lineWidth = 1.2; hStroke(ctx, pa);
        hComet(pa, q.d, 1, tail, b.rgb, 0.85, b.rgb === HG ? HS.g : HS.v); }
      for (let i = hm.rip.length - 1; i >= 0; i--) { const r = hm.rip[i]; r.t += dt; if (r.t > 1.6) { hm.rip.splice(i, 1); continue; } const f = r.t / 1.6;
        ctx.strokeStyle = 'rgba(' + r.rgb + ',' + ((1 - f) * 0.55).toFixed(3) + ')'; ctx.lineWidth = 1.2; ctx.beginPath(); ctx.arc(r.p[0], r.p[1], 3 + f * (r.big ? 24 : 16), 0, TAU); ctx.stroke();
        if (r.t < 0.9) { const fa = 1 - r.t / 0.9, r3 = (r.big ? 16 : 11) * (0.6 + 0.4 * fa); ctx.globalAlpha = fa * 0.8; ctx.drawImage(HS.w, r.p[0] - r3, r.p[1] - r3, r3 * 2, r3 * 2); ctx.globalAlpha = 1; } }
      hm.bok.forEach(b => { b.y -= b.vy * dt * sk; if (b.y < -12) { b.y = H + 12; b.x = Math.random() * W; } ctx.globalAlpha = b.a * (0.7 + 0.3 * Math.sin(t * 0.5 + b.ph)); ctx.drawImage(b.v ? HS.bv : HS.bg, b.x + Math.sin(t * 0.3 + b.ph) * 10 - b.r, b.y - b.r, b.r * 2, b.r * 2); });
      ctx.globalAlpha = 1;
      const hov = mouse.on && reactive();
      hm.badges.forEach((b, i) => {
        b.send = Math.max(0, b.send - dt * 0.7);
        const near = hov && Math.hypot(mouse.x - b.x, mouse.y - b.y) < b.r * 1.8; b.hov += ((near ? 1 : 0) - b.hov) * Math.min(1, dt * 6);
        const lift = Math.max(b.send, b.hov), br = 0.5 + 0.5 * Math.sin(t * 0.8 + i * 0.9), gr = b.r * (2 + lift * 0.3), sw = b.spr.S;
        ctx.globalCompositeOperation = 'lighter'; ctx.globalAlpha = 0.16 + br * 0.1 + lift * 0.35; ctx.drawImage(b.rgb === HG ? HS.g : HS.v, b.x - gr, b.y - gr, gr * 2, gr * 2); ctx.globalAlpha = 1;
        ctx.globalCompositeOperation = 'source-over'; ctx.drawImage(b.spr.c, b.x - sw / 2, b.y - sw / 2, sw, sw);
        ctx.globalCompositeOperation = 'lighter';
        if (lift > 0.02) { ctx.globalAlpha = lift * 0.45; ctx.drawImage(b.spr.c, b.x - sw / 2, b.y - sw / 2, sw, sw); ctx.globalAlpha = 1; }
        const dir = i % 2 ? 1 : -1, a0 = t * 0.35 * dir + i * 1.1;
        ctx.strokeStyle = 'rgba(' + b.rgb + ',' + (0.7 + 0.3 * lift).toFixed(3) + ')'; ctx.lineWidth = 1.6; ctx.beginPath(); ctx.arc(b.x, b.y, b.r, a0, a0 + 1.9); ctx.stroke();
        ctx.strokeStyle = 'rgba(' + b.rgb + ',0.4)'; ctx.lineWidth = 1.1; ctx.beginPath(); ctx.arc(b.x, b.y, b.r, a0 + 2.7, a0 + 3.5); ctx.stroke();
        ctx.setLineDash([1.5, 5]); ctx.lineDashOffset = -t * 5 * dir; ctx.strokeStyle = 'rgba(' + b.rgb + ',' + (0.18 + br * 0.1).toFixed(3) + ')'; ctx.lineWidth = 1; ctx.beginPath(); ctx.arc(b.x, b.y, b.r + 5, 0, TAU); ctx.stroke(); ctx.setLineDash([]);
      });
      if (hov) { const cg = ctx.createRadialGradient(mouse.x, mouse.y, 0, mouse.x, mouse.y, 180); cg.addColorStop(0, 'rgba(255,190,110,0.06)'); cg.addColorStop(1, 'rgba(255,190,110,0)'); ctx.fillStyle = cg; ctx.fillRect(mouse.x - 180, mouse.y - 180, 360, 360); }
      ctx.globalCompositeOperation = 'source-over'; ctx.globalAlpha = 1;
    };

    // ---- World map image overlay ----
    const IW = 1672, IH = 941;
    const crs = (P, n) => { const o = []; for (let i = 0; i < P.length - 1; i++) { const p0 = P[i - 1] || P[i], p1 = P[i], p2 = P[i + 1], p3 = P[i + 2] || P[i + 1];
      for (let j = 0; j < n; j++) { const t = j / n, t2 = t * t, t3 = t2 * t, f = (k) => 0.5 * (2 * p1[k] + (p2[k] - p0[k]) * t + (2 * p0[k] - 5 * p1[k] + 4 * p2[k] - p3[k]) * t2 + (3 * p1[k] - p0[k] - 3 * p2[k] + p3[k]) * t3); o.push([f(0), f(1)]); } }
      o.push(P[P.length - 1]); return o; };
    const mkPath = (s, extra) => { const L = [0]; for (let i = 1; i < s.length; i++) L.push(L[i - 1] + Math.hypot(s[i][0] - s[i - 1][0], s[i][1] - s[i - 1][1])); return Object.assign({ s, L, len: L[L.length - 1] }, extra || {}); };
    const ellPts = (cx, cy, rx, ry, a0, a1, n) => Array.from({ length: n + 1 }, (_, i) => { const t = a0 + (a1 - a0) * i / n; return [cx + Math.cos(t) * rx, cy + Math.sin(t) * ry]; });
    const DEV = [{ id: 'laptop', x: 395, y: 125 }, { id: 'monitor', x: 745, y: 517 }, { id: 'server', x: 1128, y: 588 }];
    const ROUTES = [
      [[252,147],[325,192],[368,236],[390,272]], [[215,290],[300,284],[390,272]], [[226,495],[330,470],[430,465],[505,493]], [[413,543],[452,500],[505,493]],
      [[552,162],[552,228],1], [[745,165],[745,230],1], [[988,182],[988,282],1], [[1285,147],[1285,232],1], [[395,168],[392,272],1],
      [[950,178],[900,207],[845,255],[820,292]], [[1022,152],[1110,130],[1180,128],[1243,130]], [[1490,256],[1460,310],[1422,352]],
      [[390,272],[560,285],[700,272],[820,292]], [[390,272],[520,318],[680,368],[870,450]], [[820,292],[872,450]],
      [[820,292],[1000,266],[1110,272],[1210,313]], [[870,450],[1000,335],[1210,313]], [[1210,313],[1350,330],[1422,352],[1500,420],[1535,500]],
      [[1210,313],[1262,370],[1292,440],[1302,590]], [[1505,553],[1400,575],[1302,590]],
      [[505,493],[620,482],[703,510]], [[785,500],[830,470],[870,450]], [[1170,590],[1240,592],[1302,590]], [[1095,565],[990,510],[870,450]]
    ].map(r => { const drop = r[r.length - 1] === 1, pts = drop ? r.slice(0, -1) : r; return mkPath(crs(pts, 14), { drop }); });
    const ORBITS = [
      mkPath(crs([[30,262],[90,190],[170,135],[300,88],[500,58],[836,48],[1100,68],[1300,105],[1480,170],[1600,250],[1650,320]], 10), { open: 1 }),
      mkPath(ellPts(836, 778, 595, 52, 0, TAU, 160), { loop: 1 }),
      mkPath(ellPts(836, 800, 730, 82, 0, TAU, 180), { loop: 1 })
    ];
    const wPulses = [], wFlash = [], devHot = {}; let wNext = 0.3;
    const orbitP = [{ o: 1, d: 0, sp: 70, c: '239,177,30' }, { o: 2, d: 2200, sp: -55, c: '226,80,190' }, { o: 0, d: 0, sp: 85, c: '239,177,30' }];
    const atD = (P, d) => { let lo = 0, hi = P.L.length - 1; while (hi - lo > 1) { const m = (lo + hi) >> 1; if (P.L[m] <= d) lo = m; else hi = m; } const seg = P.L[hi] - P.L[lo] || 1, f = (d - P.L[lo]) / seg; return [P.s[lo][0] + (P.s[hi][0] - P.s[lo][0]) * f, P.s[lo][1] + (P.s[hi][1] - P.s[lo][1]) * f]; };
    const comet = (P, d, dir, tail, m, c, alpha, loop) => {
      const N = 18; let prev = null;
      for (let j = 0; j <= N; j++) { let dd = d - dir * tail * (j / N); if (loop) dd = ((dd % P.len) + P.len) % P.len; else if (dd < 0 || dd > P.len) { prev = null; continue; }
        const q = atD(P, dd), X = m.ox + q[0] * m.s, Y = m.oy + q[1] * m.s;
        if (prev && Math.hypot(X - prev[0], Y - prev[1]) < 60) { const f = 1 - j / N; ctx.strokeStyle = 'rgba(' + c + ',' + f * alpha + ')'; ctx.lineWidth = 0.6 + f * 1.8; ctx.beginPath(); ctx.moveTo(prev[0], prev[1]); ctx.lineTo(X, Y); ctx.stroke(); }
        prev = [X, Y]; }
      let hd = loop ? ((d % P.len) + P.len) % P.len : d; if (hd < 0 || hd > P.len) return;
      const q = atD(P, hd), X = m.ox + q[0] * m.s, Y = m.oy + q[1] * m.s, r = 9;
      const g = ctx.createRadialGradient(X, Y, 0, X, Y, r); g.addColorStop(0, 'rgba(255,236,210,' + alpha + ')'); g.addColorStop(0.3, 'rgba(' + c + ',' + alpha * 0.7 + ')'); g.addColorStop(1, 'rgba(' + c + ',0)');
      ctx.fillStyle = g; ctx.fillRect(X - r, Y - r, r * 2, r * 2);
    };
    const drawDevice = (dv, m, t) => {
      const X = m.ox + dv.x * m.s, Y = m.oy + dv.y * m.s, R = 42 * m.s, z = 15 * m.s, hot = devHot[dv.id] || 0;
      ctx.save(); ctx.translate(X, Y); ctx.globalAlpha = 0.6 + hot * 0.3;
      const hg = ctx.createRadialGradient(0, 0, R * 0.8, 0, 0, R * 1.5); hg.addColorStop(0, 'rgba(216,25,142,' + (0.18 + hot * 0.2) + ')'); hg.addColorStop(1, 'rgba(216,25,142,0)');
      ctx.fillStyle = hg; ctx.fillRect(-R * 1.5, -R * 1.5, R * 3, R * 3);
      ctx.beginPath(); ctx.arc(0, 0, R, 0, TAU); ctx.fillStyle = 'rgba(30,8,36,0.82)'; ctx.fill();
      const rg = ctx.createLinearGradient(-R, -R, R, R); rg.addColorStop(0, 'rgba(239,177,30,0.95)'); rg.addColorStop(1, 'rgba(200,70,210,0.95)');
      ctx.shadowColor = 'rgba(239,140,60,0.8)'; ctx.shadowBlur = 10 + hot * 10; ctx.strokeStyle = rg; ctx.lineWidth = 2; ctx.stroke(); ctx.shadowBlur = 0;
      ctx.strokeStyle = 'rgba(255,214,160,0.95)'; ctx.fillStyle = 'rgba(255,214,160,0.95)'; ctx.lineWidth = Math.max(1.4, 2 * m.s); ctx.lineJoin = 'round';
      if (dv.id === 'monitor') {
        rr(-z * 1.15, -z * 0.9, z * 2.3, z * 1.5, 2); ctx.stroke(); line(0, z * 0.6, 0, z * 0.95); line(-z * 0.55, z * 0.95, z * 0.55, z * 0.95);
        for (let i = 0; i < 4; i++) { const v = 0.35 + 0.55 * (0.5 + 0.5 * Math.sin(t * 0.9 + i * 1.6)); ctx.fillRect(-z * 0.78 + i * z * 0.42, z * 0.38 - v * z * 1.0, z * 0.24, v * z * 1.0); }
      } else if (dv.id === 'server') {
        for (let i = 0; i < 3; i++) { const y = -z * 1.05 + i * z * 0.75; rr(-z * 0.95, y, z * 1.9, z * 0.58, 2); ctx.stroke(); line(-z * 0.65, y + z * 0.29, z * 0.1, y + z * 0.29);
          const on = Math.sin(t * 2 + i * 2.2) > 0; ctx.fillStyle = on ? 'rgba(255,200,90,1)' : 'rgba(255,214,160,0.35)'; ctx.beginPath(); ctx.arc(z * 0.58, y + z * 0.29, z * 0.11, 0, TAU); ctx.fill(); }
      } else {
        rr(-z * 0.95, -z * 0.85, z * 1.9, z * 1.25, 2); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(-z * 1.25, z * 0.62); ctx.lineTo(z * 1.25, z * 0.62); ctx.lineTo(z * 1.05, z * 0.4); ctx.lineTo(-z * 1.05, z * 0.4); ctx.closePath(); ctx.stroke();
        const cy = (t * 6) % 4; for (let i = 0; i < 3; i++) { ctx.globalAlpha = (0.6 + hot * 0.3) * (Math.abs(i - cy) < 1 ? 1 : 0.45); ctx.fillRect(-z * 0.65, -z * 0.6 + i * z * 0.3, z * (0.6 + ((i * 7) % 5) * 0.15), z * 0.12); }
      }
      ctx.restore();
    };
    const drawWorld = (dt, k) => {
      const s = Math.max(W / IW, H / IH), m = { s, ox: (W - IW * s) * 0.5, oy: (H - IH * s) * 0.38 }, t = T;
      for (const id in devHot) devHot[id] = Math.max(0, devHot[id] - dt * 1.2);
      ctx.lineCap = 'round';
      ctx.globalCompositeOperation = 'lighter';
      for (const op of orbitP) { const P = ORBITS[op.o]; op.d += op.sp * dt * (0.8 + 0.2 * k);
        if (P.open && op.d > P.len + 200) op.d = -200;
        comet(P, op.d, Math.sign(op.sp), 160, m, op.c, 0.45, !!P.loop); }
      wNext -= dt * (0.7 + 0.3 * k) * (1 + E * 0.4);
      if (wNext <= 0 && wPulses.length < 5) { const ri = Math.floor(Math.random() * ROUTES.length), R = ROUTES[ri], dir = R.drop ? 1 : (Math.random() < 0.5 ? 1 : -1);
        wPulses.push({ R, dir, d: dir > 0 ? 0 : R.len, sp: 110 + Math.random() * 60, c: Math.random() < 0.6 ? '239,177,30' : '226,80,190' }); wNext = 0.6 + Math.random() * 0.9; }
      for (let i = wPulses.length - 1; i >= 0; i--) { const p = wPulses[i]; p.d += p.dir * p.sp * dt * (0.8 + 0.2 * k);
        const done = p.dir > 0 ? p.d > p.R.len + 90 : p.d < -90;
        if (!p.hit && (p.dir > 0 ? p.d >= p.R.len : p.d <= 0)) { p.hit = 1; const e = p.dir > 0 ? p.R.s[p.R.s.length - 1] : p.R.s[0]; wFlash.push({ x: e[0], y: e[1], a: 0, c: p.c });
          DEV.forEach(dv => { if (Math.hypot(dv.x - e[0], dv.y - e[1]) < 60) devHot[dv.id] = 1; }); }
        if (done) { wPulses.splice(i, 1); continue; }
        comet(p.R, p.d, p.dir, 90, m, p.c, 0.7, false); }
      for (let i = wFlash.length - 1; i >= 0; i--) { const f = wFlash[i]; f.a += dt; if (f.a > 0.9) { wFlash.splice(i, 1); continue; }
        const X = m.ox + f.x * m.s, Y = m.oy + f.y * m.s, r = f.a / 0.9; ctx.strokeStyle = 'rgba(' + f.c + ',' + (1 - r) * 0.5 + ')'; ctx.lineWidth = 1.2; ctx.beginPath(); ctx.arc(X, Y, 3 + r * 16, 0, TAU); ctx.stroke(); }
      ctx.globalCompositeOperation = 'source-over';
      DEV.forEach(dv => drawDevice(dv, m, t));
    };

    // ---- Global command centre ----
    const D2R = Math.PI / 180;
    const LAND = [
      [[-168,66],[-162,70],[-140,70],[-120,72],[-95,72],[-80,73],[-65,62],[-60,55],[-66,45],[-70,42],[-76,35],[-81,31],[-80,25],[-84,30],[-90,29],[-97,26],[-97,21],[-90,21],[-87,16],[-83,10],[-79,9],[-82,8],[-86,12],[-92,15],[-105,20],[-110,24],[-112,30],[-117,32],[-124,40],[-124,48],[-130,55],[-140,60],[-152,58],[-165,60]],
      [[-55,60],[-45,60],[-20,70],[-20,80],[-40,83],[-60,82],[-72,78],[-55,70]],
      [[-79,9],[-72,12],[-62,11],[-50,0],[-35,-6],[-39,-15],[-48,-26],[-58,-38],[-65,-42],[-68,-52],[-72,-50],[-74,-40],[-71,-30],[-70,-18],[-76,-14],[-81,-5],[-80,0],[-77,7]],
      [[-10,36],[-9,43],[-2,44],[-5,48],[2,51],[8,54],[10,57],[5,61],[12,66],[20,70],[30,70],[40,67],[45,68],[60,70],[60,55],[50,45],[40,42],[30,41],[26,38],[22,37],[20,40],[16,38],[12,44],[8,44],[3,43],[-2,37]],
      [[-5,50],[1,51],[0,53],[-3,56],[-5,58],[-6,55],[-3,54],[-5,52]],
      [[-17,21],[-16,12],[-12,6],[-8,4],[0,5],[9,4],[10,-2],[13,-12],[12,-18],[18,-34],[22,-34],[32,-28],[36,-20],[40,-15],[40,-5],[44,-2],[51,11],[43,12],[37,18],[33,30],[22,32],[10,37],[0,36],[-6,36],[-10,30]],
      [[44,-25],[47,-25],[50,-15],[49,-12],[44,-16]],
      [[26,40],[36,36],[35,32],[43,13],[52,16],[58,22],[60,25],[67,24],[72,20],[77,8],[80,14],[87,22],[92,22],[97,16],[98,8],[103,1],[104,10],[109,12],[108,20],[118,24],[122,31],[120,38],[126,38],[128,35],[130,42],[140,48],[142,54],[135,55],[155,60],[163,62],[180,66],[180,72],[140,73],[110,77],[80,73],[60,70],[60,55],[50,45],[40,42]],
      [[130,31],[136,34],[141,38],[142,44],[140,42],[135,35]],
      [[109,1],[117,7],[119,1],[116,-4],[110,-3]], [[95,5],[106,-6],[102,-4]], [[131,-1],[141,-3],[150,-10],[141,-9],[132,-4]],
      [[114,-22],[122,-18],[130,-12],[137,-12],[142,-11],[146,-19],[153,-26],[150,-37],[141,-38],[135,-35],[129,-32],[115,-34]]
    ];
    const inPoly = (x, y, P) => { let c = false; for (let i = 0, j = P.length - 1; i < P.length; j = i++) { const [xi, yi] = P[i], [xj, yj] = P[j]; if ((yi > y) !== (yj > y) && x < (xj - xi) * (y - yi) / (yj - yi) + xi) c = !c; } return c; };
    const landPts = [];
    for (let la = -54; la <= 82; la += 2.6) { const st = 2.6 / Math.max(0.3, Math.cos(la * D2R)); for (let lo = -180; lo < 180; lo += st) if (LAND.some(P => inPoly(lo, la, P))) landPts.push([lo * D2R, la * D2R]); }
    const CITIES = [['Lagos',3.4,6.5],['Abuja',7.5,9.1],['Nairobi',36.8,-1.3],['Johannesburg',28,-26.2],['Cairo',31.2,30],['London',-0.1,51.5],['New York',-74,40.7],['São Paulo',-46.6,-23.5],['Dubai',55.3,25.2],['Mumbai',72.9,19.1],['Singapore',103.8,1.3],['Accra',-0.2,5.6],['Kigali',30.1,-1.9],['Toronto',-79.4,43.7]];
    const vec = (lo, la) => [Math.cos(la) * Math.cos(lo), Math.cos(la) * Math.sin(lo), Math.sin(la)];
    const proj = (lo, la, l0, p0) => { const cl = Math.cos(la), dl = lo - l0; return [cl * Math.sin(dl), Math.cos(p0) * Math.sin(la) - Math.sin(p0) * cl * Math.cos(dl), Math.sin(p0) * Math.sin(la) + Math.cos(p0) * cl * Math.cos(dl)]; };
    let panels = [], desks = [], arcs = [], glob = { x: 0, y: 0, R: 1 }, lamT = 0, arcNext = 0;
    const SCR = ['map', 'chart', 'line', 'radar', 'text'];
    const buildGlobal = () => {
      glob = { x: W * 0.5, y: H * 0.42, R: Math.min(W * 0.23, H * 0.33) };
      panels = [];
      for (const side of [-1, 1]) for (let c = 0; c < 2; c++) for (let r = 0; r < 3; r++) {
        if (c === 1 && W < 1300) continue;
        const w = 150 - c * 14, ph = 92 - c * 8, y = H * 0.07 + r * (ph + 26) + c * 16;
        if (y + ph > H * 0.7) continue;
        const x = side < 0 ? 26 + c * (w + 24) : W - 26 - w - c * (w + 24);
        panels.push({ x, y, w, h: ph, side, t: SCR[Math.floor(Math.random() * SCR.length)], g: 0, fl: 0, ph: Math.random() * 10, d: Array.from({ length: 14 }, () => Math.random()) });
      }
      desks = [];
      const n = Math.max(3, Math.round(W / 300));
      for (let i = 0; i < n; i++) { const cx = (i + 0.5) * W / n; if (Math.abs(cx - W / 2) < W * 0.09) continue; desks.push({ cx, w: Math.min(220, W / n - 40), g: 0, fl: 0, ph: Math.random() * 10, t: [SCR[Math.floor(Math.random() * 5)], SCR[Math.floor(Math.random() * 5)]], d: Array.from({ length: 14 }, () => Math.random()) }); }
      arcs = [];
    };
    const spawnArc = (from) => { const a = from ?? (Math.random() < 0.55 ? 0 : Math.floor(Math.random() * CITIES.length)); let b; do b = Math.floor(Math.random() * CITIES.length); while (b === a); arcs.push({ a: CITIES[a], b: CITIES[b], t: 0, c: Math.random() < 0.4 ? '239,177,30' : '216,25,142' }); if (arcs.length > 16) arcs.shift(); };
    const drawScreen = (ty, x, y, w, h, t, g, d, rgb) => {
      ctx.save(); ctx.beginPath(); ctx.rect(x, y, w, h); ctx.clip();
      ctx.fillStyle = 'rgba(' + rgb + ',' + (0.05 + g * 0.12) + ')'; ctx.fillRect(x, y, w, h);
      const a = 0.32 + g * 0.5;
      if (ty === 'map') {
        ctx.fillStyle = 'rgba(255,255,255,' + a * 0.7 + ')';
        for (let i = 0; i < landPts.length; i += 2) { const p = landPts[i]; ctx.fillRect(x + (p[0] / Math.PI + 1) / 2 * w, y + (0.5 - p[1] / Math.PI) * h, 1, 1); }
        for (let i = 0; i < 4; i++) { const c = CITIES[(i * 3 + Math.floor(t * 0.3)) % CITIES.length], cx = x + (c[1] / 180 + 1) / 2 * w, cy = y + (0.5 - c[2] / 180) * h, p = (t * 0.8 + i * 0.25) % 1; ctx.strokeStyle = 'rgba(239,177,30,' + (1 - p) * Math.min(1, a * 1.6) + ')'; ctx.lineWidth = 1; ctx.beginPath(); ctx.arc(cx, cy, 1 + p * 7, 0, TAU); ctx.stroke(); }
      } else if (ty === 'chart') {
        const n = d.length, bw = (w - 16) / n;
        for (let i = 0; i < n; i++) { const v = 0.2 + 0.7 * (0.5 + 0.5 * Math.sin(t * 0.7 + d[i] * 9)); ctx.fillStyle = i % 4 === 0 ? 'rgba(239,177,30,' + a + ')' : 'rgba(' + rgb + ',' + a + ')'; ctx.fillRect(x + 8 + i * bw, y + h - 8 - v * (h - 22), bw * 0.6, v * (h - 22)); }
      } else if (ty === 'line') {
        ctx.strokeStyle = 'rgba(255,255,255,' + a * 0.25 + ')'; ctx.lineWidth = 1; for (let i = 1; i < 4; i++) line(x, y + i * h / 4, x + w, y + i * h / 4);
        ctx.beginPath(); for (let i = 0; i <= 30; i++) { const xx = x + i / 30 * w, yy = y + h * (0.55 - 0.22 * Math.sin(i * 0.5 + t * 1.4 + d[0] * 6) - 0.12 * Math.sin(i * 1.3 - t * 0.9)); i ? ctx.lineTo(xx, yy) : ctx.moveTo(xx, yy); }
        ctx.strokeStyle = 'rgba(239,177,30,' + Math.min(1, a + 0.15) + ')'; ctx.lineWidth = 1.4; ctx.stroke();
      } else if (ty === 'radar') {
        const cx = x + w / 2, cy = y + h / 2, r0 = Math.min(w, h) * 0.4;
        ctx.strokeStyle = 'rgba(255,255,255,' + a * 0.35 + ')'; ctx.lineWidth = 1;
        for (let i = 1; i <= 3; i++) { ctx.beginPath(); ctx.arc(cx, cy, r0 * i / 3, 0, TAU); ctx.stroke(); }
        const sa = t * 1.2; ctx.beginPath(); ctx.moveTo(cx, cy); ctx.arc(cx, cy, r0, sa - 0.6, sa); ctx.closePath(); ctx.fillStyle = 'rgba(' + rgb + ',' + a * 0.5 + ')'; ctx.fill();
        for (let i = 0; i < 5; i++) { const an = d[i] * TAU, rd = r0 * (0.3 + d[i + 5] * 0.65), age = ((sa - an) % TAU + TAU) % TAU; ctx.fillStyle = 'rgba(239,177,30,' + Math.max(0, 1 - age / 3) * Math.min(1, a + 0.3) + ')'; ctx.fillRect(cx + Math.cos(an) * rd - 1.5, cy + Math.sin(an) * rd - 1.5, 3, 3); }
      } else {
        const LH = 7, rows = Math.ceil(h / LH) + 1, off = (t * 10) % LH, base = Math.floor(t * 10 / LH);
        for (let i = 0; i < rows; i++) { const k2 = (i + base) % d.length; ctx.fillStyle = k2 % 5 === 0 ? 'rgba(239,177,30,' + a + ')' : 'rgba(255,255,255,' + a * 0.6 + ')'; ctx.fillRect(x + 6 + (k2 % 3) * 5, y + 4 + i * LH - off, (w - 20) * (0.25 + d[k2] * 0.6), 2); }
      }
      ctx.restore();
    };
    const glowOf = (o, cx, cy, dt) => {
      const near = mouse.on && reactive() && Math.hypot(cx - mouse.x, cy - mouse.y) < 150;
      o.g += ((near ? 1 : 0) - o.g) * Math.min(1, dt * 6); o.fl *= Math.pow(0.3, dt);
      if (Math.random() < dt * 0.04) o.fl = 0.8;
      return Math.min(1, o.g + o.fl);
    };
    const drawGlobal = (dt, k) => {
      const t = T, { x: gx, y: gy, R } = glob, hy = H * 0.8;
      lamT += dt * 0.05 * k * (1 + E * 0.6);
      const l0 = (12 + Math.sin(lamT) * 75) * D2R, p0 = 0.3;
      ctx.lineCap = 'round'; ctx.lineJoin = 'round';
      // floor
      ctx.lineWidth = 1; ctx.strokeStyle = 'rgba(255,255,255,0.045)';
      for (let i = -12; i <= 12; i++) line(W / 2 + i * W * 0.025, hy, W / 2 + i * W * 0.17, H);
      for (let i = 0; i < 7; i++) { const f = i / 6, y = hy + (H - hy) * f * f; line(0, y, W, y); }
      const fg = ctx.createRadialGradient(gx, hy + (H - hy) * 0.45, 0, gx, hy + (H - hy) * 0.45, R * 1.4);
      fg.addColorStop(0, 'rgba(216,25,142,0.12)'); fg.addColorStop(1, 'rgba(216,25,142,0)');
      ctx.fillStyle = fg; ctx.fillRect(gx - R * 1.4, hy - R, R * 2.8, R * 2.4);
      // wall screens
      for (const p of panels) {
        const g = glowOf(p, p.x + p.w / 2, p.y + p.h / 2, dt), rgb = '216,25,142';
        ctx.save(); ctx.translate(p.x, p.y); ctx.transform(1, p.side * 0.09, 0, 1, 0, 0);
        ctx.strokeStyle = 'rgba(255,255,255,0.1)'; ctx.lineWidth = 1; line(p.w / 2, 0, p.w / 2, -14);
        ctx.shadowColor = 'rgba(' + rgb + ',' + g + ')'; ctx.shadowBlur = g * 18;
        rr(0, 0, p.w, p.h, 4); ctx.strokeStyle = 'rgba(255,255,255,' + (0.2 + g * 0.6) + ')'; ctx.lineWidth = 1.3; ctx.stroke(); ctx.shadowBlur = 0;
        drawScreen(p.t, 4, 4, p.w - 8, p.h - 8, t + p.ph, g, p.d, rgb);
        ctx.restore();
      }
      // globe rings (back half)
      const ring = (front) => { ctx.lineWidth = 1.2; ctx.strokeStyle = 'rgba(239,177,30,0.22)'; ctx.beginPath(); ctx.ellipse(gx, gy + R * 0.05, R * 1.28, R * 0.26, -0.08, front ? 0 : Math.PI, front ? Math.PI : TAU); ctx.stroke();
        ctx.setLineDash([4, 10]); ctx.lineDashOffset = -t * 20; ctx.strokeStyle = 'rgba(255,255,255,0.14)'; ctx.beginPath(); ctx.ellipse(gx, gy + R * 0.05, R * 1.44, R * 0.32, -0.08, front ? 0 : Math.PI, front ? Math.PI : TAU); ctx.stroke(); ctx.setLineDash([]); };
      ring(false);
      const halo = ctx.createRadialGradient(gx, gy, R * 0.7, gx, gy, R * 1.5); halo.addColorStop(0, 'rgba(216,25,142,0.12)'); halo.addColorStop(1, 'rgba(216,25,142,0)');
      ctx.fillStyle = halo; ctx.fillRect(gx - R * 1.5, gy - R * 1.5, R * 3, R * 3);
      const body = ctx.createRadialGradient(gx - R * 0.3, gy - R * 0.35, R * 0.1, gx, gy, R);
      body.addColorStop(0, 'rgba(255,255,255,0.06)'); body.addColorStop(1, 'rgba(216,25,142,0.08)');
      ctx.beginPath(); ctx.arc(gx, gy, R, 0, TAU); ctx.fillStyle = body; ctx.fill();
      ctx.strokeStyle = 'rgba(255,255,255,0.24)'; ctx.lineWidth = 1.2; ctx.stroke();
      ctx.lineWidth = 1; ctx.strokeStyle = 'rgba(255,255,255,0.08)';
      const path = (pts) => { ctx.beginPath(); let on = false; for (const [lo, la] of pts) { const q = proj(lo, la, l0, p0); if (q[2] > 0) { const X = gx + q[0] * R, Y = gy - q[1] * R; on ? ctx.lineTo(X, Y) : ctx.moveTo(X, Y); on = true; } else on = false; } ctx.stroke(); };
      for (let m = -180; m < 180; m += 30) path(Array.from({ length: 37 }, (_, i) => [m * D2R, (-90 + i * 5) * D2R]));
      for (let pl = -60; pl <= 60; pl += 30) path(Array.from({ length: 73 }, (_, i) => [(-180 + i * 5) * D2R, pl * D2R]));
      const ds = Math.max(1.6, R * 0.016);
      for (const [lo, la] of landPts) { const q = proj(lo, la, l0, p0), X = gx + q[0] * R, Y = gy - q[1] * R;
        if (q[2] > 0) { ctx.fillStyle = 'rgba(255,224,242,' + (0.18 + q[2] * 0.5) + ')'; ctx.fillRect(X - ds / 2, Y - ds / 2, ds, ds); }
        else { ctx.fillStyle = 'rgba(255,255,255,0.035)'; ctx.fillRect(X - 0.7, Y - 0.7, 1.4, 1.4); } }
      // arcs
      arcNext -= dt * k * (1 + E * 0.5);
      if (arcNext <= 0) { spawnArc(); arcNext = 0.8 + Math.random() * 1.3; }
      ctx.globalCompositeOperation = 'lighter';
      const arcPt = (A, B, om, s) => { const so = Math.sin(om) || 1, w1 = Math.sin((1 - s) * om) / so, w2 = Math.sin(s * om) / so, v = [A[0] * w1 + B[0] * w2, A[1] * w1 + B[1] * w2, A[2] * w1 + B[2] * w2];
        const q = proj(Math.atan2(v[1], v[0]), Math.asin(Math.max(-1, Math.min(1, v[2]))), l0, p0), hh = 1 + (0.06 + 0.3 * om / Math.PI) * Math.sin(Math.PI * s);
        return [gx + q[0] * R * hh, gy - q[1] * R * hh, q[2] > 0 || Math.hypot(q[0], q[1]) * hh > 1]; };
      for (let i = arcs.length - 1; i >= 0; i--) {
        const ar = arcs[i]; ar.t += dt * 0.45 * (0.7 + 0.3 * k) * (1 + E * 0.3);
        if (ar.t > 1.7) { arcs.splice(i, 1); continue; }
        const A = vec(ar.a[1] * D2R, ar.a[2] * D2R), B = vec(ar.b[1] * D2R, ar.b[2] * D2R), om = Math.acos(Math.max(-1, Math.min(1, A[0] * B[0] + A[1] * B[1] + A[2] * B[2])));
        const s1 = Math.min(1, ar.t), s0 = Math.max(0, ar.t - 0.55), fade = ar.t > 1 ? 1 - (ar.t - 1) / 0.7 : 1;
        ctx.beginPath(); let on = false;
        for (let j = 0; j <= 30; j++) { const [X, Y, vis] = arcPt(A, B, om, s0 + (s1 - s0) * j / 30); if (vis) { on ? ctx.lineTo(X, Y) : ctx.moveTo(X, Y); on = true; } else on = false; }
        ctx.strokeStyle = 'rgba(' + ar.c + ',' + 0.6 * fade + ')'; ctx.lineWidth = 1.5; ctx.stroke();
        const [hx, hy2, hv] = arcPt(A, B, om, s1);
        if (hv && ar.t < 1) { const hg = ctx.createRadialGradient(hx, hy2, 0, hx, hy2, 12); hg.addColorStop(0, 'rgba(' + ar.c + ',0.9)'); hg.addColorStop(1, 'rgba(' + ar.c + ',0)'); ctx.fillStyle = hg; ctx.fillRect(hx - 12, hy2 - 12, 24, 24); }
        if (hv && ar.t >= 1) { const p = (ar.t - 1) / 0.7; ctx.strokeStyle = 'rgba(' + ar.c + ',' + (1 - p) * 0.8 + ')'; ctx.lineWidth = 1.2; ctx.beginPath(); ctx.arc(hx, hy2, 2 + p * 16, 0, TAU); ctx.stroke(); }
      }
      ctx.globalCompositeOperation = 'source-over';
      CITIES.forEach((c, i) => { const q = proj(c[1] * D2R, c[2] * D2R, l0, p0); if (q[2] <= 0) return; const X = gx + q[0] * R, Y = gy - q[1] * R;
        ctx.beginPath(); ctx.arc(X, Y, i === 0 ? 3 : 2, 0, TAU); ctx.fillStyle = i === 0 ? '#EFB11E' : 'rgba(255,255,255,0.8)'; ctx.fill();
        if (i === 0) { const p = (t * 0.6) % 1; ctx.strokeStyle = 'rgba(239,177,30,' + (1 - p) * 0.8 + ')'; ctx.lineWidth = 1.2; ctx.beginPath(); ctx.arc(X, Y, 3 + p * 14, 0, TAU); ctx.stroke();
          ctx.font = '600 10px Poppins, sans-serif'; ctx.fillStyle = 'rgba(239,177,30,' + 0.85 * q[2] + ')'; ctx.fillText('LAGOS', X + 8, Y - 6); } });
      ring(true);
      // console desks
      for (const dk of desks) {
        const g = glowOf(dk, dk.cx, hy - 20, dt), w = dk.w, x0 = dk.cx - w / 2, mw = w * 0.46, mh = w * 0.27;
        ctx.beginPath(); ctx.moveTo(x0, hy + 6); ctx.lineTo(x0 + w, hy + 6); ctx.lineTo(x0 + w + 16, hy + 24); ctx.lineTo(x0 - 16, hy + 24); ctx.closePath();
        ctx.fillStyle = 'rgba(255,255,255,0.03)'; ctx.fill(); ctx.strokeStyle = 'rgba(255,255,255,' + (0.14 + g * 0.3) + ')'; ctx.lineWidth = 1.2; ctx.stroke();
        ctx.fillStyle = 'rgba(0,0,0,0.18)'; ctx.fillRect(x0 - 12, hy + 24, w + 24, 46); ctx.strokeStyle = 'rgba(255,255,255,0.07)'; ctx.strokeRect(x0 - 12, hy + 24, w + 24, 46);
        for (let i = 0; i < 2; i++) {
          const mx = x0 + w * 0.03 + i * (w * 0.5), my = hy - mh - 4;
          ctx.strokeStyle = 'rgba(255,255,255,0.16)'; line(mx + mw / 2, my + mh, mx + mw / 2, hy + 6);
          ctx.shadowColor = 'rgba(216,25,142,' + g + ')'; ctx.shadowBlur = g * 16;
          rr(mx, my, mw, mh, 3); ctx.strokeStyle = 'rgba(255,255,255,' + (0.2 + g * 0.6) + ')'; ctx.lineWidth = 1.2; ctx.stroke(); ctx.shadowBlur = 0;
          drawScreen(dk.t[i], mx + 3, my + 3, mw - 6, mh - 6, t + dk.ph + i * 3, g, dk.d, '216,25,142');
        }
      }
    };

    // ---- Flywheel ----
    let gears = [], Theta = 0; const PT = 20;
    const buildGears = () => {
      const mk = n => n * PT / TAU;
      gears = [{ n: 72, r: mk(72), x: W * 0.64, y: H * 0.6, parent: null, phi: 0, hov: 0, spokes: 6 }];
      let tries = 0;
      while (gears.length < 22 && tries < 600) {
        tries++;
        const p = gears[Math.floor(Math.random() * gears.length)];
        const n = [12, 16, 20, 26, 34, 44, 56][Math.floor(Math.random() * 7)], r = mk(n), phi = Math.random() * TAU;
        const x = p.x + Math.cos(phi) * (p.r + r), y = p.y + Math.sin(phi) * (p.r + r);
        if (x < -r * 0.6 || x > W + r * 0.6 || y < -r * 0.6 || y > H + r * 0.6) continue;
        if (gears.some(g => g !== p && Math.hypot(g.x - x, g.y - y) < g.r + r + 16)) continue;
        gears.push({ n, r, x, y, parent: p, phi, hov: 0, spokes: 3 + Math.floor(Math.random() * 4) });
      }
    };
    const drawGears = (dt, k) => {
      Theta += dt * 0.06 * k * (1 + E);
      ctx.lineJoin = 'round'; ctx.lineCap = 'round';
      for (const g of gears) {
        g.th = g.parent ? g.phi + Math.PI - (Math.PI - (g.phi - g.parent.th) * g.parent.n) / g.n : Theta;
        const over = mouse.on && reactive() && Math.hypot(g.x - mouse.x, g.y - mouse.y) < g.r + 6;
        g.hov += ((over ? 1 : 0) - g.hov) * Math.min(1, dt * 8);
        const ap = TAU / g.n, ro = g.r + 6, ri = g.r - 6, h = g.hov;
        ctx.beginPath();
        for (let i = 0; i < g.n; i++) {
          const a = g.th + i * ap;
          [[ri, a - ap * 0.5], [ri, a - ap * 0.27], [ro, a - ap * 0.16], [ro, a + ap * 0.16], [ri, a + ap * 0.27]].forEach(([rad, an], j) => { const X = g.x + Math.cos(an) * rad, Y = g.y + Math.sin(an) * rad; i === 0 && j === 0 ? ctx.moveTo(X, Y) : ctx.lineTo(X, Y); });
        }
        ctx.closePath();
        ctx.fillStyle = `rgba(255,255,255,${0.012 + h * 0.03})`; ctx.fill();
        ctx.strokeStyle = h > 0.02 ? `rgba(239,177,30,${0.2 + h * 0.6})` : !g.parent ? 'rgba(239,177,30,0.3)' : 'rgba(255,255,255,0.15)'; ctx.lineWidth = 1.3; ctx.stroke();
        ctx.beginPath(); ctx.arc(g.x, g.y, g.r * 0.7, 0, TAU); ctx.moveTo(g.x + g.r * 0.2, g.y); ctx.arc(g.x, g.y, g.r * 0.2, 0, TAU); ctx.stroke();
        ctx.beginPath(); ctx.arc(g.x, g.y, Math.max(3, g.r * 0.06), 0, TAU); ctx.stroke();
        for (let s = 0; s < g.spokes; s++) { const a = g.th + s * TAU / g.spokes, c = Math.cos(a), sn = Math.sin(a); line(g.x + c * g.r * 0.2, g.y + sn * g.r * 0.2, g.x + c * g.r * 0.7, g.y + sn * g.r * 0.7); }
      }
      if (E > 0.3) {
        ctx.globalCompositeOperation = 'lighter';
        for (const g of gears) if (g.parent) {
          const X = g.parent.x + Math.cos(g.phi) * g.parent.r, Y = g.parent.y + Math.sin(g.phi) * g.parent.r;
          const gr = ctx.createRadialGradient(X, Y, 0, X, Y, 16); gr.addColorStop(0, `rgba(239,177,30,${Math.min(0.85, E * 0.16)})`); gr.addColorStop(1, 'rgba(239,177,30,0)');
          ctx.fillStyle = gr; ctx.fillRect(X - 16, Y - 16, 32, 32);
        }
        ctx.globalCompositeOperation = 'source-over';
      }
    };
    const AU = {};
    const drawAurora = (dt, k, web) => {
      const key = W + 'x' + H;
      if (AU.key !== key) { AU.key = key; AU.sp = Array.from({ length: Math.round(W * H / 14000) }, () => ({ x: Math.random() * W, y: Math.random() * H, s: .5 + Math.random() * 1.4, ph: Math.random() * 6.28, v: 4 + Math.random() * 10, g: Math.random() < .35 })); }
      const bg = ctx.createLinearGradient(0, 0, 0, H);
      bg.addColorStop(0, '#13051a'); bg.addColorStop(.55, web ? '#200a38' : '#2a0b2f'); bg.addColorStop(1, '#100410');
      ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);
      ctx.globalCompositeOperation = 'lighter';
      const M = Math.max(W, H);
      const pool = (x, y, r, c) => { const g = ctx.createRadialGradient(x, y, 0, x, y, r); g.addColorStop(0, c); g.addColorStop(1, 'rgba(0,0,0,0)'); ctx.fillStyle = g; ctx.fillRect(x - r, y - r, r * 2, r * 2); };
      pool(W * (.24 + .05 * Math.sin(T * .13)), H * (.34 + .04 * Math.cos(T * .1)), M * .48, 'rgba(216,25,142,.2)');
      pool(W * (.8 + .04 * Math.cos(T * .11)), H * .72, M * .42, web ? 'rgba(124,58,237,.24)' : 'rgba(239,177,30,.13)');
      pool(W * .55, H * 1.05, M * .5, 'rgba(157,15,130,.18)');
      const R = web
        ? [{ y: .44, a: .1, f: 1.1, sp: .2, c: '157,92,255', n: 30, gap: 2.4, t: -.12 }, { y: .6, a: .08, f: 1.5, sp: -.16, c: '216,25,142', n: 26, gap: 2.2, t: .1 }, { y: .74, a: .05, f: 2, sp: .26, c: '239,177,30', n: 16, gap: 2, t: -.05 }]
        : [{ y: .4, a: .11, f: 1.2, sp: .18, c: '216,25,142', n: 32, gap: 2.4, t: -.14 }, { y: .58, a: .08, f: 1.6, sp: -.14, c: '239,177,30', n: 24, gap: 2.1, t: .08 }, { y: .72, a: .06, f: .9, sp: .22, c: '157,15,130', n: 26, gap: 2.6, t: -.06 }];
      const step = Math.max(10, W / 140);
      for (const r of R) {
        const lg = ctx.createLinearGradient(0, 0, W, 0);
        lg.addColorStop(0, `rgba(${r.c},0)`); lg.addColorStop(.2, `rgba(${r.c},1)`); lg.addColorStop(.8, `rgba(${r.c},1)`); lg.addColorStop(1, `rgba(${r.c},0)`);
        ctx.strokeStyle = lg; ctx.lineWidth = 1;
        for (let j = 0; j < r.n; j++) {
          ctx.globalAlpha = (.05 + .13 * Math.sin(Math.PI * j / (r.n - 1))) * Math.min(1.3, .75 + .3 * k);
          ctx.beginPath();
          for (let x = -step; x <= W + step; x += step) {
            const u = x / W;
            const y = H * (r.y + r.t * (u - .5)) + H * r.a * Math.sin(u * r.f * 6.283 + T * r.sp + j * .045) + H * r.a * .45 * Math.sin(u * r.f * 14.4 - T * r.sp * 1.4 + j * .02) + (j - r.n / 2) * r.gap * (1 + .7 * Math.sin(u * 3.1 + T * .2));
            x < 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
          }
          ctx.stroke();
        }
      }
      if (web) {
        const cx = W * .72, cy = H * .5, span = M * .7;
        ctx.lineWidth = 1.2;
        for (let i = 0; i < 6; i++) {
          const rad = (T * 38 * (.7 + .3 * k) + i * span / 6) % span, a = Math.pow(1 - rad / span, 2);
          ctx.globalAlpha = .32 * a; ctx.strokeStyle = i % 2 ? '#EFB11E' : '#c58bff';
          ctx.beginPath(); ctx.arc(cx, cy, rad, 0, 6.283); ctx.stroke();
        }
        ctx.globalAlpha = .9; pool(cx, cy, 70, 'rgba(239,177,30,.35)');
      }
      for (const p of AU.sp) {
        p.y -= p.v * dt * (.6 + .4 * k); if (p.y < -4) { p.y = H + 4; p.x = Math.random() * W; }
        ctx.globalAlpha = (.25 + .55 * (.5 + .5 * Math.sin(T * 1.6 + p.ph)));
        ctx.fillStyle = p.g ? '#ffd36b' : '#ffc2ea';
        ctx.beginPath(); ctx.arc(p.x, p.y, p.s, 0, 6.283); ctx.fill();
      }
      ctx.globalAlpha = 1; ctx.globalCompositeOperation = 'source-over';
    };
    const tickFx = (dt) => {
      E *= Math.pow(0.4, dt);
      ctx.globalCompositeOperation = 'lighter';
      for (let i = ripples.length - 1; i >= 0; i--) {
        const rp = ripples[i]; rp.age += dt; if (rp.age > 1) { ripples.splice(i, 1); continue; }
        ctx.globalAlpha = (1 - rp.age) * 0.6; ctx.beginPath(); ctx.arc(rp.x, rp.y, 8 + rp.age * 90, 0, TAU); ctx.strokeStyle = '#EFB11E'; ctx.lineWidth = 1.5; ctx.stroke();
      }
      ctx.globalAlpha = 1; ctx.globalCompositeOperation = 'source-over';
    };

    const build = () => {
      const k = intensity();
      const L = Math.max(10, Math.round(H / 46));
      lanes = Array.from({ length: L }, (_, i) => ({
        base: H * (0.06 + 0.88 * i / (L - 1)),
        amp: 10 + Math.random() * 28,
        f: (0.7 + Math.random() * 1.3) * TAU / Math.max(W, 1),
        ph: Math.random() * TAU,
        dr: 0.12 + Math.random() * 0.22
      }));
      gates = [0.2, 0.42, 0.64, 0.86].map(g => ({ x: g * W, flash: new Float32Array(L) }));
      parts = Array.from({ length: Math.round(L * 7 * k) }, () => spawn(true));
      builtFor = k;
    };
    const spawn = (anywhere) => ({
      lane: Math.floor(Math.random() * lanes.length),
      x: anywhere ? Math.random() * W : -Math.random() * W * 0.35,
      v: 38 + Math.random() * 80,
      len: 18 + Math.random() * 46,
      w: 0.8 + Math.random() * 1.6,
      c: pickCol(),
      a: 0.45 + Math.random() * 0.5
    });
    const laneY = (ln, x, t) => {
      let y = ln.base + ln.amp * Math.sin(x * ln.f + ln.ph + t * ln.dr) + ln.amp * 0.4 * Math.sin(x * ln.f * 2.3 + ln.ph * 1.7 - t * ln.dr * 1.3);
      if (mouse.on && reactive()) {
        const dx = x - mouse.x, dy = y - mouse.y, d2 = dx * dx + dy * dy;
        y += (dy >= 0 ? 1 : -1) * 54 * Math.exp(-d2 / (2 * 140 * 140));
      }
      return y;
    };
    const resize = () => {
      const dpr = Math.min(2, window.devicePixelRatio || 1);
      W = cv.clientWidth; H = cv.clientHeight;
      cv.width = W * dpr; cv.height = H * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      build(); buildPCs(); buildGears(); buildGlobal();
    };
    this._ro = new ResizeObserver(resize); this._ro.observe(cv); resize();
    this._mv = e => { const r = cv.getBoundingClientRect(), nx = e.clientX - r.left, ny = e.clientY - r.top; if (mouse.on && reactive()) E = Math.min(8, E + Math.min(60, Math.hypot(nx - mouse.x, ny - mouse.y)) * 0.0025); mouse.x = nx; mouse.y = ny; mouse.on = true; };
    this._dn = e => {
      if (!reactive()) return;
      const r = cv.getBoundingClientRect(), x = e.clientX - r.left, y = e.clientY - r.top;
      E = Math.min(8, E + 2.5); ripples.push({ x, y, age: 0 });
      if (mode() === 'Hologram') holoClick(x, y);
      if (mode() === 'Global') { spawnArc(0); spawnArc(0); spawnArc(0); }
      if (mode() === 'Computers') {
        let bi = -1, bd = 240; machines.forEach((m, i) => { const d = Math.hypot(m.x - x, m.y - y); if (d < bd) { bd = d; bi = i; } });
        if (bi >= 0) { machines[bi].flash = 1; cables.forEach(cb => { if (cb.a === bi) cb.pulses.push({ t: 0, dir: 1, c: '#EFB11E' }); else if (cb.b === bi) cb.pulses.push({ t: 0, dir: -1, c: '#EFB11E' }); }); }
      }
    };
    window.addEventListener('pointerdown', this._dn);
    this._lv = () => { mouse.on = false; };
    window.addEventListener('pointermove', this._mv);
    document.documentElement.addEventListener('pointerleave', this._lv);

    const bez = (p, t) => { const u = 1 - t; return { x: u*u*u*p[0] + 3*u*u*t*p[2] + 3*u*t*t*p[4] + t*t*t*p[6], y: u*u*u*p[1] + 3*u*u*t*p[3] + 3*u*t*t*p[5] + t*t*t*p[7] }; };

    let last = performance.now(), T = 0;
    const frame = (now) => {
      this._raf = requestAnimationFrame(frame);
      // The first RAF timestamp can precede performance.now() from this frame.
      // Clamp it so broadcast rings never receive a negative radius.
      const dt = Math.max(0, Math.min(0.05, (now - last) / 1000)); last = now;
      if (!W || !H) return;
      const k = intensity(); if (k !== builtFor) build();
      T += dt * (0.6 + 0.4 * k);
      ctx.clearRect(0, 0, W, H);
      const scr = this.state.screen, aur = scr === 'past' || scr === 'webinars';
      const md = aur ? 'Aurora' : mode();
      if (aur) drawAurora(dt, k, scr === 'webinars');
      else if (md === 'Hologram') drawHolo(dt, k);
      else if (md === 'World map') drawWorld(dt, k);
      else if (md === 'Global') drawGlobal(dt, k); else if (md === 'Computers') drawPCs(dt, k); else if (md === 'Flywheel') drawGears(dt, k);
      if (md === 'Lanes') {
      // lanes
      ctx.lineWidth = 1;
      for (const ln of lanes) {
        ctx.beginPath();
        for (let x = -20; x <= W + 20; x += 24) { const y = laneY(ln, x, T); x === -20 ? ctx.moveTo(x, y) : ctx.lineTo(x, y); }
        ctx.strokeStyle = 'rgba(255,255,255,0.055)'; ctx.stroke();
      }
      // gates
      for (const g of gates) {
        for (let i = 0; i < lanes.length; i++) {
          const f = g.flash[i]; g.flash[i] *= Math.pow(0.04, dt);
          const y = laneY(lanes[i], g.x, T);
          ctx.beginPath(); ctx.arc(g.x, y, 1.6 + f * 2.4, 0, TAU);
          ctx.fillStyle = f > 0.05 ? `rgba(239,177,30,${0.18 + f * 0.8})` : 'rgba(255,255,255,0.16)'; ctx.fill();
        }
      }
      // hover boost zones
      const stage0 = this.stageRef.current, cr0 = cv.getBoundingClientRect(), boosts = [];
      if (stage0) stage0.querySelectorAll('[data-boost]').forEach(el => { if (el.matches(':hover')) { const r = el.getBoundingClientRect(); boosts.push([r.left - cr0.left, r.top - cr0.top, r.right - cr0.left, r.bottom - cr0.top]); } });
      // particles
      ctx.globalCompositeOperation = 'lighter'; ctx.lineCap = 'round';
      for (let p of parts) {
        const ln = lanes[p.lane]; if (!ln) continue;
        const hy = laneY(ln, p.x, T);
        let boost = 1;
        if (mouse.on && reactive()) { const dx = p.x - mouse.x, dy = hy - mouse.y; boost += 2 * Math.exp(-(dx*dx + dy*dy) / (2 * 160 * 160)); }
        let inB = false; for (const bx of boosts) { if (p.x > bx[0] - 60 && p.x < bx[2] + 60 && hy > bx[1] && hy < bx[3]) { inB = true; break; } }
        if (inB) boost += 2.4;
        const col = inB ? '#EFB11E' : p.c;
        const acc = 1 + 2.4 * Math.pow(Math.max(0, p.x) / W, 1.6);
        const px = p.x; p.x += p.v * acc * boost * dt * (0.55 + 0.45 * k);
        for (const g of gates) if (px < g.x && p.x >= g.x) g.flash[p.lane] = 1;
        const tl = Math.min(240, p.len * acc * boost);
        const tx = p.x - tl, ty = laneY(ln, tx, T), y = laneY(ln, p.x, T);
        const gr = ctx.createLinearGradient(tx, ty, p.x, y);
        gr.addColorStop(0, 'rgba(0,0,0,0)'); gr.addColorStop(1, col);
        ctx.globalAlpha = Math.min(1, p.a * (0.7 + 0.3 * boost));
        ctx.beginPath(); ctx.moveTo(tx, ty);
        for (let s = 1; s <= 6; s++) { const xx = tx + tl * s / 6; ctx.lineTo(xx, laneY(ln, xx, T)); }
        ctx.strokeStyle = gr; ctx.lineWidth = p.w; ctx.stroke();
        ctx.beginPath(); ctx.arc(p.x, y, p.w * 1.1, 0, TAU); ctx.fillStyle = col; ctx.fill();
        if (p.x - tl > W + 10) Object.assign(p, spawn(false));
      }
      ctx.globalAlpha = 1;
      }
      tickFx(dt);
      // fade background behind content-heavy screens
      const dimT = ['agenda', 'session', 'profile', 'exhibitors', 'sponsors', 'emergence', 'notifications', 'attendees', 'speakers', 'communities'].includes(this.state.screen) ? 0.58 : ['lobby', 'networking', 'login', 'register', 'start'].includes(this.state.screen) ? 0.65 : 1;
      this._dim = (this._dim ?? 1) + (dimT - (this._dim ?? 1)) * Math.min(1, dt * 4);
      if (this._dim < 0.995) { ctx.globalCompositeOperation = 'destination-out'; ctx.fillStyle = `rgba(0,0,0,${1 - this._dim})`; ctx.fillRect(0, 0, W, H); ctx.globalCompositeOperation = 'source-over'; }

      // node graph
      const stage = this.stageRef.current;
      const els = stage && this.state.screen !== 'lobby' ? stage.querySelectorAll('[data-node]') : [];
      const cr = cv.getBoundingClientRect(), nodes = {};
      els.forEach(el => { const r = el.getBoundingClientRect(); nodes[el.dataset.node] = { x: r.left - cr.left + r.width / 2, y: r.top - cr.top + r.height / 2, r: r.width / 2, w: r.width, h: r.height, rect: el.dataset.shape === 'rect', el, hov: el.matches(':hover') }; });
      const seen = new Set();
      for (const id in nodes) {
        const a = nodes[id]; const tos = (a.el.dataset.to || '').split(/\s+/).filter(Boolean);
        for (const tid of tos) {
          const b = nodes[tid]; if (!b) continue;
          const key = id + '>' + tid; seen.add(key);
          let L = links.get(key); if (!L) { L = { pulses: [], next: Math.random() * 1.2 }; links.set(key, L); }
          const mx = (b.x - a.x) * 0.5;
          const P = [a.x, a.y, a.x + mx, a.y, b.x - mx, b.y, b.x, b.y];
          const hot = a.hov || b.hov;
          ctx.beginPath(); ctx.moveTo(P[0], P[1]); ctx.bezierCurveTo(P[2], P[3], P[4], P[5], P[6], P[7]);
          ctx.strokeStyle = hot ? 'rgba(239,177,30,0.75)' : 'rgba(255,255,255,0.22)'; ctx.lineWidth = hot ? 2 : 1.25; ctx.stroke();
          L.next -= dt * k * (hot ? 2.6 : 1);
          if (L.next <= 0) { L.pulses.push({ t: 0, c: Math.random() < 0.35 ? '#EFB11E' : '#D8198E' }); L.next = 1.1 + Math.random() * 1.4; }
          ctx.globalCompositeOperation = 'lighter';
          for (let i = L.pulses.length - 1; i >= 0; i--) {
            const pu = L.pulses[i]; pu.t += dt * (0.32 + 0.18 * k) * (hot ? 1.8 : 1);
            if (pu.t >= 1) { if (hot) rings.push({ x: b.x, y: b.y, r: b.r, w: b.w, h: b.h, rect: b.rect, age: 0, c: '#EFB11E' }); L.pulses.splice(i, 1); continue; }
            const e = Math.pow(pu.t, 1.7);
            for (let j = 0; j < 10; j++) {
              const tt = Math.max(0, e - j * 0.012); const q = bez(P, tt);
              ctx.globalAlpha = (1 - j / 10) * 0.9; ctx.beginPath(); ctx.arc(q.x, q.y, 3.2 - j * 0.22, 0, TAU);
              ctx.fillStyle = hot ? '#EFB11E' : pu.c; ctx.fill();
            }
          }
          ctx.globalAlpha = 1; ctx.globalCompositeOperation = 'source-over';
        }
      }
      for (const key of links.keys()) if (!seen.has(key)) links.delete(key);
      ctx.globalCompositeOperation = 'lighter';
      for (let i = rings.length - 1; i >= 0; i--) {
        const g = rings[i]; g.age += dt; if (g.age > 0.9) { rings.splice(i, 1); continue; }
        ctx.globalAlpha = (1 - g.age / 0.9) * 0.7; ctx.beginPath(); const gw = 4 + g.age * 30;
        if (g.rect && ctx.roundRect) ctx.roundRect(g.x - g.w / 2 - gw, g.y - g.h / 2 - gw, g.w + gw * 2, g.h + gw * 2, 26 + gw); else ctx.arc(g.x, g.y, g.r + gw, 0, TAU);
        ctx.strokeStyle = g.c; ctx.lineWidth = 1.5; ctx.stroke();
      }
      ctx.globalAlpha = 1; ctx.globalCompositeOperation = 'source-over';
    };
    this._raf = requestAnimationFrame(frame);
    }

    destroy() {
      cancelAnimationFrame(this._raf);
      this._ro?.disconnect();
      window.removeEventListener("pointermove", this._mv);
      window.removeEventListener("pointerdown", this._dn);
      document.documentElement.removeEventListener("pointerleave", this._lv);
      this.canvasRef.current.remove();
      delete this.stage.__accelerateBackground;
    }
  }

  function boot() {
    document.querySelectorAll(".rc-stage").forEach(stage => {
      if (!stage.__accelerateBackground) {
        stage.__accelerateBackground = new PortalBackground(stage);
      }
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot, { once: true });
  } else {
    boot();
  }
})();
