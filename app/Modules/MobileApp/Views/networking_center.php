<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => false, 'activeTab' => 'network', 'title' => 'Networking Center', 'screenLabel' => 'Networking Center']) ?>
<?php
$isSpeakers  = ($activeTab ?? 'attendees') === 'speakers';

// Attendees are sent as compact rows and drawn in batches on the device, so a large
// attendee list does not turn into a multi-megabyte page.
$attendeeRows = [];
foreach (($attendees ?? []) as $person) {
    $attendeeRows[] = [
        (int) $person['attendee_id'],
        trim((string) ($person['firstname'] ?? '')),
        trim((string) ($person['lastname'] ?? '')),
        trim((string) ($person['position'] ?? '')),
        trim((string) ($person['company'] ?? '')),
        trim((string) ($person['country'] ?? '')),
        !empty($person['profile_picture']) ? '/uploads/attendee_pictures/' . rawurlencode($person['profile_picture']) : '',
        !empty($person['is_online']) ? 1 : 0,
        (string) ($person['connection'] ?? 'none'),
    ];
}
?>

<div class="m-screen m-screen--16">
    <div class="m-head">
        <span class="m-kicker">Accelerate 2026</span>
        <h1 class="m-title m-title--30">Networking Center</h1>
    </div>

    <div class="m-seg" role="tablist" id="netTabs">
        <button type="button" role="tab" class="<?= $isSpeakers ? '' : 'is-active' ?>" aria-selected="<?= $isSpeakers ? 'false' : 'true' ?>" data-net-tab="attendees">ATTENDEES</button>
        <button type="button" role="tab" class="<?= $isSpeakers ? 'is-active' : '' ?>" aria-selected="<?= $isSpeakers ? 'true' : 'false' ?>" data-net-tab="speakers">SPEAKERS</button>
    </div>

    <div class="m-field">
        <i class="fa fa-search" aria-hidden="true"></i>
        <input class="m-input m-search" id="netSearch" type="search" placeholder="<?= $isSpeakers ? 'Search speakers' : 'Search attendees or companies' ?>" aria-label="Search">
    </div>

    <div class="m-chips" id="netFilters"<?= $isSpeakers ? ' hidden' : '' ?>>
        <button class="m-chip is-active" type="button" data-net-filter="all">All</button>
        <button class="m-chip" type="button" data-net-filter="online">Online now</button>
        <button class="m-chip" type="button" data-net-filter="pending">Requested</button>
    </div>

    <div class="m-stack" id="netAttendees" style="gap:16px"<?= $isSpeakers ? ' hidden' : '' ?>></div>
    <div id="netMore" aria-hidden="true" style="height:1px"></div>

    <div class="m-stack" id="netSpeakers" style="gap:16px"<?= $isSpeakers ? '' : ' hidden' ?>>
        <?php foreach (($speakers ?? []) as $speaker): ?>
            <?php
            $title    = trim(($speaker['speaker_title'] ?? '') . (!empty($speaker['speaker_company']) ? ', ' . $speaker['speaker_company'] : ''), ', ');
            $photo    = !empty($speaker['speaker_photo']) ? base_url('uploads/speakers/' . rawurlencode($speaker['speaker_photo'])) : mobile_asset_url('images/user.png');
            $sessions = array_values(array_filter(array_map(static fn ($s) => !empty($s['sessions_id']) ? ['id' => (int) $s['sessions_id'], 'name' => (string) $s['sessions_name']] : null, $speaker['sessions'] ?? [])));
            ?>
            <article class="m-card m-card--20 m-speaker" data-speaker
                     data-name="<?= esc($speaker['speaker_name'], 'attr') ?>" data-title="<?= esc($title, 'attr') ?>" data-photo="<?= esc($photo, 'attr') ?>"
                     data-bio="<?= esc((string) ($speaker['bio'] ?? ''), 'attr') ?>" data-sessions="<?= esc(json_encode($sessions), 'attr') ?>"
                     data-search="<?= esc(mb_strtolower(($speaker['speaker_name'] ?? '') . ' ' . $title), 'attr') ?>">
                <img src="<?= esc($photo, 'attr') ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='<?= mobile_asset_url('images/user.png') ?>'">
                <div><b><?= esc($speaker['speaker_name']) ?></b><span><?= esc($title) ?></span></div>
                <button class="m-iconbtn" type="button" data-speaker-view aria-label="View profile"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="m-empty" id="netEmpty" hidden>No matches. Try a different name.</div>
</div>

