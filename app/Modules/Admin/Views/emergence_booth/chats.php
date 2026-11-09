<?php
$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>
<div class="mb-6 flex items-center justify-between"><div><h1 class="text-2xl font-semibold text-gray-100">Emergence Booth Chats</h1><p class="mt-1 text-sm text-gray-400">Messages update automatically while this page is open.</p></div><a href="<?php echo site_url('admin/emergence-booth'); ?>" class="text-sm text-blue-400">← Booth management</a></div>

<?php if (!($ready ?? false)): ?>
  <div class="rounded border border-amber-600 bg-gray-800 p-4 text-amber-200">The booth migration must be run before chats can be used.</div>
<?php elseif (!$member): ?>
  <div class="rounded border border-gray-700 bg-gray-800 p-5 text-gray-300">You are not currently an active booth team member. A super admin must add your admin account to the booth team before you can reply to attendees.</div>
<?php else: ?>
  <div class="grid min-h-[560px] grid-cols-1 overflow-hidden rounded-lg border border-gray-700 bg-gray-800 lg:grid-cols-[320px_minmax(0,1fr)]">
    <aside class="border-b border-gray-700 lg:border-b-0 lg:border-r"><div class="border-b border-gray-700 p-4"><strong class="text-gray-100">Your conversations</strong></div><div class="divide-y divide-gray-700">
      <?php if (!$threads): ?><p class="p-4 text-sm text-gray-400">No attendee messages yet.</p><?php endif; ?>
      <?php foreach ($threads as $thread): ?><a href="<?php echo site_url('admin/emergence-booth/chats?attendee=' . (int) $thread['attendee_id']); ?>" class="block p-4 hover:bg-gray-700 <?php echo $selectedAttendeeId === (int) $thread['attendee_id'] ? 'bg-gray-700' : ''; ?>"><div class="flex items-center justify-between gap-2"><strong class="truncate text-sm text-gray-200"><?php echo esc($thread['name'] ?? 'Attendee'); ?></strong><?php if ($thread['unread']): ?><span class="rounded-full bg-fuchsia-700 px-2 py-0.5 text-xs text-white"><?php echo (int) $thread['unread']; ?></span><?php endif; ?></div><span class="block truncate text-xs text-gray-400"><?php echo esc($thread['last_message']); ?></span><span class="mt-1 block text-xs text-gray-500"><?php echo esc($thread['email'] ?? ''); ?></span></a><?php endforeach; ?>
    </div></aside>
    <section class="flex min-h-[400px] flex-col">
      <?php if (!$selectedAttendeeId): ?><div class="m-auto text-sm text-gray-400">Choose a conversation to reply.</div><?php else: ?>
        <?php $thread = $threads[$selectedAttendeeId]; ?><header class="border-b border-gray-700 p-4"><strong class="text-gray-100"><?php echo esc($thread['name'] ?? 'Attendee'); ?></strong><span class="ml-2 text-sm text-gray-400"><?php echo esc($thread['email'] ?? ''); ?></span></header>
        <div id="boothAdminMessages" class="flex-1 space-y-3 overflow-y-auto p-4"></div>
        <form id="boothAdminChatForm" class="flex gap-2 border-t border-gray-700 p-4"><input id="boothAdminChatInput" maxlength="2000" class="min-w-0 flex-1 rounded border border-gray-600 bg-gray-900 p-2 text-sm text-gray-100" placeholder="Type a reply…"><button class="rounded bg-blue-600 px-4 py-2 text-sm text-white">Send</button></form>
      <?php endif; ?>
    </section>
  </div>
<?php endif; ?>

<?php if (($ready ?? false) && $member && $selectedAttendeeId): ?>
<script>
(() => {
  const endpoint = <?php echo json_encode(site_url('admin/emergence-booth/chats/' . $selectedAttendeeId)); ?>;
  const csrf = <?php echo json_encode(csrf_hash()); ?>;
  const box = document.getElementById('boothAdminMessages'), form = document.getElementById('boothAdminChatForm'), input = document.getElementById('boothAdminChatInput');
  let signature = '';
  function render(rows) { const next = JSON.stringify(rows); if (next === signature) return; signature = next; box.innerHTML = ''; rows.forEach(row => { const node = document.createElement('div'); node.className = 'max-w-[80%] rounded px-3 py-2 text-sm ' + (row.sender_type === 'admin' ? 'ml-auto bg-blue-700 text-white' : 'bg-gray-700 text-gray-100'); node.textContent = row.message; box.appendChild(node); }); box.scrollTop = box.scrollHeight; }
  function load() { fetch(endpoint, {headers:{'X-CSRF-TOKEN':csrf}}).then(r=>r.json()).then(data=>render(data.data || [])).catch(()=>{}); }
  form.addEventListener('submit', event => { event.preventDefault(); const message=input.value.trim(); if (!message) return; fetch(endpoint,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':csrf},body:new URLSearchParams({message})}).then(r=>r.json()).then(()=>{input.value='';signature='';load();}); });
  load(); window.setInterval(load, 2000);
})();
</script>
<?php endif; ?>
<?php $this->endSection(); ?>
