<?php
$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
  <div><h1 class="text-2xl font-semibold text-gray-100">Emergence Booth</h1><p class="mt-1 text-sm text-gray-400">Manage the booth team, video, programmes, toolkit, callbacks, and conversations.</p></div>
  <?php if ($ready ?? false): ?><a href="<?php echo site_url('admin/emergence-booth/chats'); ?>" class="rounded bg-fuchsia-700 px-4 py-2 text-sm font-medium text-white hover:bg-fuchsia-600"><i class="bx bx-chat"></i> Booth chats</a><?php endif; ?>
</div>

<?php if ($error = session()->getFlashdata('error')): ?>
  <section class="mb-4 flex gap-3 rounded-lg border border-red-500/70 bg-red-950/35 p-4 text-red-100" role="alert">
    <span class="flex h-7 w-7 flex-none items-center justify-center rounded-full bg-red-500/20 font-bold text-red-300" aria-hidden="true">!</span>
    <div><h2 class="font-semibold">We couldn’t complete that upload or update.</h2><p class="mt-1 text-sm leading-6 text-red-100/90"><?php echo esc($error); ?></p><p class="mt-2 text-xs text-red-200/75">No playlist changes were saved. Review the message above, then try again.</p></div>
  </section>
<?php endif; ?>
<?php if ($success = session()->getFlashdata('success')): ?><div class="mb-4 rounded border border-green-700 bg-gray-800 p-3 text-green-300"><?php echo esc($success); ?></div><?php endif; ?>

<?php if (!($ready ?? false)): ?>
  <div class="rounded-lg border border-amber-600 bg-gray-800 p-6 text-amber-200"><strong>One database migration is needed before the booth can be configured.</strong><p class="mt-2 text-sm text-gray-300">After this code is deployed, run <code class="rounded bg-gray-900 px-1.5 py-0.5">php spark migrate</code> manually when you are ready. Nothing has been run automatically.</p></div>
