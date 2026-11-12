<?= module_view('MobileApp', 'includes/header', ['shell' => 'tabbed', 'back' => false, 'activeTab' => 'booth', 'title' => 'Emergence Booth', 'screenLabel' => 'Emergence Booth']) ?>
<?php
$members    = $members ?? [];
$programmes = $programmes ?? [];
$resources  = $resources ?? [];
$videoUrl   = (string) ($videoUrl ?? '');
$topics     = ['Programmes', 'Partnership', 'Speaking', 'Media'];
?>

<div class="m-screen m-screen--22">
    <div class="m-head">
        <span class="m-kicker">EPR Global · Host Booth</span>
        <h1 class="m-title">Emergence Booth</h1>
        <span class="m-sub">Meet the team, explore programmes and resources, or start a conversation.</span>
    </div>

    <?php if (! ($ready ?? false)): ?>
        <div class="m-card m-info m-info--20"><b>Emergence Booth is being prepared.</b><span>Please check back shortly.</span></div>
    <?php else: ?>
        <div class="m-video" data-video-src="<?= esc($videoUrl, 'attr') ?>" data-video-title="Welcome film">
            <img class="m-video__ghost" src="<?= mobile_asset_url('images/accelerate-logo-white.png') ?>" alt="" data-video-hide>
            <?php if ($videoUrl !== ''): ?>
                <button class="m-play m-play--64" type="button" data-play data-video-hide aria-label="Play welcome film"><i class="fa fa-play" aria-hidden="true"></i></button>
            <?php endif; ?>
            <span class="m-video__tag" data-video-hide><i class="fa fa-film" aria-hidden="true"></i>WELCOME FILM</span>
            <div class="m-video__caption" data-video-hide>Why we build systems, not hype</div>
        </div>

        <aside class="m-card m-panel m-panel--14">
            <span class="m-label">AT THE BOOTH</span>
            <?php foreach ($members as $member): ?>
                <?php
                $initials = implode('', array_map(static fn ($part) => strtoupper(mb_substr($part, 0, 1)), array_slice(array_filter(explode(' ', (string) $member['display_name'])), 0, 2)));
                $isOnline = !empty($member['is_online']);
                ?>
                <div class="m-member">
                    <span class="m-member__avatar"><?= esc($initials) ?><i class="<?= $isOnline ? 'is-online' : '' ?>"></i></span>
                    <div class="m-member__id"><b><?= esc($member['display_name']) ?></b><span><?= esc($member['title'] ?: 'EPR Global Team') ?> · <?= $isOnline ? 'Online' : 'Offline' ?></span></div>
                    <button class="m-member__chat" type="button" data-member-chat="<?= (int) $member['booth_member_id'] ?>" data-member-name="<?= esc($member['display_name'], 'attr') ?>"><i class="fa fa-comment" aria-hidden="true"></i><?= $isOnline ? 'Chat now' : 'Message' ?></button>
                </div>
            <?php endforeach; ?>
            <?php if (! $members): ?>
                <span class="m-sub">Our team will be online during booth hours.</span>
            <?php endif; ?>
        </aside>

        <section class="m-stack" style="gap:12px">
            <span class="m-label" style="padding:0 4px">PROGRAMMES</span>
            <?php foreach ($programmes as $programme): ?>
                <article class="m-card m-card--20 m-programme">
                    <span class="m-programme__icon"><i class="fa <?= esc($programme['icon'] ?: 'fa-rocket', 'attr') ?>" aria-hidden="true"></i></span>
                    <span class="m-programme__label"><?= esc($programme['label']) ?></span>
                    <h3><?= esc($programme['title']) ?></h3>
                    <p><?= esc($programme['description']) ?></p>
                </article>
            <?php endforeach; ?>
            <?php if (! $programmes): ?>
                <div class="m-card m-info"><span>Programmes will appear here soon.</span></div>
            <?php endif; ?>
        </section>

        <div class="m-card m-toolkit">
            <span class="m-label">TAKE-HOME TOOLKIT</span>
            <?php foreach ($resources as $resource): ?>
                <a class="m-resource" href="<?= base_url(ltrim((string) $resource['file_path'], '/')) ?>" download="<?= esc($resource['file_name'], 'attr') ?>">
                    <span class="m-resource__icon"><i class="fa fa-download" aria-hidden="true"></i></span>
                    <span class="m-resource__copy"><b><?= esc($resource['title']) ?></b><small><?= esc($resource['meta']) ?></small></span>
                    <i class="fa fa-download" aria-hidden="true"></i>
                </a>
            <?php endforeach; ?>
            <?php if (! $resources): ?>
                <span class="m-sub">Resources will be added soon.</span>
            <?php endif; ?>
        </div>

        <form class="m-card m-callback" id="callbackForm">
            <span class="m-label">REQUEST A CALLBACK</span>
            <span class="m-callback__copy">Tell us what you would like to discuss. An EPR team member will follow up.</span>
            <div class="m-topics">
                <?php foreach ($topics as $index => $topic): ?>
                    <button class="m-topic <?= $index === 0 ? 'is-active' : '' ?>" type="button" data-topic="<?= esc($topic, 'attr') ?>"><?= esc($topic) ?></button>
                <?php endforeach; ?>
            </div>
            <textarea class="m-input" name="note" rows="4" placeholder="Add a short note (optional)" maxlength="5000" style="background:rgba(255,255,255,.04)"></textarea>
            <button class="m-btn m-btn--46" type="submit"><i class="fa fa-phone" aria-hidden="true"></i>Request a callback</button>
            <span class="m-ok" id="callbackStatus" role="status" hidden>Request received. We will be in touch.</span>
        </form>
    <?php endif; ?>