<aside class="m-sheet" data-sheet="person" role="dialog" aria-modal="true" aria-label="Attendee profile" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-hero">
        <div class="m-hero__avatar" id="personAvatar"></div>
        <span class="m-hero__status" id="personStatus">Offline</span>
        <span class="m-hero__name" id="personName"></span>
        <span class="m-hero__role" id="personRole"></span>
    </div>
    <div class="m-sheet__pad m-sheet__pad--14">
        <span class="m-fact"><i class="fa fa-briefcase" aria-hidden="true"></i><strong>Company</strong><em id="personCompany"></em></span>
        <span class="m-fact"><i class="fa fa-map-marker" aria-hidden="true"></i><strong>Country</strong><em id="personCountry"></em></span>
        <div class="m-locknote" id="personNote"></div>
        <div class="m-attendee__actions">
            <button class="m-connect" type="button" id="personConnect"><i class="fa fa-user-plus" aria-hidden="true"></i><span>Connect</span></button>
            <button class="m-iconbtn" type="button" id="personMessage" aria-label="Message"><i class="fa fa-comment-o" aria-hidden="true"></i></button>
        </div>
    </div>
</aside>

<aside class="m-sheet" data-sheet="speaker" role="dialog" aria-modal="true" aria-label="Speaker profile" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-hero">
        <img id="speakerPhoto" src="<?= mobile_asset_url('images/user.png') ?>" alt="">
        <span class="m-hero__name" id="speakerName"></span>
        <span class="m-hero__role" id="speakerTitle"></span>
    </div>
    <div class="m-sheet__pad">
        <p class="m-bio" id="speakerBio" hidden></p>
        <span class="m-eyebrow">Sessions</span>
        <div class="m-stack" id="speakerSessions" style="gap:12px"></div>
    </div>
</aside>

