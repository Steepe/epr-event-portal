<?php
$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
$communities = $communities ?? [];
$admins = $admins ?? [];
?>

<div class="mb-6 flex flex-wrap items-start justify-between gap-4">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Country Communities</h1>
    <p class="mt-1 text-sm text-gray-400">Each country has a Country Manager, an in-portal message inbox, and a WhatsApp community link or QR code.</p>
  </div>
  <a href="<?php echo site_url('admin/communities/chats'); ?>" class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-500">Open manager inbox</a>
</div>

<?php if (session('success')): ?><div class="mb-5 rounded border border-green-700 bg-green-950/40 p-4 text-green-200"><?php echo esc(session('success')); ?></div><?php endif; ?>
<?php if (session('error')): ?><div class="mb-5 rounded border border-red-700 bg-red-950/40 p-4 text-red-200"><?php echo esc(session('error')); ?></div><?php endif; ?>

<?php if (! ($ready ?? false)): ?>
  <div class="rounded-lg border border-amber-600 bg-amber-950/30 p-5 text-amber-100">
    <strong>Communities is not ready yet.</strong>
    <p class="mt-1 text-sm">Deploy this release, then run the pending Communities migration when you are ready. No records can be saved until both community tables exist.</p>
  </div>
<?php else: ?>
  <?php if (! empty($canManage)): ?>
    <section class="mb-8 rounded-lg border border-gray-700 bg-gray-800 p-5">
      <h2 class="text-lg font-semibold text-gray-100">Add country community</h2>
      <p class="mt-1 text-sm text-gray-400">Create one entry for each country where EPR has an active community. Select the admin account that will receive and reply to attendee messages.</p>
      <form method="post" action="<?php echo site_url('admin/communities/store'); ?>" enctype="multipart/form-data" class="mt-5 grid gap-4 md:grid-cols-2">
        <?php echo csrf_field(); ?>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Country</span><input name="country" value="<?php echo esc(old('country')); ?>" required maxlength="120" placeholder="e.g. Ghana" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100"></label>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Country Manager</span><select name="manager_admin_id" required class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100"><option value="">Select an active admin</option><?php foreach ($admins as $admin): ?><option value="<?php echo (int) $admin['id']; ?>" <?php echo (int) old('manager_admin_id') === (int) $admin['id'] ? 'selected' : ''; ?>><?php echo esc($admin['name']); ?> — <?php echo esc($admin['email']); ?></option><?php endforeach; ?></select></label>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Manager title</span><input name="manager_title" value="<?php echo esc(old('manager_title')); ?>" maxlength="255" placeholder="e.g. Country Manager, Ghana" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100"></label>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Display order</span><input name="sort_order" type="number" value="<?php echo esc(old('sort_order') ?: '0'); ?>" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100"></label>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Manager photo (required)</span><input type="file" name="manager_photo" required accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full text-sm text-gray-300"></label>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">WhatsApp community link</span><input name="whatsapp_url" type="url" value="<?php echo esc(old('whatsapp_url')); ?>" maxlength="1000" placeholder="https://chat.whatsapp.com/..." class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100"></label>
        <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">WhatsApp QR code (use if no link)</span><input type="file" name="whatsapp_qr" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full text-sm text-gray-300"></label>
        <label class="flex items-center gap-2 text-sm text-gray-300 md:col-span-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-700 bg-gray-900"> Show this community in the portal</label>
        <div class="md:col-span-2"><button class="rounded bg-fuchsia-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-fuchsia-600">Save country community</button></div>
      </form>
    </section>
  <?php else: ?>
    <div class="mb-6 rounded border border-blue-700 bg-blue-950/30 p-4 text-sm text-blue-100">You can reply to messages for the community assigned to you. Only Super Admins can add or edit communities.</div>
  <?php endif; ?>

  <section class="grid gap-5 xl:grid-cols-2">
    <?php foreach ($communities as $community): ?>
      <?php $photoUrl = ! empty($community['manager_photo']) ? base_url(ltrim((string) $community['manager_photo'], '/')) : ''; ?>
      <article class="rounded-lg border border-gray-700 bg-gray-800 p-5">
        <div class="flex items-start justify-between gap-4">
          <div class="flex min-w-0 items-center gap-3">
            <?php if ($photoUrl): ?><img src="<?php echo esc($photoUrl, 'attr'); ?>" alt="" class="h-14 w-14 rounded-full object-cover"><?php else: ?><span class="flex h-14 w-14 items-center justify-center rounded-full bg-fuchsia-800 text-lg font-semibold text-white"><?php echo esc(mb_strtoupper(mb_substr((string) ($community['manager_name'] ?: 'M'), 0, 1))); ?></span><?php endif; ?>
            <div class="min-w-0"><h2 class="truncate text-lg font-semibold text-gray-100"><?php echo esc($community['country']); ?></h2><p class="text-sm text-gray-300"><?php echo esc($community['manager_name'] ?: 'Unassigned manager'); ?></p><p class="text-xs text-gray-500"><?php echo esc($community['manager_title'] ?: 'Country Manager'); ?></p></div>
          </div>
          <span class="rounded-full px-2 py-1 text-xs <?php echo ! empty($community['is_active']) ? 'bg-green-900/60 text-green-300' : 'bg-gray-700 text-gray-400'; ?>"><?php echo ! empty($community['is_active']) ? 'Visible' : 'Hidden'; ?></span>
        </div>
        <div class="mt-4 flex flex-wrap gap-2 text-xs text-gray-400"><span class="rounded bg-gray-900 px-2 py-1"><?php echo ! empty($community['whatsapp_url']) ? 'WhatsApp link set' : 'QR code only'; ?></span><span class="rounded bg-gray-900 px-2 py-1">Order <?php echo (int) $community['sort_order']; ?></span></div>
        <div class="mt-4"><a href="<?php echo site_url('admin/communities/chats?community=' . (int) $community['community_id']); ?>" class="inline-flex rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-500">Open manager inbox</a></div>

        <?php if (! empty($canManage)): ?>
          <details class="mt-5 border-t border-gray-700 pt-4">
            <summary class="cursor-pointer text-sm font-medium text-gray-300">Edit community</summary>
            <form method="post" action="<?php echo site_url('admin/communities/' . (int) $community['community_id'] . '/update'); ?>" enctype="multipart/form-data" class="mt-4 grid gap-3 md:grid-cols-2">
              <?php echo csrf_field(); ?>
              <label class="text-sm text-gray-300">Country<input name="country" required maxlength="120" value="<?php echo esc($community['country']); ?>" class="mt-1 w-full rounded border border-gray-700 bg-gray-900 p-2 text-gray-100"></label>
              <label class="text-sm text-gray-300">Country Manager<select name="manager_admin_id" required class="mt-1 w-full rounded border border-gray-700 bg-gray-900 p-2 text-gray-100"><?php foreach ($admins as $admin): ?><option value="<?php echo (int) $admin['id']; ?>" <?php echo (int) $community['manager_admin_id'] === (int) $admin['id'] ? 'selected' : ''; ?>><?php echo esc($admin['name']); ?> — <?php echo esc($admin['email']); ?></option><?php endforeach; ?></select></label>
              <label class="text-sm text-gray-300">Manager title<input name="manager_title" maxlength="255" value="<?php echo esc($community['manager_title']); ?>" class="mt-1 w-full rounded border border-gray-700 bg-gray-900 p-2 text-gray-100"></label>
              <label class="text-sm text-gray-300">Display order<input name="sort_order" type="number" value="<?php echo (int) $community['sort_order']; ?>" class="mt-1 w-full rounded border border-gray-700 bg-gray-900 p-2 text-gray-100"></label>
              <label class="text-sm text-gray-300">Replace manager photo<input type="file" name="manager_photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-1 w-full text-xs text-gray-300"></label>
              <label class="text-sm text-gray-300">WhatsApp link<input type="url" name="whatsapp_url" maxlength="1000" value="<?php echo esc($community['whatsapp_url']); ?>" class="mt-1 w-full rounded border border-gray-700 bg-gray-900 p-2 text-gray-100"></label>
              <label class="text-sm text-gray-300">Replace WhatsApp QR<input type="file" name="whatsapp_qr" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-1 w-full text-xs text-gray-300"></label>
              <label class="flex items-center gap-2 self-end text-sm text-gray-300"><input type="checkbox" name="is_active" value="1" <?php echo ! empty($community['is_active']) ? 'checked' : ''; ?> class="rounded border-gray-700 bg-gray-900"> Visible in portal</label>
              <div class="flex flex-wrap gap-3 md:col-span-2"><button class="rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-500">Save changes</button><button form="delete-community-<?php echo (int) $community['community_id']; ?>" class="rounded bg-red-700 px-4 py-2 text-sm text-white hover:bg-red-600" onclick="return confirm('Delete this country community and all of its messages?');">Delete community</button></div>
            </form>
            <form id="delete-community-<?php echo (int) $community['community_id']; ?>" method="post" action="<?php echo site_url('admin/communities/' . (int) $community['community_id'] . '/delete'); ?>"><?php echo csrf_field(); ?></form>
          </details>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
    <?php if ($communities === []): ?><div class="rounded-lg border border-dashed border-gray-700 p-8 text-center text-sm text-gray-400 xl:col-span-2">No country communities have been added yet.</div><?php endif; ?>
  </section>
<?php endif; ?>

<?php $this->endSection(); ?>
