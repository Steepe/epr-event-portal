<?php $this->extend('App\Modules\Admin\Views\layout'); ?>
<?php $this->section('content'); ?>

<div class="bg-gray-900 text-gray-200 p-6 rounded-lg shadow border border-gray-800">
  <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
    <div>
      <h1 class="text-xl font-semibold">Admin Activity Log</h1>
      <p class="text-sm text-gray-400 mt-1">Latest 250 recorded activities. Passwords, tokens, and secrets are redacted.</p>
    </div>
  </div>

  <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
    <select name="admin_id" class="bg-gray-800 border border-gray-700 rounded p-2.5">
      <option value="">All administrators</option>
      <?php foreach ($admins as $admin): ?>
        <option value="<?php echo (int) $admin['id']; ?>" <?php echo $selectedAdminId === (int) $admin['id'] ? 'selected' : ''; ?>><?php echo esc($admin['name'] . ' (' . $admin['email'] . ')'); ?></option>
      <?php endforeach; ?>
    </select>
    <select name="event" class="bg-gray-800 border border-gray-700 rounded p-2.5">
      <option value="">All activities</option>
      <?php foreach ($events as $item): ?>
        <option value="<?php echo esc($item['event']); ?>" <?php echo $selectedEvent === $item['event'] ? 'selected' : ''; ?>><?php echo esc(ucwords(str_replace('_', ' ', $item['event']))); ?></option>
      <?php endforeach; ?>
    </select>
    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Filter</button>
  </form>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="border-b border-gray-700 text-left text-gray-400">
        <tr><th class="p-3">Timestamp</th><th class="p-3">Administrator</th><th class="p-3">Activity</th><th class="p-3">Route</th><th class="p-3">Details</th><th class="p-3">IP</th></tr>
      </thead>
      <tbody>
      <?php foreach ($activities as $activity): ?>
        <tr class="border-b border-gray-800 align-top">
          <td class="p-3 whitespace-nowrap"><?php echo esc($activity['created_at']); ?></td>
          <td class="p-3"><?php echo esc($activity['admin_name'] ?: 'Unknown'); ?><br><span class="text-xs text-gray-500"><?php echo esc($activity['admin_email'] ?? ''); ?></span></td>
          <td class="p-3"><?php echo esc(ucwords(str_replace('_', ' ', $activity['event']))); ?><br><span class="text-xs text-gray-500"><?php echo esc($activity['http_method']); ?> · <?php echo esc((string) ($activity['response_status'] ?? '')); ?></span></td>
          <td class="p-3 break-all"><?php echo esc($activity['route']); ?></td>
          <td class="p-3 max-w-md break-words text-xs text-gray-400"><?php echo esc($activity['details'] ?? ''); ?></td>
          <td class="p-3 whitespace-nowrap"><?php echo esc($activity['ip_address'] ?? ''); ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if ($activities === []): ?>
        <tr><td colspan="6" class="p-6 text-center text-gray-400">No activity has been recorded yet.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->endSection(); ?>