</div>

<aside class="m-sheet" data-sheet="memberChat" role="dialog" aria-modal="true" aria-label="Booth chat" hidden>
    <div class="m-sheet__grip"></div>
    <div class="m-memberchat">
        <header class="m-memberchat__head"><b id="memberChatName">Chat with EPR</b><button class="m-x" type="button" data-close-sheet aria-label="Close chat">×</button></header>
        <div class="m-memberchat__list" id="memberChatList"></div>
        <form class="m-memberchat__form" id="memberChatForm" autocomplete="off">
            <input class="m-input" id="memberChatInput" maxlength="2000" placeholder="Type a message…" aria-label="Message">
            <button type="submit">Send</button>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var chatUrl = <?= json_encode(site_url('mobile/emergence-booth/chat')) ?> + '/';
    var callbackUrl = <?= json_encode(site_url('mobile/emergence-booth/callback')) ?>;
    var sheet = document.querySelector('.m-sheet[data-sheet="memberChat"]');
    var list = document.getElementById('memberChatList');
    var input = document.getElementById('memberChatInput');
    var memberId = null, poller = 0, signature = '';

    function render(rows) {
        var next = JSON.stringify(rows);
        if (next === signature) return;
        signature = next;
        list.textContent = '';
        if (!rows.length) {
            var empty = document.createElement('span'); empty.className = 'm-chat__empty'; empty.textContent = 'Start the conversation.'; list.appendChild(empty);
        }
        rows.forEach(function (row) {
            var item = document.createElement('div');
            item.className = 'm-memberchat__msg' + (row.sender_type === 'attendee' ? ' is-mine' : '');
            item.textContent = row.message;
            list.appendChild(item);
        });
        list.scrollTop = list.scrollHeight;
    }
    function load() {
        if (!memberId) return;
        fetch(chatUrl + memberId, { credentials: 'same-origin' }).then(function (res) { return res.json(); }).then(function (data) { render(data.data || []); }).catch(function () { /* keep last state */ });
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-member-chat]');
        if (button) {
            memberId = button.getAttribute('data-member-chat'); signature = '';
            document.getElementById('memberChatName').textContent = 'Chat with ' + button.getAttribute('data-member-name');
            list.textContent = ''; input.value = '';
            EPR.openSheet('memberChat');
            load(); clearInterval(poller); poller = setInterval(load, 2000);
            return;
        }
        var topic = event.target.closest('[data-topic]');
        if (topic) {
            Array.prototype.forEach.call(document.querySelectorAll('[data-topic]'), function (item) { item.classList.toggle('is-active', item === topic); });
            document.getElementById('callbackStatus').hidden = true;
        }
    });
    sheet.addEventListener('sheet:close', function () { clearInterval(poller); memberId = null; });

    document.getElementById('memberChatForm').addEventListener('submit', function (event) {
        event.preventDefault();
        var message = input.value.trim();
        if (!message || !memberId) return;
        input.value = '';
        EPR.post(chatUrl + memberId, { message: message }).then(function (res) {
            if (!res.ok) { EPR.toast((res.body && res.body.message) || 'Unable to send your message.', true); input.value = message; return; }
            signature = ''; load();
        }).catch(function () { EPR.toast('Unable to send your message.', true); input.value = message; });
    });

    var callback = document.getElementById('callbackForm');
    if (callback) {
        callback.addEventListener('submit', function (event) {
            event.preventDefault();
            var active = callback.querySelector('[data-topic].is-active');
            var status = document.getElementById('callbackStatus');
            var button = callback.querySelector('button[type="submit"]'); button.disabled = true;
            EPR.post(callbackUrl, { topic: active ? active.getAttribute('data-topic') : '', note: callback.note.value.trim() }).then(function (res) {
                if (!res.ok || !res.body || res.body.status !== 'success') throw new Error((res.body && res.body.message) || '');
                callback.note.value = ''; status.hidden = false;
            }).catch(function (error) {
                EPR.toast(error.message || 'Unable to send your request.', true);
            }).then(function () { button.disabled = false; });
        });
    }
});
</script>

<?= module_view('MobileApp', 'includes/footer') ?>
