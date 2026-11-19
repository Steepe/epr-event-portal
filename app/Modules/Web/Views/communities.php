<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$communities = $communities ?? [];
$tabs = [
    ['ATTENDEES', base_url('attendees/attendees'), false],
    ['SPEAKERS', base_url('attendees/speakers'), false],
    ['COMMUNITIES', base_url('attendees/communities'), true],
];
$initials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    return strtoupper(mb_substr((string) ($parts[0] ?? 'C'), 0, 1) . mb_substr((string) ($parts[1] ?? ''), 0, 1));
};
?>

<main class="rc-page rc-page-center" data-screen-label="Communities">
    <div class="rc-container" style="gap:24px">
        <header class="rc-net-sub-header">
            <div class="rc-net-heading">
                <a href="<?php echo base_url('attendees/networking_center'); ?>" class="rc-back-link"><i class="fa fa-arrow-left" aria-hidden="true"></i>NETWORKING CENTER</a>
                <span class="rc-page-title">Communities</span>
                <span class="rc-page-sub">Find your country community, meet its EPR Country Manager and join the conversation.</span>
            </div>
            <div class="rc-net-tabs">
                <?php foreach ($tabs as [$label, $url, $active]): ?><a href="<?php echo $url; ?>" class="rc-net-tab <?php echo $active ? 'is-active' : ''; ?>"><?php echo esc($label); ?></a><?php endforeach; ?>
            </div>
        </header>

        <?php if (! ($ready ?? false)): ?>
            <div class="rc-panel" style="padding:28px;text-align:center"><strong>Communities are being prepared.</strong><p style="margin:10px 0 0;color:rgba(255,255,255,.7)">Please check back shortly.</p></div>
        <?php elseif ($communities === []): ?>
            <div class="rc-panel" style="padding:28px;text-align:center"><strong>Country communities are coming soon.</strong><p style="margin:10px 0 0;color:rgba(255,255,255,.7)">EPR Country Managers will appear here as each community opens.</p></div>
        <?php else: ?>
            <section class="rc-attendee-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
                <?php foreach ($communities as $community): ?>
                    <?php
                    $managerName = trim((string) ($community['manager_name'] ?? 'EPR Country Manager'));
                    $photo = ! empty($community['manager_photo']) ? base_url(ltrim((string) $community['manager_photo'], '/')) : '';
                    $qrUrl = ! empty($community['whatsapp_qr_path']) ? base_url(ltrim((string) $community['whatsapp_qr_path'], '/')) : '';
                    ?>
                    <article class="rc-person-card" style="padding:22px;display:flex;flex-direction:column;gap:16px">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                            <span class="rc-mono" style="font-size:11px;letter-spacing:.16em;color:#efb11e"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc(mb_strtoupper((string) $community['country'])); ?></span>
                            <span style="font-size:11px;padding:5px 8px;border-radius:999px;background:rgba(216,25,142,.17);color:#f0b4e9">EPR COMMUNITY</span>
                        </div>

                        <div style="display:flex;align-items:center;gap:14px">
                            <span class="rc-avatar-token" style="width:68px;height:68px;flex:none">
                                <?php if ($photo): ?><img src="<?php echo esc($photo, 'attr'); ?>" alt="<?php echo esc($managerName); ?>" style="width:100%;height:100%;object-fit:cover" onerror="this.remove();this.parentNode.querySelector('[data-manager-initials]').style.display='inline-flex';"><?php endif; ?>
                                <span data-manager-initials style="<?php echo $photo ? 'display:none;' : 'display:inline-flex;'; ?>width:100%;height:100%;align-items:center;justify-content:center;font-size:20px"><?php echo esc($initials($managerName)); ?></span>
                            </span>
                            <div style="min-width:0;display:flex;flex-direction:column;gap:3px"><span style="font-size:16px;font-weight:600"><?php echo esc($managerName); ?></span><span style="font-size:13px;color:rgba(255,255,255,.72)"><?php echo esc($community['manager_title'] ?: 'EPR Country Manager'); ?></span><span style="font-size:12px;color:rgba(255,255,255,.52)">EPR Global · <?php echo esc($community['country']); ?></span></div>
                        </div>

                        <p style="margin:0;color:rgba(255,255,255,.74);font-size:13px;line-height:1.55">Connect with your local EPR community, ask a question, or get support from the Country Manager.</p>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px;margin-top:auto">
                            <button type="button" class="rc-card-action rc-community-chat" data-community-id="<?php echo (int) $community['community_id']; ?>" data-manager-name="<?php echo esc($managerName, 'attr'); ?>" style="justify-content:center"><i class="fa fa-comment" aria-hidden="true"></i><span>Chat with manager</span></button>
                            <?php if (! empty($community['whatsapp_url'])): ?>
                                <a href="<?php echo esc($community['whatsapp_url'], 'attr'); ?>" target="_blank" rel="noopener" class="rc-card-action" style="justify-content:center;text-decoration:none;background:rgba(37,211,102,.18);border-color:rgba(37,211,102,.55);color:#fff"><i class="fa fa-whatsapp" aria-hidden="true"></i><span>Join community</span></a>
                            <?php else: ?>
                                <button type="button" class="rc-card-action rc-community-qr" data-community-country="<?php echo esc($community['country'], 'attr'); ?>" data-qr-url="<?php echo esc($qrUrl, 'attr'); ?>" style="justify-content:center;background:rgba(37,211,102,.18);border-color:rgba(37,211,102,.55)"><i class="fa fa-qrcode" aria-hidden="true"></i><span>Join community</span></button>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>
