<?php
echo module_view('Web', 'includes/header');
echo module_view('Web', 'includes/topbar');

$members = $members ?? [];
$programmes = $programmes ?? [];
$resources = $resources ?? [];
$videoUrl = $videoUrl ?? '';
$topics = ['Programmes', 'Partnership', 'Speaking', 'Media'];
?>

<main class="rc-page rc-page-center" data-screen-label="Emergence Booth">
  <div class="rc-container" style="max-width:1120px;gap:28px">
    <div class="rc-title-stack">
      <span class="rc-kicker">EPR Global · Host Booth</span>
      <span class="rc-page-title">Emergence Booth</span>
      <span class="rc-page-sub">Meet the team, explore programmes and resources, or start a conversation.</span>
    </div>

    <?php if (!($ready ?? false)): ?>
      <div class="rc-panel" style="padding:28px;text-align:center">
        <strong>Emergence Booth is being prepared.</strong>
        <p style="margin:10px 0 0;color:rgba(255,255,255,.7)">Please check back shortly.</p>
      </div>
    <?php else: ?>
      <section class="rc-em-top-grid" style="display:grid;grid-template-columns:minmax(0,1.45fr) minmax(280px,1fr);gap:24px">
        <div class="rc-emergence-video" data-booth-video data-video-src="<?php echo esc($videoUrl, 'attr'); ?>">
          <img src="<?php echo asset_url('images/accelerate-logo-white.png'); ?>" alt="" style="width:46%;height:auto;opacity:.22">
          <?php if ($videoUrl !== ''): ?>
            <button type="button" class="rc-em-play" aria-label="Play welcome film" style="position:absolute;width:76px;height:76px;border:0;border-radius:50%;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;box-shadow:0 0 0 10px rgba(216,25,142,.16),0 0 40px rgba(216,25,142,.55)"><i class="fa fa-play" aria-hidden="true"></i></button>
          <?php endif; ?>
          <span style="position:absolute;top:16px;left:16px;display:flex;align-items:center;gap:8px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.16em;padding:6px 10px;border-radius:999px;background:rgba(18,5,15,.78)"><i class="fa fa-film" aria-hidden="true"></i>WELCOME FILM</span>
          <div style="position:absolute;left:0;right:0;bottom:0;padding:16px 20px;background:linear-gradient(0deg,rgba(10,2,12,.85),rgba(10,2,12,0))"><span style="font-size:14px;font-weight:600">Why we build systems, not hype</span></div>
        </div>

        <aside class="rc-panel" style="padding:22px;display:flex;flex-direction:column;gap:16px">
          <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">AT THE BOOTH</span>
          <?php if ($members): ?>
            <?php foreach ($members as $member): ?>
              <?php $initials = implode('', array_map(static fn($part) => strtoupper(substr($part, 0, 1)), explode(' ', (string) $member['display_name']))); ?>
              <div style="display:flex;align-items:center;gap:12px">
                <span style="position:relative;flex:none;width:46px;height:46px;border-radius:50%;background:#9d0f82;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:15px"><?php echo esc($initials); ?><span style="position:absolute;right:0;bottom:0;width:11px;height:11px;border-radius:50%;background:<?php echo !empty($member['is_online']) ? '#4ade80' : '#777'; ?>;box-shadow:0 0 0 2px #1e0a22"></span></span>
                <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:2px"><span style="font-size:14px;font-weight:600"><?php echo esc($member['display_name']); ?></span><span style="font-size:12px;color:rgba(255,255,255,.7)"><?php echo esc($member['title'] ?: 'EPR Global Team'); ?> · <?php echo !empty($member['is_online']) ? 'Online' : 'Offline'; ?></span></div>
                <button class="rc-card-action rc-booth-chat" type="button" data-member-id="<?php echo (int) $member['booth_member_id']; ?>" data-member-name="<?php echo esc($member['display_name'], 'attr'); ?>" style="flex:none;width:auto;font-size:12px"><i class="fa fa-comment" aria-hidden="true"></i><span><?php echo !empty($member['is_online']) ? 'Chat now' : 'Message'; ?></span></button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p style="margin:0;color:rgba(255,255,255,.7);font-size:13px">Our team will be online during booth hours.</p>
          <?php endif; ?>
        </aside>
      </section>

      <section style="display:flex;flex-direction:column;gap:14px">
        <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">PROGRAMMES</span>
        <div class="rc-em-programmes" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px">
          <?php if ($programmes): ?>
            <?php foreach ($programmes as $programme): ?>
              <article class="rc-emergence-card"><span style="width:44px;height:44px;border-radius:12px;background:linear-gradient(90deg,#7d0686,#bd159e 50%,#7c0585);display:flex;align-items:center;justify-content:center;font-size:18px"><i class="fa <?php echo esc($programme['icon']); ?>" aria-hidden="true"></i></span><span class="rc-mono" style="font-size:10px;letter-spacing:.16em;color:#efb11e"><?php echo esc($programme['label']); ?></span><h3 style="margin:0;font-size:18px;font-weight:600"><?php echo esc($programme['title']); ?></h3><p style="margin:0;color:rgba(255,255,255,.72);font-size:13px;line-height:1.55"><?php echo esc($programme['description']); ?></p></article>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="rc-panel" style="grid-column:1/-1;padding:20px;color:rgba(255,255,255,.7)">Programmes will appear here soon.</div>
          <?php endif; ?>
        </div>
      </section>

      <section class="rc-em-two-col" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:24px">
        <div class="rc-panel" style="padding:22px;display:flex;flex-direction:column;gap:8px">
          <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6);margin-bottom:10px">TAKE-HOME TOOLKIT</span>
          <?php if ($resources): ?>
            <?php foreach ($resources as $resource): ?>
              <a class="rc-resource-row" href="<?php echo base_url(ltrim($resource['file_path'], '/')); ?>" download="<?php echo esc($resource['file_name'], 'attr'); ?>" style="text-decoration:none;color:inherit"><span style="flex:none;width:40px;height:40px;border-radius:10px;background:rgba(239,177,30,.14);color:#efb11e;display:flex;align-items:center;justify-content:center;font-size:17px"><i class="fa fa-download" aria-hidden="true"></i></span><span style="flex:1;min-width:0;display:flex;flex-direction:column;gap:2px"><span style="font-size:14px;font-weight:500"><?php echo esc($resource['title']); ?></span><span class="rc-mono" style="font-size:10px;letter-spacing:.1em;color:rgba(255,255,255,.55)"><?php echo esc($resource['meta']); ?></span></span><i class="fa fa-download" aria-hidden="true"></i></a>
            <?php endforeach; ?>
          <?php else: ?>
            <span style="font-size:13px;color:rgba(255,255,255,.7)">Resources will be added soon.</span>
          <?php endif; ?>
        </div>

        <div class="rc-panel" style="padding:22px;display:flex;flex-direction:column;gap:14px">
          <span class="rc-mono" style="font-size:11px;letter-spacing:.18em;color:rgba(255,255,255,.6)">REQUEST A CALLBACK</span>
          <form data-callback-form class="rc-em-form"><span style="font-size:13px;color:rgba(255,255,255,.75);line-height:1.5">Tell us what you would like to discuss. An EPR team member will follow up.</span><div style="display:flex;flex-wrap:wrap;gap:8px"><?php foreach ($topics as $index => $topic): ?><button type="button" class="rc-outline-pill rc-booth-topic" data-topic="<?php echo esc($topic, 'attr'); ?>" style="background:<?php echo $index === 0 ? 'rgba(157,15,130,.55)' : 'rgba(255,255,255,.10)'; ?>;border-color:<?php echo $index === 0 ? '#d8198e' : 'rgba(255,255,255,.3)'; ?>;color:#fff"><?php echo esc($topic); ?></button><?php endforeach; ?></div><textarea data-callback-note class="rc-input" rows="4" placeholder="Add a short note (optional)" style="height:auto;padding:12px 14px!important;resize:none"></textarea><button type="submit" class="rc-btn" style="height:46px;text-transform:none;letter-spacing:0;font-size:14px"><i class="fa fa-phone" aria-hidden="true"></i> Request a callback</button><span data-callback-status style="font-size:13px"></span></form>
        </div>
      </section>
    <?php endif; ?>
  </div>
