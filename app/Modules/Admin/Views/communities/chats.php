<?php
$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
$communities = $communities ?? [];
$threads = $threads ?? [];
?>

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
  <div><h1 class="text-2xl font-semibold text-gray-200">Community Manager Inbox</h1><p class="mt-1 text-sm text-gray-400">Reply to attendees who used the Chat with manager button in their country community.</p></div>
  <a href="<?php echo site_url('admin/communities'); ?>" class="rounded bg-gray-700 px-4 py-2 text-sm text-white hover:bg-gray-600">← Communities</a>
</div>

<?php if (! ($ready ?? false)): ?>
  <div class="rounded border border-amber-600 bg-amber-950/30 p-5 text-amber-100">Run the Communities migration before using the manager inbox.</div>
<?php elseif ($communities === []): ?>
  <div class="rounded border border-gray-700 bg-gray-800 p-6 text-gray-400">No community is assigned to this admin account yet.</div>
<?php else: ?>
  <div class="mb-4 flex flex-wrap gap-2">
    <?php foreach ($communities as $community): ?><a href="<?php echo site_url('admin/communities/chats?community=' . (int) $community['community_id']); ?>" class="rounded px-3 py-2 text-sm <?php echo (int) $selectedCommunityId === (int) $community['community_id'] ? 'bg-fuchsia-700 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'; ?>"><?php echo esc($community['country']); ?></a><?php endforeach; ?>
  </div>
  <div class="grid min-h-[560px] overflow-hidden rounded-lg border border-gray-700 bg-gray-800 lg:grid-cols-[320px_minmax(0,1fr)]">
    <aside class="border-b border-gray-700 lg:border-b-0 lg:border-r">
      <?php foreach ($threads as $thread): ?><a href="<?php echo site_url('admin/communities/chats?community=' . (int) $selectedCommunityId . '&attendee=' . (int) $thread['attendee_id']); ?>" class="block border-b border-gray-700 p-4 hover:bg-gray-700 <?php echo (int) $selectedAttendeeId === (int) $thread['attendee_id'] ? 'bg-gray-700' : ''; ?>"><div class="flex items-center justify-between gap-2"><strong class="truncate text-sm text-gray-100"><?php echo esc($thread['name']); ?></strong><?php if ($thread['unread']): ?><span class="rounded-full bg-fuchsia-700 px-2 py-0.5 text-xs text-white"><?php echo (int) $thread['unread']; ?></span><?php endif; ?></div><span class="mt-1 block truncate text-xs text-gray-400"><?php echo esc($thread['last_message']); ?></span><span class="mt-1 block text-xs text-gray-500"><?php echo esc($thread['email']); ?></span></a><?php endforeach; ?>
      <?php if ($threads === []): ?><p class="p-5 text-sm text-gray-400">No attendee messages yet.</p><?php endif; ?>
    </aside>
    <section class="flex min-h-[450px] flex-col">
      <?php if ($selectedAttendeeId): ?>
        <div class="border-b border-gray-700 p-4 text-sm font-medium text-gray-100">Conversation</div>
        <div data-community-chat-messages class="flex-1 space-y-3 overflow-y-auto p-4"></div>
        <form data-community-chat-form class="flex gap-2 border-t border-gray-700 p-3"><input data-community-chat-input maxlength="2000" placeholder="Write a reply…" class="min-w-0 flex-1 rounded border border-gray-700 bg-gray-900 p-2.5 text-sm text-gray-100"><button class="rounded bg-blue-600 px-4 text-sm text-white hover:bg-blue-500">Send</button></form>
      <?php else: ?><div class="m-auto p-8 text-center text-sm text-gray-400">Select an attendee conversation to reply.</div><?php endif; ?>
    </section>
  </div>
<?php endif; ?>

<?php if (($ready ?? false) && $selectedCommunityId && $selectedAttendeeId): ?>
<script>
(() => {
  const endpoint = <?php echo json_encode(site_url('admin/communities/' . (int) $selectedCommunityId . '/chats/' . (int) $selectedAttendeeId)); ?>;
  const csrf = <?php echo json_encode(csrf_hash()); ?>;
  const box = document.querySelector('[data-community-chat-messages]');
  const form = document.querySelector('[data-community-chat-form]');
  const input = document.querySelector('[data-community-chat-input]');
  let signature = '';
  function render(rows) {
    const next = JSON.stringify(rows);
    if (next === signature) return;
    signature = next; box.innerHTML = '';
    if (!rows.length) box.innerHTML = '<p class="m-auto text-sm text-gray-400">Start the conversation.</p>';
    rows.forEach(row => { const node = document.createElement('div'); node.className = 'max-w-[80%] rounded px-3 py-2 text-sm ' + (row.sender_type === 'admin' ? 'ml-auto bg-blue-700 text-white' : 'bg-gray-700 text-gray-100'); node.textContent = row.message; box.appendChild(node); });
    box.scrollTop = box.scrollHeight;
  }
  function load() { fetch(endpoint, {headers: {'X-CSRF-TOKEN': csrf}}).then(r => r.json()).then(data => render(data.data || [])).catch(() => {}); }
  form.addEventListener('submit', event => { event.preventDefault(); const message = input.value.trim(); if (!message) return; fetch(endpoint, {method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':csrf},body:new URLSearchParams({message})}).then(r => r.json()).then(() => { input.value = ''; signature = ''; load(); }); });
  load(); window.setInterval(load, 2500);
})();
</script>
<?php endif; ?>
<?php $this->endSection(); ?>