<?php else: ?>
  <div class="grid gap-6 xl:grid-cols-2">
    <section class="rounded-lg border border-gray-700 bg-gray-800 p-5">
      <h2 class="font-semibold text-gray-100">EPR Films playlist</h2><p class="mt-1 text-sm text-gray-400">Upload up to six Bunny Stream videos. They play on the attendee booth in the order listed below, then continuously loop back to the first video.</p>
      <?php $availableVideoSlots = max(0, 6 - count($videoPlaylist ?? [])); ?>
      <?php if ($availableVideoSlots > 0): ?><form id="booth-video-upload-form" method="post" action="<?php echo site_url('admin/emergence-booth/video'); ?>" data-bunny-credentials-url="<?php echo site_url('admin/emergence-booth/video/credentials'); ?>" data-bunny-save-url="<?php echo site_url('admin/emergence-booth/video/direct'); ?>" enctype="multipart/form-data" class="mt-4 space-y-3"><?php echo csrf_field(); ?><label class="block text-sm text-gray-300">Choose up to <?php echo $availableVideoSlots; ?> video<?php echo $availableVideoSlots === 1 ? '' : 's'; ?><input type="file" name="booth_videos[]" accept="video/mp4,video/webm,video/quicktime,.mp4,.m4v,.mov,.webm" multiple required data-max-videos="<?php echo $availableVideoSlots; ?>" data-max-video-bytes="536870912" class="mt-1 block w-full rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-300"></label><p class="text-xs text-gray-500">Each video can be up to 512 MB. Videos upload directly to Bunny Stream, so they do not pass through this server. They will play in the order selected.</p><div id="booth-video-upload-error" class="hidden rounded-lg border border-red-500/70 bg-red-950/35 p-3 text-sm text-red-100" role="alert"><strong class="block font-semibold">Upload could not be completed.</strong><span id="booth-video-upload-error-message" class="mt-1 block leading-5"></span></div><div id="booth-video-upload-progress" class="hidden" aria-live="polite"><div class="mb-1 flex justify-between text-xs text-gray-300"><span id="booth-video-upload-status">Uploading videos…</span><span id="booth-video-upload-percent">0%</span></div><div class="h-2 overflow-hidden rounded-full bg-gray-700"><div id="booth-video-upload-fill" class="h-full w-0 rounded-full bg-blue-500 transition-all duration-150" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div></div></div><button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-500">Add selected videos to Bunny playlist</button></form><?php else: ?><p class="mt-4 text-sm text-amber-300">The playlist is full. Remove a video below before adding another.</p><?php endif; ?>
      <ol class="mt-4 space-y-2 text-sm text-gray-300"><?php foreach (($videoPlaylist ?? []) as $index => $url): ?><li class="flex flex-wrap items-center gap-2 rounded border border-gray-700 bg-gray-900 p-2"><span class="text-gray-500">#<?php echo (int) $index + 1; ?></span><a class="min-w-0 flex-1 truncate text-blue-400 hover:underline" target="_blank" rel="noopener" href="<?php echo esc($url); ?>">View Bunny video</a><form method="post" action="<?php echo site_url('admin/emergence-booth/video/' . $index . '/move'); ?>" class="flex gap-1"><?php echo csrf_field(); ?><button class="rounded border border-gray-600 px-2 py-1 text-xs text-gray-200 hover:border-blue-400 disabled:cursor-not-allowed disabled:opacity-40" type="submit" name="direction" value="up" <?php echo $index === 0 ? 'disabled' : ''; ?> aria-label="Move video <?php echo (int) $index + 1; ?> up">↑</button><button class="rounded border border-gray-600 px-2 py-1 text-xs text-gray-200 hover:border-blue-400 disabled:cursor-not-allowed disabled:opacity-40" type="submit" name="direction" value="down" <?php echo $index === count($videoPlaylist) - 1 ? 'disabled' : ''; ?> aria-label="Move video <?php echo (int) $index + 1; ?> down">↓</button></form><form method="post" action="<?php echo site_url('admin/emergence-booth/video/' . $index . '/delete'); ?>"><?php echo csrf_field(); ?><button class="text-xs text-red-400 hover:text-red-300" type="submit">Remove</button></form></li><?php endforeach; ?><?php if (empty($videoPlaylist)): ?><li class="text-gray-500">No videos queued yet.</li><?php endif; ?></ol>
    </section>

    <section class="rounded-lg border border-gray-700 bg-gray-800 p-5">
      <h2 class="font-semibold text-gray-100">Add booth team member</h2><p class="mt-1 text-sm text-gray-400">A member is linked to an active admin account. Their green status dot means they are currently active in Admin.</p>
      <form method="post" action="<?php echo site_url('admin/emergence-booth/members'); ?>" class="mt-4 grid gap-3 md:grid-cols-2"><?php echo csrf_field(); ?><select name="admin_id" required class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><option value="">Choose admin account</option><?php foreach (($admins ?? []) as $admin): ?><option value="<?php echo (int) $admin['id']; ?>"><?php echo esc($admin['name']); ?> · <?php echo esc($admin['email']); ?></option><?php endforeach; ?></select><input name="display_name" placeholder="Display name (optional)" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><input name="title" placeholder="Role, e.g. Partnerships" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><input name="sort_order" type="number" value="0" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><label class="flex items-center gap-2 text-sm text-gray-300"><input name="is_active" type="checkbox" value="1" checked> Show at booth</label><button class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-500">Save member</button></form>
    </section>
  </div>

  <section class="mt-6 rounded-lg border border-gray-700 bg-gray-800 p-5"><h2 class="font-semibold text-gray-100">Booth team</h2><div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3"><?php foreach (($members ?? []) as $member): ?><form method="post" action="<?php echo site_url('admin/emergence-booth/members'); ?>" class="rounded border border-gray-700 bg-gray-900 p-3 space-y-2"><?php echo csrf_field(); ?><input type="hidden" name="admin_id" value="<?php echo (int) $member['admin_id']; ?>"><div class="flex items-center justify-between"><strong><?php echo esc($member['display_name']); ?></strong><span class="text-xs <?php echo !empty($member['is_online']) ? 'text-green-400' : 'text-gray-500'; ?>">● <?php echo !empty($member['is_online']) ? 'Online' : 'Offline'; ?></span></div><input name="display_name" value="<?php echo esc($member['display_name']); ?>" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><input name="title" value="<?php echo esc($member['title']); ?>" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><div class="flex gap-2"><input name="sort_order" type="number" value="<?php echo (int) $member['sort_order']; ?>" class="w-20 rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><label class="flex items-center gap-1 text-xs text-gray-300"><input name="is_active" type="checkbox" value="1" <?php echo $member['is_active'] ? 'checked' : ''; ?>> Show</label><button class="ml-auto text-sm text-blue-400">Save</button></div></form><?php endforeach; ?></div></section>

  <div class="mt-6 grid gap-6 xl:grid-cols-2">
    <section class="rounded-lg border border-gray-700 bg-gray-800 p-5"><h2 class="font-semibold text-gray-100">Programmes</h2><p class="mt-1 text-sm text-gray-400">Add the programme card, upload its flyer, and choose whether attendees can join its waitlist.</p><form method="post" enctype="multipart/form-data" action="<?php echo site_url('admin/emergence-booth/programmes'); ?>" class="mt-4 grid gap-3"><?php echo csrf_field(); ?><input name="title" required placeholder="Programme title" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><input name="label" placeholder="Label, e.g. 12 WEEKS · COHORT" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><textarea name="description" required placeholder="Description" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"></textarea><label class="text-sm text-gray-300">Programme flyer <input name="flyer" type="file" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="mt-1 block w-full rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-300"></label><div class="grid grid-cols-2 gap-2"><input name="icon" value="fa-rocket" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><input name="sort_order" type="number" value="0" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><label class="flex items-center gap-1 text-sm text-gray-300"><input name="is_active" type="checkbox" value="1" checked> Show</label><label class="flex items-center gap-1 text-sm text-gray-300"><input name="waitlist_enabled" type="checkbox" value="1" checked> Enable waitlist</label></div><button class="justify-self-start rounded bg-blue-600 px-4 py-2 text-sm text-white">Add programme</button></form><div class="mt-5 space-y-3"><?php foreach (($programmes ?? []) as $programme): ?><details class="rounded border border-gray-700 bg-gray-900 p-3"><summary class="cursor-pointer text-sm text-gray-200"><?php echo esc($programme['title']); ?> <?php echo $programme['is_active'] ? '' : '(Hidden)'; ?> · <?php echo (int) ($programme['waitlist_count'] ?? 0); ?> waiting</summary><form method="post" enctype="multipart/form-data" action="<?php echo site_url('admin/emergence-booth/programmes/' . $programme['programme_id']); ?>" class="mt-3 space-y-2"><?php echo csrf_field(); ?><input name="title" value="<?php echo esc($programme['title']); ?>" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><input name="label" value="<?php echo esc($programme['label']); ?>" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><textarea name="description" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><?php echo esc($programme['description']); ?></textarea><?php if (! empty($programme['flyer_path'])): ?><img src="<?php echo base_url(ltrim($programme['flyer_path'], '/')); ?>" alt="" class="max-h-40 rounded border border-gray-700"><?php endif; ?><label class="block text-xs text-gray-300">Replace flyer <input name="flyer" type="file" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" class="mt-1 block w-full text-sm text-gray-300"></label><div class="flex flex-wrap gap-2"><input name="icon" value="<?php echo esc($programme['icon']); ?>" class="w-32 rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><input name="sort_order" type="number" value="<?php echo (int) $programme['sort_order']; ?>" class="w-20 rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><label class="text-xs text-gray-300"><input name="is_active" type="checkbox" value="1" <?php echo $programme['is_active'] ? 'checked' : ''; ?>> Show</label><label class="text-xs text-gray-300"><input name="waitlist_enabled" type="checkbox" value="1" <?php echo !empty($programme['waitlist_enabled']) ? 'checked' : ''; ?>> Waitlist</label><button class="ml-auto text-sm text-blue-400">Save</button></div></form><form method="post" action="<?php echo site_url('admin/emergence-booth/programmes/' . $programme['programme_id'] . '/delete'); ?>" class="mt-2"><?php echo csrf_field(); ?><button onclick="return confirm('Delete this programme and its waitlist?')" class="text-xs text-red-400">Delete</button></form></details><?php endforeach; ?></div></section>

    <section class="rounded-lg border border-gray-700 bg-gray-800 p-5"><h2 class="font-semibold text-gray-100">Take-Home Toolkit</h2><form method="post" enctype="multipart/form-data" action="<?php echo site_url('admin/emergence-booth/resources'); ?>" class="mt-4 grid gap-3"><?php echo csrf_field(); ?><input name="title" required placeholder="Resource title" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><input name="meta" placeholder="Meta, e.g. PDF · 3 MB" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><input name="resource_file" type="file" required accept=".pdf,.xlsx,.xls,.docx,.pptx,.zip" class="rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><div class="flex gap-2"><input name="sort_order" type="number" value="0" class="w-20 rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><label class="text-sm text-gray-300"><input name="is_active" type="checkbox" value="1" checked> Show</label><button class="ml-auto rounded bg-blue-600 px-4 py-2 text-sm text-white">Upload resource</button></div></form><div class="mt-5 space-y-3"><?php foreach (($resources ?? []) as $resource): ?><details class="rounded border border-gray-700 bg-gray-900 p-3"><summary class="cursor-pointer text-sm text-gray-200"><?php echo esc($resource['title']); ?> <?php echo $resource['is_active'] ? '' : '(Hidden)'; ?></summary><form method="post" enctype="multipart/form-data" action="<?php echo site_url('admin/emergence-booth/resources/' . $resource['resource_id']); ?>" class="mt-3 space-y-2"><?php echo csrf_field(); ?><input name="title" value="<?php echo esc($resource['title']); ?>" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><input name="meta" value="<?php echo esc($resource['meta']); ?>" class="w-full rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><input name="resource_file" type="file" accept=".pdf,.xlsx,.xls,.docx,.pptx,.zip" class="w-full text-sm text-gray-300"><div class="flex gap-2"><input name="sort_order" type="number" value="<?php echo (int) $resource['sort_order']; ?>" class="w-20 rounded border border-gray-700 bg-gray-800 p-2 text-sm text-gray-200"><label class="text-xs text-gray-300"><input name="is_active" type="checkbox" value="1" <?php echo $resource['is_active'] ? 'checked' : ''; ?>> Show</label><button class="ml-auto text-sm text-blue-400">Save</button></div></form><form method="post" action="<?php echo site_url('admin/emergence-booth/resources/' . $resource['resource_id'] . '/delete'); ?>" class="mt-2"><?php echo csrf_field(); ?><button onclick="return confirm('Delete this toolkit file?')" class="text-xs text-red-400">Delete</button></form></details><?php endforeach; ?></div></section>
  </div>

  <section class="mt-6 rounded-lg border border-gray-700 bg-gray-800 p-5"><h2 class="font-semibold text-gray-100">Programme waitlists</h2><p class="mt-1 text-sm text-gray-400">Attendees who asked to be notified about a programme.</p><div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm text-gray-300"><thead class="text-xs uppercase text-gray-500"><tr><th class="p-2">Programme</th><th class="p-2">Attendee</th><th class="p-2">Country</th><th class="p-2">Joined</th></tr></thead><tbody><?php foreach (($programmeWaitlists ?? []) as $waitlist): ?><tr class="border-t border-gray-700"><td class="p-2 font-medium text-gray-200"><?php echo esc($waitlist['programme_title'] ?? 'Programme'); ?></td><td class="p-2"><?php echo esc(trim(($waitlist['firstname'] ?? '') . ' ' . ($waitlist['lastname'] ?? '')) ?: 'Attendee'); ?><br><span class="text-xs text-gray-500"><?php echo esc($waitlist['email'] ?? ''); ?><br><?php echo esc($waitlist['telephone'] ?? ''); ?></span></td><td class="p-2"><?php echo esc($waitlist['country'] ?? ''); ?></td><td class="p-2 text-xs"><?php echo esc($waitlist['created_at']); ?></td></tr><?php endforeach; ?><?php if (empty($programmeWaitlists)): ?><tr class="border-t border-gray-700"><td colspan="4" class="p-3 text-gray-500">No waitlist signups yet.</td></tr><?php endif; ?></tbody></table></div></section>

  <section class="mt-6 rounded-lg border border-gray-700 bg-gray-800 p-5">
    <h2 class="font-semibold text-gray-100">Callback topics</h2>
    <p class="mt-1 text-sm text-gray-400">These are the options attendees can choose when they request a callback.</p>
    <form method="post" action="<?php echo site_url('admin/emergence-booth/callback-topics'); ?>" class="mt-4 space-y-3" data-callback-topics><?php echo csrf_field(); ?>
      <div class="grid gap-2 sm:grid-cols-2" data-callback-topic-list>
        <?php foreach (($callbackTopics ?? []) as $topic): ?>
          <div class="flex gap-2" data-callback-topic-row><input name="topics[]" value="<?php echo esc($topic); ?>" maxlength="100" required class="min-w-0 flex-1 rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200"><button type="button" data-remove-callback-topic class="rounded border border-gray-600 px-3 text-sm text-gray-300 hover:border-red-500 hover:text-red-300">Remove</button></div>
        <?php endforeach; ?>
      </div>
      <div class="flex flex-wrap gap-3"><button type="button" data-add-callback-topic class="rounded border border-gray-600 px-4 py-2 text-sm text-gray-200 hover:border-blue-400">Add topic</button><button class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-500">Save callback topics</button></div>
    </form>
  </section>

  <section class="mt-6 rounded-lg border border-gray-700 bg-gray-800 p-5"><h2 class="font-semibold text-gray-100">Callback requests</h2><div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm text-gray-300"><thead class="text-xs uppercase text-gray-500"><tr><th class="p-2">Attendee</th><th class="p-2">Topic / note</th><th class="p-2">Received</th><th class="p-2">Status</th></tr></thead><tbody><?php foreach (($callbacks ?? []) as $callback): ?><tr class="border-t border-gray-700"><td class="p-2"><?php echo esc(trim(($callback['firstname'] ?? '') . ' ' . ($callback['lastname'] ?? '')) ?: 'Attendee'); ?><br><span class="text-xs text-gray-500"><?php echo esc($callback['email'] ?? ''); ?><br><?php echo esc($callback['telephone'] ?? ''); ?></span></td><td class="p-2"><strong><?php echo esc($callback['topic']); ?></strong><br><?php echo nl2br(esc($callback['note'])); ?></td><td class="p-2 text-xs"><?php echo esc($callback['created_at']); ?></td><td class="p-2"><form method="post" action="<?php echo site_url('admin/emergence-booth/callbacks/' . $callback['callback_request_id']); ?>" class="flex gap-2"><?php echo csrf_field(); ?><select name="status" class="rounded border border-gray-700 bg-gray-900 p-1 text-xs"><option value="new" <?php echo $callback['status'] === 'new' ? 'selected' : ''; ?>>New</option><option value="in_progress" <?php echo $callback['status'] === 'in_progress' ? 'selected' : ''; ?>>In progress</option><option value="completed" <?php echo $callback['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option></select><button class="text-xs text-blue-400">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/tus-js-client@4.3.1/dist/tus.min.js"></script>
