<?php
$event           = $session ?? [];
$sessionSpeakers = array_values(array_filter($speakers ?? [], static fn ($speaker): bool => !empty($speaker['speaker_name'])));
$conferenceId    = (int) ($event['conference_id'] ?? session('live-conference-id'));
$isLiveConf      = ($conference['status'] ?? 'live') === 'live';
$agendaUrl       = $isLiveConf ? site_url('mobile/agenda/' . $conferenceId) : site_url('mobile/pastConference/' . $conferenceId);

echo module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => $agendaUrl, 'activeTab' => $isLiveConf ? 'agenda' : 'explore', 'title' => $event['sessions_name'] ?? 'Session', 'screenLabel' => 'Live Session']);

$utc       = new DateTimeZone('UTC');
$hasTimes  = !empty($event['event_date']) && !empty($event['start_time']) && !empty($event['end_time']);
$start     = $hasTimes ? new DateTime($event['event_date'] . ' ' . $event['start_time'], $utc) : null;
$end       = $hasTimes ? new DateTime($event['event_date'] . ' ' . $event['end_time'], $utc) : null;
$now       = new DateTime('now', $utc);
$isLiveNow = $start && $end && $now >= $start && $now < $end;
$dateLong  = !empty($event['event_date']) ? date('F j, Y', strtotime($event['event_date'])) : '';

$canAccess = (int) ($event['access_level'] ?? 1) !== 2 || (int) ($premium ?? 0) === 1;
$videoId   = trim((string) ($event['vimeo_id'] ?? ''));
$videoSrc  = '';
if ($videoId !== '') {
    if ($isLiveConf && preg_match('/^[A-Za-z0-9_-]+$/', $videoId)) {
        $videoSrc = 'https://watch.livepush.io/live/' . rawurlencode($videoId);
    } elseif (ctype_digit($videoId)) {
        $videoSrc = 'https://player.vimeo.com/video/' . $videoId;
    } elseif (str_contains($videoId, 'event')) {
        $videoSrc = 'https://vimeo.com/' . $videoId . '/embed';
    } else {
        $videoSrc = 'https://vimeo.com/event/' . rawurlencode($videoId) . '/embed';
    }
}

$lead      = $sessionSpeakers[0] ?? null;
$leadRole  = $lead ? trim(($lead['speaker_title'] ?? '') . (!empty($lead['speaker_company']) ? ', ' . $lead['speaker_company'] : ''), ', ') : '';
$leadPhoto = $lead && !empty($lead['speaker_photo']) ? base_url('uploads/speakers/' . rawurlencode($lead['speaker_photo'])) : mobile_asset_url('images/user.png');
$sessionId = (int) ($event['sessions_id'] ?? 0);
?>