</main>

<aside data-community-chat-panel hidden class="rc-panel" style="position:fixed;z-index:40;right:24px;bottom:24px;width:min(380px,calc(100vw - 32px));height:min(540px,calc(100vh - 48px));padding:0;display:flex;flex-direction:column;overflow:hidden;background:#201024">
    <header style="padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.12);display:flex;justify-content:space-between;align-items:center"><strong data-community-chat-name>Chat with Country Manager</strong><button type="button" data-community-chat-close aria-label="Close chat" style="border:0;background:transparent;color:#fff;font-size:22px;cursor:pointer">×</button></header>
    <div data-community-chat-messages style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px"></div>
    <form data-community-chat-form style="padding:12px;border-top:1px solid rgba(255,255,255,.12);display:flex;gap:8px"><input data-community-chat-input class="rc-input" maxlength="2000" placeholder="Type a message…" style="flex:1;min-width:0"><button class="rc-btn" type="submit" style="height:42px;padding:0 14px">Send</button></form>
</aside>

<aside data-community-qr-panel hidden class="rc-panel" style="position:fixed;z-index:41;left:50%;top:50%;transform:translate(-50%,-50%);width:min(360px,calc(100vw - 32px));padding:24px;text-align:center;background:#201024">
    <button type="button" data-community-qr-close aria-label="Close QR code" style="position:absolute;right:10px;top:7px;border:0;background:transparent;color:#fff;font-size:24px;cursor:pointer">×</button>
    <span class="rc-mono" style="font-size:11px;letter-spacing:.14em;color:#efb11e">JOIN COMMUNITY</span>
    <h2 data-community-qr-country style="margin:8px 0 16px;font-size:22px"></h2>
    <img data-community-qr-image src="" alt="" style="display:block;width:min(250px,100%);margin:auto;border-radius:12px;background:#fff;padding:10px">
    <p style="margin:16px 0 0;color:rgba(255,255,255,.7);font-size:13px">Scan this QR code with WhatsApp to join the community.</p>
</aside>

</div></div>
<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrf = <?php echo json_encode(csrf_hash()); ?>;
    const panel = document.querySelector('[data-community-chat-panel]');
    const messages = document.querySelector('[data-community-chat-messages]');
    const chatName = document.querySelector('[data-community-chat-name]');
    const chatForm = document.querySelector('[data-community-chat-form]');
    const chatInput = document.querySelector('[data-community-chat-input]');
    const qrPanel = document.querySelector('[data-community-qr-panel]');
    let communityId = null, poller = null, lastSignature = '';
    const endpoint = id => <?php echo json_encode(site_url('attendees/communities/chat/')); ?> + id;
    const request = (url, options = {}) => fetch(url, {...options, headers: {...(options.headers || {}), 'X-CSRF-TOKEN': csrf}}).then(r => r.json());
    function renderChat(rows) {
        const signature = JSON.stringify(rows);
        if (signature === lastSignature) return;
        lastSignature = signature;
        messages.innerHTML = '';
        if (!rows.length) messages.innerHTML = '<span style="margin:auto;color:rgba(255,255,255,.6);font-size:13px">Start the conversation with your Country Manager.</span>';
        rows.forEach(row => { const item = document.createElement('div'); item.style.cssText = 'max-width:82%;padding:9px 11px;border-radius:10px;line-height:1.4;font-size:13px;align-self:' + (row.sender_type === 'attendee' ? 'flex-end;background:#9d0f82' : 'flex-start;background:rgba(255,255,255,.12)'); item.textContent = row.message; messages.appendChild(item); });
        messages.scrollTop = messages.scrollHeight;
    }
    function loadChat() { if (communityId) request(endpoint(communityId)).then(data => renderChat(data.data || [])).catch(() => {}); }
    document.querySelectorAll('.rc-community-chat').forEach(button => button.addEventListener('click', function () { communityId = this.dataset.communityId; lastSignature = ''; chatName.textContent = 'Chat with ' + this.dataset.managerName; panel.hidden = false; loadChat(); clearInterval(poller); poller = setInterval(loadChat, 2500); }));
    document.querySelector('[data-community-chat-close]').addEventListener('click', () => { panel.hidden = true; clearInterval(poller); });
    chatForm.addEventListener('submit', function (event) { event.preventDefault(); const message = chatInput.value.trim(); if (!message || !communityId) return; request(endpoint(communityId), {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({message})}).then(data => { if (data.status === 'success') { chatInput.value = ''; lastSignature = ''; loadChat(); } }); });
    document.querySelectorAll('.rc-community-qr').forEach(button => button.addEventListener('click', function () { document.querySelector('[data-community-qr-country]').textContent = this.dataset.communityCountry + ' Community'; document.querySelector('[data-community-qr-image]').src = this.dataset.qrUrl; qrPanel.hidden = false; }));
    document.querySelector('[data-community-qr-close]').addEventListener('click', () => { qrPanel.hidden = true; });
});
</script>
</body></html>