<script>
(() => {
  const form = document.getElementById('booth-video-upload-form');
  if (!form) return;

  const progress = document.getElementById('booth-video-upload-progress');
  const fill = document.getElementById('booth-video-upload-fill');
  const status = document.getElementById('booth-video-upload-status');
  const percent = document.getElementById('booth-video-upload-percent');
  const errorBox = document.getElementById('booth-video-upload-error');
  const errorMessage = document.getElementById('booth-video-upload-error-message');
  const fileInput = form.querySelector('input[type="file"]');
  const button = form.querySelector('button[type="submit"]');
  const csrf = form.querySelector('input[name^="csrf"]');
  const uploadKey = (file) => `epr-bunny-booth:${file.name}:${file.size}:${file.lastModified}`;

  const setProgress = (label, value) => {
    const percentage = Math.max(0, Math.min(100, Math.round(value)));
    status.textContent = label;
    percent.textContent = `${percentage}%`;
    fill.style.width = `${percentage}%`;
    fill.setAttribute('aria-valuenow', String(percentage));
  };
  const enableButton = () => {
    button.disabled = false;
    button.classList.remove('cursor-not-allowed', 'opacity-60');
  };
  const showUploadError = (message) => {
    errorMessage.textContent = message;
    errorBox.classList.remove('hidden');
    progress.classList.add('hidden');
    enableButton();
    errorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  };
  const bunnyUpload = async (file, index, total) => {
    const key = uploadKey(file);
    const credentialsRequest = new FormData();
    credentialsRequest.append('title', `Emergence Booth Video ${index + 1}`);
    credentialsRequest.append('file_size', String(file.size));
    credentialsRequest.append('resume_video_id', localStorage.getItem(key) || '');
    if (csrf) credentialsRequest.append(csrf.name, csrf.value);

    setProgress(`Preparing video ${index + 1} of ${total} for Bunny Stream…`, (index / total) * 100);
    const response = await fetch(form.dataset.bunnyCredentialsUrl, {
      method: 'POST', body: credentialsRequest, credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'},
    });
    const credentials = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(credentials.error || 'Bunny Stream could not prepare this upload.');
    if (csrf && credentials.csrfHash) csrf.value = credentials.csrfHash;

    localStorage.setItem(key, credentials.videoId);
    return new Promise(async (resolve, reject) => {
      const upload = new window.tus.Upload(file, {
        endpoint: 'https://video.bunnycdn.com/tusupload',
        retryDelays: [0, 3000, 5000, 10000, 20000, 60000],
        headers: {
          AuthorizationSignature: credentials.signature,
          AuthorizationExpire: String(credentials.expirationTime),
          VideoId: credentials.videoId,
          LibraryId: credentials.libraryId,
        },
        metadata: {filetype: file.type || 'video/mp4', title: file.name},
        onProgress: (uploaded, size) => {
          const fileProgress = size > 0 ? uploaded / size : 0;
          setProgress(`Uploading video ${index + 1} of ${total} directly to Bunny Stream…`, ((index + fileProgress) / total) * 100);
        },
        onError: (error) => reject(new Error(error.message || `Bunny Stream could not upload video ${index + 1}.`)),
        onSuccess: () => {
          localStorage.removeItem(key);
          resolve(credentials.videoId);
        },
      });
      try {
        const previousUploads = await upload.findPreviousUploads();
        if (previousUploads.length && credentials.videoId === localStorage.getItem(key)) upload.resumeFromPreviousUpload(previousUploads[0]);
        upload.start();
      } catch (error) {
        reject(error);
      }
    });
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const files = Array.from(fileInput.files || []);
    if (!files.length) return showUploadError('Choose at least one MP4, M4V, MOV, or WebM video before uploading.');
    if (!window.tus) return showUploadError('The direct Bunny uploader could not load. Refresh the page and try again.');
    const availableSlots = Number(fileInput.dataset.maxVideos || 0);
    const maxVideoBytes = Number(fileInput.dataset.maxVideoBytes || 0);
    if (availableSlots && files.length > availableSlots) return showUploadError(`You selected ${files.length} videos, but this playlist has room for ${availableSlots}.`);
    if (maxVideoBytes && files.some((file) => file.size > maxVideoBytes)) return showUploadError('Each booth video must be 512 MB or smaller.');

    errorBox.classList.add('hidden');
    progress.classList.remove('hidden');
    button.disabled = true;
    button.classList.add('cursor-not-allowed', 'opacity-60');
    setProgress('Preparing secure Bunny uploads…', 0);

    try {
      const videoIds = [];
      for (let index = 0; index < files.length; index += 1) videoIds.push(await bunnyUpload(files[index], index, files.length));

      setProgress('Saving the Bunny videos to the booth playlist…', 100);
      const saveRequest = new FormData();
      videoIds.forEach((videoId) => saveRequest.append('video_ids[]', videoId));
      if (csrf) saveRequest.append(csrf.name, csrf.value);
      const response = await fetch(form.dataset.bunnySaveUrl, {
        method: 'POST', body: saveRequest, credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'},
      });
      const result = await response.json().catch(() => ({}));
      if (!response.ok || !result.success) throw new Error(result.error || 'The Bunny uploads finished, but the playlist could not be saved.');
      window.location.assign(result.redirect || window.location.href);
    } catch (error) {
      showUploadError(`${error.message || 'The Bunny upload could not be completed.'} Keep this page open and select the same video again to resume an interrupted upload.`);
    }
  });
})();
</script>