<div class="m-screen m-screen--16" style="padding-top:18px">
    <div class="m-session__head">
        <a class="m-backlink" href="<?= $agendaUrl ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i><?= $isLiveConf ? 'Agenda' : 'Archive' ?><?= $dateLong !== '' ? ' · ' . esc($dateLong) : '' ?></a>
        <h1 class="m-session__title"><?= esc($event['sessions_name'] ?? 'Session') ?></h1>
        <?php if ($start && $end): ?>
            <div class="m-session__meta">
                <span class="m-mono" data-local-time data-date="<?= esc($event['event_date'], 'attr') ?>" data-start="<?= esc($event['start_time'], 'attr') ?>" data-end="<?= esc($event['end_time'], 'attr') ?>"><span data-lt-start><?= esc($start->format('g:i A')) ?></span>&nbsp;–&nbsp;<span data-lt-end><?= esc($end->format('g:i A')) ?></span>&nbsp;·&nbsp;<span data-lt-zone>GMT</span></span>
                <?php if ($isLiveNow): ?><span><i class="fa fa-eye" aria-hidden="true"></i>Live now</span><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="m-video">
        <?php if ($canAccess && $videoSrc !== ''): ?>
            <iframe src="<?= esc($videoSrc, 'attr') ?>" title="<?= esc($event['sessions_name'] ?? 'Session video', 'attr') ?>" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        <?php else: ?>
            <img class="m-video__speaker" src="<?= esc($leadPhoto, 'attr') ?>" alt="" onerror="this.onerror=null;this.src='<?= mobile_asset_url('images/user.png') ?>'">
            <?php if ($isLiveNow): ?><div class="m-video__tag" style="padding:0;background:transparent"><span class="m-live m-live--sq">LIVE</span></div><?php endif; ?>
            <div class="m-video__lead">
                <?php if (! $canAccess): ?>
                    <b><i class="fa fa-lock" aria-hidden="true"></i> Paid session</b><span>Upgrade to Premium Access to watch.</span>
                <?php elseif ($lead): ?>
                    <b><?= esc($lead['speaker_name']) ?></b><span><?= esc($leadRole !== '' ? $leadRole : 'Video will appear here when the session starts.') ?></span>
                <?php else: ?>
                    <b>Coming up</b><span>Video will appear here when the session starts.</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (! $canAccess): ?>
        <a class="m-btn m-btn--plain" href="<?= site_url('mobile/upgrade') ?>"><i class="fa fa-unlock-alt" aria-hidden="true"></i>Unlock this session</a>
    <?php endif; ?>

    <div class="m-session__tools">
        <button class="m-like" type="button" id="likeSession" aria-pressed="false"><i class="fa fa-heart-o" aria-hidden="true"></i><span>Like Session</span></button>
        <div class="m-rate">
            <span>Rate</span>
            <div class="m-stars" id="rateSession" role="radiogroup" aria-label="Rate this session">
                <?php for ($i = 1; $i <= 5; $i++): ?><button type="button" role="radio" aria-checked="false" aria-label="<?= $i ?> star<?= $i === 1 ? '' : 's' ?>" data-star="<?= $i ?>">★</button><?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="m-seg m-seg--12" data-seg role="tablist">
        <button type="button" role="tab" class="is-active" aria-selected="true" data-seg-target="sessChat">Live chat</button>
        <button type="button" role="tab" aria-selected="false" data-seg-target="sessOverview">Overview</button>
        <button type="button" role="tab" aria-selected="false" data-seg-target="sessSpeakers">Speakers</button>
    </div>

    <div class="m-card m-chat" id="sessChat">
        <div class="m-chat__head"><b>Live chat</b><span class="m-chat__online" id="chatCount">LIVE</span></div>
        <div class="m-chat__list" id="chat-messages"><span class="m-chat__empty" id="chatEmpty">Start the conversation.</span></div>
        <form class="m-chat__form" id="chatForm" autocomplete="off">
            <input class="m-input" id="chatInput" placeholder="Type a message..." maxlength="2000" aria-label="Chat message">
            <button class="m-chat__send" type="submit">Send</button>
        </form>
    </div>

    <div class="m-card m-panel" id="sessOverview" hidden>
        <span class="m-label m-label--wide">Session overview</span>
        <p><?= nl2br(esc(trim((string) ($event['description'] ?? '')) !== '' ? $event['description'] : 'No description available.')) ?></p>
        <?php if (!empty($event['workbook'])): ?>
            <a class="m-btn-ghost m-btn-ghost--38" href="<?= base_url('uploads/workbooks/' . rawurlencode($event['workbook'])) ?>" download><i class="fa fa-download" aria-hidden="true"></i>Download Workbook</a>
        <?php endif; ?>
    </div>

    <div class="m-card m-panel m-panel--14" id="sessSpeakers" hidden>
        <span class="m-label m-label--wide">Speakers</span>
        <?php if ($sessionSpeakers): ?>
            <?php foreach ($sessionSpeakers as $speaker): ?>
                <?php $role = trim(($speaker['speaker_title'] ?? '') . (!empty($speaker['speaker_company']) ? ', ' . $speaker['speaker_company'] : ''), ', '); ?>
                <div class="m-person">
                    <img src="<?= !empty($speaker['speaker_photo']) ? base_url('uploads/speakers/' . rawurlencode($speaker['speaker_photo'])) : mobile_asset_url('images/user.png') ?>" alt="" onerror="this.onerror=null;this.src='<?= mobile_asset_url('images/user.png') ?>'">
                    <div><b><?= esc($speaker['speaker_name']) ?></b><span><?= esc($role) ?></span></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="m-person"><img src="<?= mobile_asset_url('images/user.png') ?>" alt=""><div><b>Speakers TBA</b><span></span></div></div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.css">