</main>

<aside data-booth-chat-panel hidden class="rc-panel" style="position:fixed;z-index:40;right:24px;bottom:24px;width:min(380px,calc(100vw - 32px));height:min(540px,calc(100vh - 48px));padding:0;display:flex;flex-direction:column;overflow:hidden;background:#201024">
  <header style="padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.12);display:flex;justify-content:space-between;align-items:center"><strong data-booth-chat-name>Chat with EPR</strong><button type="button" data-booth-chat-close aria-label="Close chat" style="border:0;background:transparent;color:#fff;font-size:22px;cursor:pointer">×</button></header>
  <div data-booth-chat-messages style="flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px"></div>
  <form data-booth-chat-form style="padding:12px;border-top:1px solid rgba(255,255,255,.12);display:flex;gap:8px"><input data-booth-chat-input class="rc-input" maxlength="2000" placeholder="Type a message…" style="flex:1;min-width:0"><button class="rc-btn" type="submit" style="height:42px;padding:0 14px">Send</button></form>
</aside>

</div></div>
<?php echo module_view('Web', 'includes/scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const csrf = <?php echo json_encode(csrf_hash()); ?>;
  const callbackUrl = <?php echo json_encode(site_url('attendees/emergence_booth/callback')); ?>;
  const panel = document.querySelector('[data-booth-chat-panel]');
  const messages = document.querySelector('[data-booth-chat-messages]');
  const chatName = document.querySelector('[data-booth-chat-name]');
  const chatForm = document.querySelector('[data-booth-chat-form]');
  const chatInput = document.querySelector('[data-booth-chat-input]');
  let memberId = null, poller = null, lastSignature = '';
  const request = (url, options = {}) => fetch(url, {...options, headers: {...(options.headers || {}), 'X-CSRF-TOKEN': csrf}}).then(r => r.json());
  function renderChat(rows) {
    const signature = JSON.stringify(rows);
    if (signature === lastSignature) return;
    lastSignature = signature; messages.innerHTML = '';
    if (!rows.length) messages.innerHTML = '<span style="margin:auto;color:rgba(255,255,255,.6);font-size:13px">Start the conversation.</span>';
    rows.forEach(row => { const item = document.createElement('div'); item.style.cssText = 'max-width:82%;padding:9px 11px;border-radius:10px;line-height:1.4;font-size:13px;align-self:' + (row.sender_type === 'attendee' ? 'flex-end;background:#9d0f82' : 'flex-start;background:rgba(255,255,255,.12)'); item.textContent = row.message; messages.appendChild(item); });
    messages.scrollTop = messages.scrollHeight;
  }
  function loadChat() { if (memberId) request(<?php echo json_encode(site_url('attendees/emergence_booth/chat/')); ?> + memberId).then(data => renderChat(data.data || [])).catch(() => {}); }
  document.querySelectorAll('.rc-booth-chat').forEach(button => button.addEventListener('click', function () { memberId = this.dataset.memberId; lastSignature = ''; chatName.textContent = 'Chat with ' + this.dataset.memberName; panel.hidden = false; loadChat(); clearInterval(poller); poller = setInterval(loadChat, 2000); }));
  document.querySelector('[data-booth-chat-close]').addEventListener('click', () => { panel.hidden = true; clearInterval(poller); });
  chatForm.addEventListener('submit', function (event) { event.preventDefault(); const message = chatInput.value.trim(); if (!message || !memberId) return; request(<?php echo json_encode(site_url('attendees/emergence_booth/chat/')); ?> + memberId, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({message})}).then(() => { chatInput.value=''; lastSignature=''; loadChat(); }); });
  document.querySelectorAll('.rc-booth-topic').forEach(button => button.addEventListener('click', function () { document.querySelectorAll('.rc-booth-topic').forEach(item => { item.style.background='rgba(255,255,255,.10)'; item.style.borderColor='rgba(255,255,255,.3)'; }); this.style.background='rgba(157,15,130,.55)'; this.style.borderColor='#d8198e'; this.classList.add('is-active'); document.querySelectorAll('.rc-booth-topic:not([data-topic="' + CSS.escape(this.dataset.topic) + '"])').forEach(item => item.classList.remove('is-active')); }));
  document.querySelector('[data-callback-form]')?.addEventListener('submit', function (event) { event.preventDefault(); const active = document.querySelector('.rc-booth-topic.is-active') || document.querySelector('.rc-booth-topic'); const note = document.querySelector('[data-callback-note]').value; const status = document.querySelector('[data-callback-status]'); request(callbackUrl, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:new URLSearchParams({topic:active.dataset.topic,note})}).then(data => { status.textContent = data.status === 'success' ? 'Request received. We will be in touch.' : (data.message || 'Unable to send your request.'); status.style.color = data.status === 'success' ? '#4ade80' : '#f87171'; if (data.status === 'success') document.querySelector('[data-callback-note]').value=''; }).catch(() => { status.textContent='Unable to send your request.'; status.style.color='#f87171'; }); });
  document.querySelector('.rc-em-play')?.addEventListener('click', function () { const holder = document.querySelector('[data-booth-video]'), source = holder.dataset.videoSrc; if (!source || holder.querySelector('iframe')) return; const iframe=document.createElement('iframe'); iframe.src=source + (source.includes('?')?'&':'?')+'autoplay=true'; iframe.allow='autoplay; fullscreen; picture-in-picture'; iframe.allowFullscreen=true; iframe.style.cssText='position:absolute;inset:0;width:100%;height:100%;border:0;z-index:3'; holder.appendChild(iframe); this.remove(); });
});
</script>
</body></html>
