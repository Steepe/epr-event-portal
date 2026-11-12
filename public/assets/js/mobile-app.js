/* EPR Mobile — shared behaviour: stage background, bottom sheets, toasts, segmented pills, notifications. */
(function () {
    'use strict';

    var EPR = window.EPR = window.EPR || {};
    var $ = function (sel, root) { return (root || document).querySelector(sel); };
    var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

    /* ---------- Stage background (same network animation as the design) ---------- */
    function startBackground(cv) {
        if (!cv || !cv.getContext) return;
        var ctx = cv.getContext('2d');
        var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var k = reduced ? 0.3 : 1, TAU = Math.PI * 2;
        var G = '239,177,30', V = '189,92,224';
        var BASE_W = 390, BASE_H = 844, W = BASE_W, H = BASE_H;
        var baseNodes = [[60, 190], [300, 150], [210, 330], [70, 470], [330, 520], [180, 660], [300, 760], [60, 740]];
        var links = [[0, 1], [0, 2], [1, 2], [2, 3], [2, 4], [3, 5], [4, 5], [5, 6], [5, 7], [1, 4]];
        var nodes = [], edges = [], bok = [];

        function layout() {
            var rect = cv.getBoundingClientRect();
            var dpr = Math.min(2, window.devicePixelRatio || 1);
            W = Math.max(1, rect.width); H = Math.max(1, rect.height);
            cv.width = W * dpr; cv.height = H * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            nodes = baseNodes.map(function (n) { return [n[0] * W / BASE_W, n[1] * H / BASE_H]; });
            edges = links.map(function (l, i) {
                var A = nodes[l[0]], B = nodes[l[1]], dx = B[0] - A[0], dy = B[1] - A[1], d = Math.hypot(dx, dy) || 1;
                var C = [(A[0] + B[0]) / 2 - dy / d * d * 0.18, (A[1] + B[1]) / 2 + dx / d * d * 0.18];
                return { A: A, B: B, C: C, rgb: i % 3 ? G : V };
            });
            if (!bok.length) {
                for (var i = 0; i < 16; i++) {
                    bok.push({ x: Math.random() * W, y: Math.random() * H, r: 2 + Math.random() * 5, vy: 4 + Math.random() * 7, ph: Math.random() * TAU, c: Math.random() < 0.5 ? G : V, a: 0.06 + Math.random() * 0.12 });
                }
            }
        }
        function at(e, t) { var u = 1 - t; return [u * u * e.A[0] + 2 * u * t * e.C[0] + t * t * e.B[0], u * u * e.A[1] + 2 * u * t * e.C[1] + t * t * e.B[1]]; }
        function glow(x, y, r, rgb, a) {
            var g = ctx.createRadialGradient(x, y, 0, x, y, r);
            g.addColorStop(0, 'rgba(' + rgb + ',' + a + ')'); g.addColorStop(1, 'rgba(' + rgb + ',0)');
            ctx.fillStyle = g; ctx.fillRect(x - r, y - r, r * 2, r * 2);
        }

        var pulses = [], ripples = [], next = 0.5, T = 0, last = performance.now(), raf = 0;
        function frame(now) {
            var dt = Math.min(0.05, (now - last) / 1000) * k; last = now; T += dt;
            ctx.clearRect(0, 0, W, H);
            ctx.lineWidth = 1;
            edges.forEach(function (e) { ctx.strokeStyle = 'rgba(' + e.rgb + ',0.09)'; ctx.beginPath(); ctx.moveTo(e.A[0], e.A[1]); ctx.quadraticCurveTo(e.C[0], e.C[1], e.B[0], e.B[1]); ctx.stroke(); });
            ctx.globalCompositeOperation = 'lighter';
            nodes.forEach(function (n, i) { var v = 0.5 + 0.5 * Math.sin(T * 0.9 + i * 1.7); glow(n[0], n[1], 10 + v * 6, G, 0.08 + v * 0.12); ctx.fillStyle = 'rgba(255,232,196,' + (0.35 + v * 0.4) + ')'; ctx.fillRect(n[0] - 1, n[1] - 1, 2, 2); });
            next -= dt;
            if (next <= 0 && pulses.length < 3) { pulses.push({ e: edges[Math.floor(Math.random() * edges.length)], t: 0, dir: Math.random() < 0.5 ? 1 : -1, sp: 0.22 + Math.random() * 0.12 }); next = 1.4 + Math.random() * 1.6; }
            var i, j;
            for (i = pulses.length - 1; i >= 0; i--) {
                var p = pulses[i]; p.t += dt * p.sp;
                if (p.t >= 1) { var n = p.dir > 0 ? p.e.B : p.e.A; ripples.push({ x: n[0], y: n[1], t: 0, rgb: p.e.rgb }); pulses.splice(i, 1); continue; }
                for (j = 0; j < 10; j++) {
                    var tt = Math.max(0, p.t - j * 0.018); if (p.dir < 0) tt = 1 - tt;
                    var q = at(p.e, tt); ctx.globalAlpha = (1 - j / 10) * 0.7;
                    ctx.beginPath(); ctx.arc(q[0], q[1], 2.2 - j * 0.18, 0, TAU); ctx.fillStyle = 'rgb(' + p.e.rgb + ')'; ctx.fill();
                }
                ctx.globalAlpha = 1; var h = at(p.e, p.dir > 0 ? p.t : 1 - p.t); glow(h[0], h[1], 16, p.e.rgb, 0.4);
            }
            for (i = ripples.length - 1; i >= 0; i--) {
                var r = ripples[i]; r.t += dt; if (r.t > 1.4) { ripples.splice(i, 1); continue; }
                var f = r.t / 1.4; ctx.strokeStyle = 'rgba(' + r.rgb + ',' + ((1 - f) * 0.45) + ')'; ctx.beginPath(); ctx.arc(r.x, r.y, 3 + f * 20, 0, TAU); ctx.stroke();
            }
            bok.forEach(function (b) { b.y -= b.vy * dt; if (b.y < -10) { b.y = H + 10; b.x = Math.random() * W; } ctx.globalAlpha = b.a * (0.7 + 0.3 * Math.sin(T * 0.5 + b.ph)); glow(b.x + Math.sin(T * 0.3 + b.ph) * 8, b.y, b.r * 2, b.c, 0.5); });
            ctx.globalAlpha = 1; ctx.globalCompositeOperation = 'source-over';
            raf = requestAnimationFrame(frame);
        }

        layout();
        window.addEventListener('resize', layout);
        document.addEventListener('visibilitychange', function () {
            cancelAnimationFrame(raf);
            if (!document.hidden) { last = performance.now(); raf = requestAnimationFrame(frame); }
        });
        raf = requestAnimationFrame(frame);
    }

    /* ---------- Toast ---------- */
    var toastTimer = 0;
    EPR.toast = function (text, isError) {
        var el = $('#mToast');
        if (!el || !text) return;
        clearTimeout(toastTimer);
        el.textContent = text;
        el.classList.toggle('is-error', !!isError);
        el.hidden = false;
        toastTimer = setTimeout(function () { el.hidden = true; }, 2800);
    };

    /* ---------- Bottom sheets ---------- */
    var openSheetEl = null;
    EPR.closeSheet = function () {
        if (!openSheetEl) return;
        var closing = openSheetEl;
        openSheetEl = null;
        closing.hidden = true;
        var scrim = $('#mScrim'); if (scrim) scrim.hidden = true;
        document.body.classList.remove('is-locked');
        closing.dispatchEvent(new CustomEvent('sheet:close'));
    };
    EPR.openSheet = function (id) {
        var sheet = typeof id === 'string' ? $('.m-sheet[data-sheet="' + id + '"]') : id;
        if (!sheet) return null;
        if (openSheetEl && openSheetEl !== sheet) EPR.closeSheet();
        openSheetEl = sheet;
        sheet.hidden = false;
        sheet.scrollTop = 0;
        var scrim = $('#mScrim'); if (scrim) scrim.hidden = false;
        document.body.classList.add('is-locked');
        sheet.dispatchEvent(new CustomEvent('sheet:open'));
        return sheet;
    };

    /* ---------- Helpers ---------- */
    EPR.post = function (url, data, asJson) {
        var options = { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } };
        if (asJson) { options.headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(data || {}); }
        else { options.body = new URLSearchParams(data || {}); }
        return fetch(url, options).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (body) { return { ok: res.ok, body: body }; });
        });
    };
    EPR.playInto = function (holder, src) {
        if (!holder || !src || holder.querySelector('iframe')) return;
        var url = src + (src.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1';
        var frame = document.createElement('iframe');
        frame.src = url;
        frame.allow = 'accelerometer; gyroscope; autoplay; encrypted-media; fullscreen; picture-in-picture';
        frame.allowFullscreen = true;
        frame.title = holder.getAttribute('data-video-title') || 'Video';
        $$('[data-video-hide]', holder).forEach(function (el) { el.hidden = true; });
        holder.appendChild(frame);
    };
    function readStore(key, fallback) { try { return JSON.parse(localStorage.getItem(key)) || fallback; } catch (e) { return fallback; } }
    function writeStore(key, value) { try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* storage unavailable */ } }

    /* ---------- Notifications (read state is kept on this device) ---------- */
    function initNotifications() {
        var rows = $$('[data-notif]');
        var badge = $('#mBellBadge');
        var storeKey = 'epr-mobile-notif-read:' + (document.body.getAttribute('data-user') || '0');
        var readMap = readStore(storeKey, {});

        function isUnread(row) { return row.getAttribute('data-unread') === '1'; }
        function setUnread(id, unread) {
            rows.forEach(function (row) {
                if (row.getAttribute('data-notif') !== id) return;
                row.setAttribute('data-unread', unread ? '1' : '0');
                row.classList.toggle('is-unread', unread);
                var toggle = $('[data-notif-toggle]', row);
                if (toggle) toggle.textContent = unread ? 'Mark read' : 'Mark unread';
            });
        }
        function refresh() {
            var ids = {};
            rows.forEach(function (row) { if (isUnread(row)) ids[row.getAttribute('data-notif')] = 1; });
            var count = Object.keys(ids).length;
            if (badge) { badge.textContent = String(count); badge.hidden = count === 0; }
            $$('[data-notif-pill]').forEach(function (el) { el.textContent = count ? count + ' NEW' : 'ALL READ'; });
            $$('[data-notif-summary]').forEach(function (el) { el.textContent = count ? 'You have ' + count + ' unread notification' + (count === 1 ? '.' : 's.') : 'You’re all caught up.'; });
            applyFilter();
        }
        function persist(id, unread) {
            if (id.indexOf('conn-') === 0) return;
            readMap[id] = unread ? 0 : 1;
            writeStore(storeKey, readMap);
        }

        var activeFilter = 'All';
        function matches(row, filter) { return filter === 'All' || (filter === 'Unread' && isUnread(row)) || row.getAttribute('data-cat') === filter; }
        function applyFilter() {
            var page = $('[data-notif-page]');
            if (!page) return;
            var pageRows = $$('[data-notif]', page);
            $$('[data-notif-filter]', page).forEach(function (chip) {
                var filter = chip.getAttribute('data-notif-filter');
                chip.classList.toggle('is-active', filter === activeFilter);
                var count = $('.m-chip__count', chip);
                if (count) count.textContent = String(pageRows.filter(function (row) { return matches(row, filter); }).length);
            });
            var visible = 0;
            pageRows.forEach(function (row) { var show = matches(row, activeFilter); row.hidden = !show; if (show) visible++; });
            $$('[data-notif-group]', page).forEach(function (group) { group.hidden = !$$('[data-notif]', group).some(function (row) { return !row.hidden; }); });
            var empty = $('[data-notif-empty]', page); if (empty) empty.hidden = visible > 0;
        }

        rows.forEach(function (row) {
            var id = row.getAttribute('data-notif');
            if (readMap[id] === 1) setUnread(id, false);
            if (readMap[id] === 0 && id.indexOf('conn-') !== 0) setUnread(id, true);
        });

        document.addEventListener('click', function (event) {
            var target = event.target;
            var markAll = target.closest('[data-notif-markall]');
            if (markAll) {
                rows.forEach(function (row) { var id = row.getAttribute('data-notif'); setUnread(id, false); persist(id, false); });
                refresh();
                return;
            }
            var toggle = target.closest('[data-notif-toggle]');
            if (toggle) {
                var row = toggle.closest('[data-notif]'), id = row.getAttribute('data-notif'), unread = !isUnread(row);
                setUnread(id, unread); persist(id, unread); refresh();
                return;
            }
            var chip = target.closest('[data-notif-filter]');
            if (chip) { activeFilter = chip.getAttribute('data-notif-filter'); applyFilter(); return; }
            var action = target.closest('[data-conn-action]');
            if (action) {
                var connRow = action.closest('[data-notif]');
                var type = action.getAttribute('data-conn-action');
                var holder = action.parentNode;
                $$('button', holder).forEach(function (b) { b.disabled = true; });
                EPR.post(document.body.getAttribute('data-api') + '/connections/' + connRow.getAttribute('data-connection') + '/' + type, {}, true).then(function (res) {
                    if (!res.ok) throw new Error(res.body && res.body.message ? res.body.message : 'failed');
                    var text = $('p', connRow);
                    if (text) text.textContent = type === 'accept' ? 'Connection accepted. You can now view each other’s social links.' : 'Connection request declined.';
                    holder.remove();
                    setUnread(connRow.getAttribute('data-notif'), false);
                    refresh();
                }).catch(function () {
                    $$('button', holder).forEach(function (b) { b.disabled = false; });
                    EPR.toast('Unable to update this request. Please try again.', true);
                });
                return;
            }
            var pref = target.closest('[data-pref]');
            if (pref) {
                var prefs = readStore('epr-mobile-notif-prefs', {});
                var on = !pref.classList.contains('is-on');
                pref.classList.toggle('is-on', on);
                pref.setAttribute('aria-pressed', on ? 'true' : 'false');
                prefs[pref.getAttribute('data-pref')] = on ? 1 : 0;
                writeStore('epr-mobile-notif-prefs', prefs);
            }
        });

        var savedPrefs = readStore('epr-mobile-notif-prefs', {});
        $$('[data-pref]').forEach(function (pref) {
            var key = pref.getAttribute('data-pref');
            if (savedPrefs[key] === undefined) return;
            pref.classList.toggle('is-on', savedPrefs[key] === 1);
            pref.setAttribute('aria-pressed', savedPrefs[key] === 1 ? 'true' : 'false');
        });

        refresh();
    }

    /* ---------- Session times: stored in GMT, shown in the attendee's local time ---------- */
    function localizeTimes() {
        if (!window.Intl || !Intl.DateTimeFormat) return;
        var timeFmt = new Intl.DateTimeFormat(undefined, { hour: 'numeric', minute: '2-digit' });
        var zone = '';
        try {
            var parts = new Intl.DateTimeFormat(undefined, { timeZoneName: 'short' }).formatToParts(new Date());
            parts.forEach(function (part) { if (part.type === 'timeZoneName') zone = part.value; });
        } catch (e) { zone = ''; }
        function utc(date, time) {
            if (!date || !time) return null;
            var value = new Date(date + 'T' + (time.length === 5 ? time + ':00' : time) + 'Z');
            return isNaN(value.getTime()) ? null : value;
        }
        $$('[data-local-time]').forEach(function (item) {
            var start = utc(item.getAttribute('data-date'), item.getAttribute('data-start'));
            var end = utc(item.getAttribute('data-date'), item.getAttribute('data-end'));
            if (!start) return;
            var startEl = $('[data-lt-start]', item), endEl = $('[data-lt-end]', item), zoneEl = $('[data-lt-zone]', item);
            if (startEl) startEl.textContent = timeFmt.format(start);
            if (endEl && end) endEl.textContent = timeFmt.format(end);
            if (zoneEl && zone) zoneEl.textContent = zone;
        });
    }

    /* ---------- Boot ---------- */
    document.addEventListener('DOMContentLoaded', function () {
        startBackground($('#mStage'));

        document.addEventListener('click', function (event) {
            var opener = event.target.closest('[data-open-sheet]');
            if (opener) { event.preventDefault(); EPR.openSheet(opener.getAttribute('data-open-sheet')); return; }
            if (event.target.closest('[data-close-sheet]') || event.target.id === 'mScrim') { event.preventDefault(); EPR.closeSheet(); return; }

            var back = event.target.closest('[data-back]');
            if (back) {
                event.preventDefault();
                var sameSite = document.referrer && document.referrer.indexOf(location.origin) === 0;
                if (sameSite && history.length > 1) history.back(); else location.href = back.getAttribute('data-back');
                return;
            }

            var seg = event.target.closest('[data-seg-target]');
            if (seg) {
                var group = seg.closest('[data-seg]');
                $$('[data-seg-target]', group).forEach(function (button) {
                    var active = button === seg;
                    button.classList.toggle('is-active', active);
                    button.setAttribute('aria-selected', active ? 'true' : 'false');
                    var panel = document.getElementById(button.getAttribute('data-seg-target'));
                    if (panel) panel.hidden = !active;
                });
                document.dispatchEvent(new CustomEvent('seg:change', { detail: seg.getAttribute('data-seg-target') }));
                return;
            }

            var play = event.target.closest('[data-play]');
            if (play) { EPR.playInto(play.closest('[data-video-src]'), play.closest('[data-video-src]').getAttribute('data-video-src')); }
        });

        document.addEventListener('keydown', function (event) { if (event.key === 'Escape') EPR.closeSheet(); });

        initNotifications();
        localizeTimes();

        var flash = document.body.getAttribute('data-flash');
        if (flash) EPR.toast(flash, document.body.getAttribute('data-flash-type') === 'error');
    });
})();