<script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sessionId = <?= $sessionId ?>;
    var attendeeId = <?= json_encode((string) session('attendee_id')) ?>;
    var api = document.body.getAttribute('data-api');

    /* Like + rating are kept on this device, as on the previous mobile build. */
    var stateKey = 'epr-mobile-session:' + sessionId;
    var state = {};
    try { state = JSON.parse(localStorage.getItem(stateKey)) || {}; } catch (e) { state = {}; }
    function save() { try { localStorage.setItem(stateKey, JSON.stringify(state)); } catch (e) { /* ignore */ } }

    var like = document.getElementById('likeSession');
    function paintLike() {
        like.classList.toggle('is-on', !!state.liked);
        like.setAttribute('aria-pressed', state.liked ? 'true' : 'false');
        like.querySelector('i').className = 'fa ' + (state.liked ? 'fa-heart' : 'fa-heart-o');
        like.querySelector('span').textContent = state.liked ? 'Liked' : 'Like Session';
    }
    like.addEventListener('click', function () { state.liked = !state.liked; save(); paintLike(); });

    var stars = Array.prototype.slice.call(document.querySelectorAll('#rateSession [data-star]'));
    function paintStars() {
        stars.forEach(function (star) {
            var on = Number(star.getAttribute('data-star')) <= (state.rating || 0);
            star.classList.toggle('is-on', on);
            star.setAttribute('aria-checked', Number(star.getAttribute('data-star')) === (state.rating || 0) ? 'true' : 'false');
        });
    }
    stars.forEach(function (star) {
        star.addEventListener('click', function () {
            state.rating = Number(star.getAttribute('data-star')); save(); paintStars();
            EPR.toast('Thanks for rating this session ' + state.rating + '/5.');
        });
    });
    paintLike(); paintStars();

    /* Live chat */
    var box = document.getElementById('chat-messages');
    var input = document.getElementById('chatInput');
    var form = document.getElementById('chatForm');
    var count = document.getElementById('chatCount');
    var seen = {}, people = {}, names = {};

    function escapeHtml(value) {
        return String(value == null ? '' : value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }
    function formatMessage(text) {
        return escapeHtml(text).replace(/@\{user:(\d+)\}/g, function (match, id) {
            return '<a>@' + escapeHtml(names[id] || 'Attendee') + '</a>';
        }).replace(/\n/g, '<br>');
    }
    function render(message) {
        if (message.id != null) { if (seen[message.id]) return; seen[message.id] = 1; }
        var empty = document.getElementById('chatEmpty'); if (empty) empty.remove();
        var mine = String(message.attendee_id) === attendeeId;
        people[message.attendee_id] = 1;
        var row = document.createElement('div');
        row.className = 'm-chat__row' + (mine ? ' is-mine' : '');
        row.innerHTML = '<div class="m-chat__bubble"><strong>' + escapeHtml(message.attendee_name || names[message.attendee_id] || 'Attendee') + '</strong><br>' + formatMessage(message.message) + '</div>';
        box.appendChild(row);
        box.scrollTop = box.scrollHeight;
        var total = Object.keys(people).length;
        count.textContent = total + ' CHATTING';
    }
    function load() {
        return fetch(api + '/chat/' + sessionId, { credentials: 'same-origin' })
            .then(function (res) { return res.json(); })
            .then(function (payload) { (Array.isArray(payload && payload.data) ? payload.data : []).forEach(render); })
            .catch(function () { /* keep the last rendered state */ });
    }
    function tokenise(text) {
        Object.keys(names).sort(function (a, b) { return names[b].length - names[a].length; }).forEach(function (id) {
            if (!names[id]) return;
            text = text.split('@' + names[id]).join('@{user:' + id + '}');
        });
        return text;
    }
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        EPR.post(api + '/chat/send/' + sessionId, { attendee_id: attendeeId, message: tokenise(text) }, true).then(function (res) {
            if (!res.ok) { EPR.toast((res.body && res.body.messages && res.body.messages.error) || 'Unable to send your message.', true); input.value = text; return; }
            load();
        }).catch(function () { EPR.toast('Unable to send your message.', true); input.value = text; });
    });

    fetch(api + '/attendees/all', { credentials: 'same-origin' })
        .then(function (res) { return res.json(); })
        .then(function (json) {
            var list = Array.isArray(json && json.data) ? json.data : (Array.isArray(json) ? json : []);
            var values = [];
            list.forEach(function (person) {
                var name = ((person.firstname || '') + ' ' + (person.lastname || '')).trim();
                if (!name) return;
                names[String(person.id)] = name;
                values.push({ key: name, id: String(person.id) });
            });
            if (window.Tribute && values.length) {
                new Tribute({ trigger: '@', lookup: 'key', values: values, selectTemplate: function (item) { return '@' + item.original.key; }, menuItemTemplate: function (item) { return item.string; } }).attach(input);
            }
        })
        .catch(function () { /* mentions are optional */ })
        .then(load)
        .then(function () { window.setInterval(function () { if (!document.hidden) load(); }, 3000); });

    document.addEventListener('seg:change', function (event) { if (event.detail === 'sessChat') box.scrollTop = box.scrollHeight; });
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