<script>
(() => {
  const form = document.querySelector('[data-callback-topics]');
  if (!form) return;

  const list = form.querySelector('[data-callback-topic-list]');
  const createRow = () => {
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.dataset.callbackTopicRow = '';
    const input = document.createElement('input');
    input.name = 'topics[]';
    input.maxLength = 100;
    input.required = true;
    input.placeholder = 'Callback topic, e.g. Volunteering';
    input.className = 'min-w-0 flex-1 rounded border border-gray-700 bg-gray-900 p-2 text-sm text-gray-200';
    const remove = document.createElement('button');
    remove.type = 'button';
    remove.dataset.removeCallbackTopic = '';
    remove.className = 'rounded border border-gray-600 px-3 text-sm text-gray-300 hover:border-red-500 hover:text-red-300';
    remove.textContent = 'Remove';
    row.append(input, remove);
    list.append(row);
    input.focus();
  };

  form.querySelector('[data-add-callback-topic]').addEventListener('click', createRow);
  list.addEventListener('click', (event) => {
    const remove = event.target.closest('[data-remove-callback-topic]');
    if (!remove) return;
    const rows = list.querySelectorAll('[data-callback-topic-row]');
    if (rows.length === 1) {
      rows[0].querySelector('input').value = '';
      rows[0].querySelector('input').focus();
      return;
    }
    remove.closest('[data-callback-topic-row]').remove();
  });
})();
</script>

<?php $this->endSection(); ?>
