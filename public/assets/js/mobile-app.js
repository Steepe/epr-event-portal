/* EPR Mobile — shared behaviour: bottom sheets, toasts, segmented pills, notifications, local session times.
   The stage background is drawn by accelerate-background.js, shared with the desktop portal. */
(function () {
    'use strict';

    var EPR = window.EPR = window.EPR || {};
    var $ = function (sel, root) { return (root || document).querySelector(sel); };
    var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

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