<aside class="m-sheet" data-sheet="message" role="dialog" aria-modal="true" aria-label="Send a message" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-sheet__bar"><b id="messageTitle">Send a message</b><button class="m-x" type="button" data-close-sheet aria-label="Close">×</button></div>
    <form class="m-sheet__body" id="messageForm">
        <textarea class="m-input" name="message" rows="4" placeholder="Enter message here..." maxlength="2000" required></textarea>
        <button class="m-btn m-btn--plain" type="submit">Send</button>
    </form>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var api = document.body.getAttribute('data-api');
    var userId = document.body.getAttribute('data-user');
    var q = function (sel, root) { return (root || document).querySelector(sel); };
    var all = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

    var tab = <?= json_encode($isSpeakers ? 'speakers' : 'attendees') ?>, filter = 'all';
    var search = q('#netSearch'), empty = q('#netEmpty');
    var attendeeList = q('#netAttendees'), speakerList = q('#netSpeakers'), filters = q('#netFilters');
    var speakers = all('[data-speaker]');
    var tones = ['#9d0f82', '#742c76', '#d8198e'];
    var labels = { none: ['', 'fa-user-plus', 'Connect'], incoming: ['', 'fa-user-plus', 'Accept'], pending: ['is-pending', 'fa-clock-o', 'Pending'], connected: ['is-connected', 'fa-check', 'Connected'] };
    var people = <?= json_encode($attendeeRows, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>.map(function (row, index) {
        var name = (row[1] + ' ' + row[2]).trim() || 'Attendee';
        return { id: String(row[0]), name: name, first: row[1] || 'them', initials: ((row[1].charAt(0) + row[2].charAt(0)).toUpperCase()) || '?',
            role: row[3] || 'Conference attendee', company: row[4], country: row[5], photo: row[6], online: row[7] ? '1' : '0', state: row[8],
            tone: tones[index % 3], search: (name + ' ' + row[4] + ' ' + row[3]).toLowerCase() };
    });
    var byId = {}; people.forEach(function (person) { byId[person.id] = person; });
    var matches = [], shown = 0, BATCH = 30;

    function el(tag, className, text) { var node = document.createElement(tag); if (className) node.className = className; if (text != null) node.textContent = text; return node; }
    function card(person) {
        var article = el('article', 'm-card m-card--20 m-attendee');
        article.setAttribute('data-person', '');
        ['id', 'name', 'first', 'initials', 'role', 'company', 'country', 'photo', 'tone', 'online', 'state'].forEach(function (key) { article.dataset[key] = person[key]; });

        var top = el('button', 'm-attendee__top'); top.type = 'button'; top.setAttribute('data-person-view', '');
        var avatar = el('span', 'm-avatar'); avatar.style.background = person.tone;
        if (person.photo) {
            var img = el('img'); img.alt = ''; img.loading = 'lazy'; img.src = person.photo;
            img.onerror = function () { img.replaceWith(document.createTextNode(person.initials)); };
            avatar.appendChild(img);
        } else avatar.appendChild(document.createTextNode(person.initials));
        if (person.online === '1') avatar.appendChild(el('span', 'm-avatar__dot'));
        var id = el('span', 'm-attendee__id'); id.appendChild(el('b', '', person.name)); id.appendChild(el('span', '', person.role));
        top.appendChild(avatar); top.appendChild(id);

        var tags = el('div', 'm-tags');
        if (person.company) tags.appendChild(el('span', 'm-tag', person.company));
        if (person.country) tags.appendChild(el('span', 'm-tag', person.country));

        var cfg = labels[person.state] || labels.none;
        var actions = el('div', 'm-attendee__actions');
        var connectButton = el('button', 'm-connect' + (cfg[0] ? ' ' + cfg[0] : '')); connectButton.type = 'button'; connectButton.setAttribute('data-person-connect', '');
        var icon = el('i', 'fa ' + cfg[1]); icon.setAttribute('aria-hidden', 'true');
        connectButton.appendChild(icon); connectButton.appendChild(el('span', '', cfg[2]));
        var messageButton = el('button', 'm-iconbtn'); messageButton.type = 'button'; messageButton.setAttribute('data-person-message', ''); messageButton.setAttribute('aria-label', 'Message ' + person.name);
        var bubble = el('i', 'fa fa-comment-o'); bubble.setAttribute('aria-hidden', 'true'); messageButton.appendChild(bubble);
        actions.appendChild(connectButton); actions.appendChild(messageButton);

        article.appendChild(top); article.appendChild(tags); article.appendChild(actions);
        return article;
    }
    function more() {
        var fragment = document.createDocumentFragment(), end = Math.min(shown + BATCH, matches.length);
        for (; shown < end; shown++) fragment.appendChild(card(matches[shown]));
        attendeeList.appendChild(fragment);
    }
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) { if (entries[0].isIntersecting && tab === 'attendees') more(); }, { rootMargin: '600px' }).observe(q('#netMore'));
    } else {
        window.addEventListener('scroll', function () { if (tab === 'attendees' && window.innerHeight + window.scrollY > document.body.offsetHeight - 800) more(); });
    }

    function apply() {
        var term = search.value.trim().toLowerCase(), visible = 0;
        attendeeList.hidden = tab !== 'attendees'; speakerList.hidden = tab !== 'speakers'; filters.hidden = tab !== 'attendees';
        if (tab === 'attendees') {
            matches = people.filter(function (person) {
                return (filter === 'all' || (filter === 'online' && person.online === '1') || (filter === 'pending' && person.state === 'pending'))
                    && (!term || person.search.indexOf(term) !== -1);
            });
            attendeeList.textContent = ''; shown = 0; more();
            visible = matches.length;
        } else {
            speakers.forEach(function (card) { var show = !term || card.dataset.search.indexOf(term) !== -1; card.hidden = !show; if (show) visible++; });
        }
        empty.hidden = visible > 0;
    }

    all('[data-net-tab]').forEach(function (button) {
        button.addEventListener('click', function () {
            tab = button.dataset.netTab; search.value = '';
            search.placeholder = tab === 'attendees' ? 'Search attendees or companies' : 'Search speakers';
            all('[data-net-tab]').forEach(function (b) { b.classList.toggle('is-active', b === button); b.setAttribute('aria-selected', b === button ? 'true' : 'false'); });
            apply();
        });
    });
    all('[data-net-filter]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            filter = chip.dataset.netFilter;
            all('[data-net-filter]').forEach(function (c) { c.classList.toggle('is-active', c === chip); });
            apply();
        });
    });
    search.addEventListener('input', apply);

    /* Connect */
    function paintButton(button, state) {
        var cfg = labels[state] || labels.none;
        button.className = 'm-connect' + (cfg[0] ? ' ' + cfg[0] : '');
        button.querySelector('i').className = 'fa ' + cfg[1];
        button.querySelector('span').textContent = cfg[2];
    }
    function connect(card) {
        if (card.dataset.state === 'pending' || card.dataset.state === 'connected') {
            EPR.toast(card.dataset.state === 'connected' ? 'You are already connected with ' + card.dataset.first + '.' : 'Your request to ' + card.dataset.first + ' is pending.');
            return;
        }
        EPR.post(api + '/connections', { receiver_id: card.dataset.id }, true).then(function (res) {
            if (!res.ok) throw new Error();
            var data = (res.body && res.body.data) || {};
            card.dataset.state = data.connection_status === 'accepted' ? 'connected' : 'pending';
            if (byId[card.dataset.id]) byId[card.dataset.id].state = card.dataset.state;
            paintButton(q('[data-person-connect]', card), card.dataset.state);
            if (current === card) fillPerson(card);
            EPR.toast(card.dataset.state === 'connected' ? 'You are now connected with ' + card.dataset.first + '.' : 'Connection request sent to ' + card.dataset.first + '.');
        }).catch(function () { EPR.toast('Unable to send the request. Please try again.', true); });
    }

    /* Person sheet */
    var current = null;
    function fillPerson(card) {
        current = card;
        var avatar = q('#personAvatar');
        avatar.style.background = card.dataset.tone;
        avatar.textContent = '';
        if (card.dataset.photo) { var img = document.createElement('img'); img.src = card.dataset.photo; img.alt = ''; img.onerror = function () { img.remove(); avatar.textContent = card.dataset.initials; }; avatar.appendChild(img); }
        else avatar.textContent = card.dataset.initials;
        var online = card.dataset.online === '1', status = q('#personStatus');
        status.textContent = online ? 'Online' : 'Offline'; status.classList.toggle('is-online', online);
        q('#personName').textContent = card.dataset.name;
        q('#personRole').textContent = card.dataset.role + (card.dataset.company ? ' · ' + card.dataset.company : '');
        q('#personCompany').textContent = card.dataset.company || '—';
        q('#personCountry').textContent = card.dataset.country || '—';
        q('#personNote').textContent = card.dataset.state === 'connected'
            ? 'You and ' + card.dataset.first + ' are connected.'
            : 'Social links unlock once ' + card.dataset.first + ' accepts your connection.';
        paintButton(q('#personConnect'), card.dataset.state);
    }
    q('#personConnect').addEventListener('click', function () { if (current) connect(current); });

    /* Message sheet */
    var messageTarget = null, messageForm = q('#messageForm');
    function openMessage(card) {
        messageTarget = card;
        q('#messageTitle').textContent = 'Message ' + card.dataset.first;
        messageForm.reset();
        EPR.openSheet('message');
    }
    q('#personMessage').addEventListener('click', function () { if (current) openMessage(current); });
    messageForm.addEventListener('submit', function (event) {
        event.preventDefault();
        var text = messageForm.message.value.trim();
        if (!text || !messageTarget) return;
        var button = messageForm.querySelector('button'); button.disabled = true;
        EPR.post(api + '/messages/send', { sender_id: userId, receiver_id: messageTarget.dataset.id, message: text }, true).then(function (res) {
            if (!res.ok || (res.body && res.body.status && res.body.status !== 'success')) throw new Error((res.body && res.body.message) || '');
            EPR.closeSheet();
            EPR.toast('Message sent to ' + messageTarget.dataset.first + '.');
        }).catch(function (error) {
            EPR.toast(error.message || 'Message could not be sent.', true);
        }).then(function () { button.disabled = false; });
    });

    document.addEventListener('click', function (event) {
        var card = event.target.closest('[data-person]');
        if (card) {
            if (event.target.closest('[data-person-view]')) { fillPerson(card); EPR.openSheet('person'); }
            else if (event.target.closest('[data-person-connect]')) connect(card);
            else if (event.target.closest('[data-person-message]')) openMessage(card);
            return;
        }
        var view = event.target.closest('[data-speaker-view]');
        if (view) {
            var speaker = view.closest('[data-speaker]');
            q('#speakerPhoto').src = speaker.dataset.photo;
            q('#speakerName').textContent = speaker.dataset.name;
            q('#speakerTitle').textContent = speaker.dataset.title;
            var bio = q('#speakerBio'); bio.textContent = speaker.dataset.bio; bio.hidden = !speaker.dataset.bio;
            var list = q('#speakerSessions'); list.textContent = '';
            var sessions = []; try { sessions = JSON.parse(speaker.dataset.sessions) || []; } catch (e) { sessions = []; }
            sessions.forEach(function (item) {
                var row = document.createElement('a'); row.className = 'm-srow'; row.href = <?= json_encode(site_url('mobile/session')) ?> + '/' + item.id;
                var body = document.createElement('div'), name = document.createElement('b'), arrow = document.createElement('i');
                name.textContent = item.name; body.appendChild(name); arrow.className = 'fa fa-long-arrow-right'; arrow.setAttribute('aria-hidden', 'true');
                row.appendChild(body); row.appendChild(arrow); list.appendChild(row);
            });
            if (!sessions.length) { var none = document.createElement('p'); none.className = 'm-bio'; none.textContent = 'No sessions scheduled yet.'; list.appendChild(none); }
            EPR.openSheet('speaker');
        }
    });

    apply();
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
