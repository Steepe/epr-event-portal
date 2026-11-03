<?php $this->extend('App\Modules\Admin\Views\layout'); ?>
<?php $this->section('content'); ?>

<div class="mb-6">
  <h1 class="text-2xl font-semibold text-gray-200">Sessions</h1>
  <p class="text-sm text-gray-400">All conference sessions.</p>
</div>

<?php if (empty($sessions)): ?>
  <div class="bg-gray-800 border border-gray-700 rounded-lg p-8 text-center text-gray-400">No sessions found.</div>
<?php else: ?>
  <div class="overflow-x-auto bg-gray-800 border border-gray-700 rounded-lg">
    <table class="min-w-full text-sm">
      <thead class="text-left text-gray-400 border-b border-gray-700">
        <tr><th class="p-4">Session</th><th class="p-4">Conference</th><th class="p-4">Date &amp; time</th><th class="p-4">Access</th><th class="p-4"></th></tr>
      </thead>
      <tbody>
      <?php foreach ($sessions as $session): ?>
        <tr class="border-b border-gray-700 last:border-0">
          <td class="p-4 font-medium text-gray-100"><?php echo esc($session['sessions_name']); ?></td>
          <td class="p-4 text-gray-300"><?php echo esc($session['conference_title'] ?: 'Unknown conference'); ?></td>
          <td class="p-4 text-gray-400"><?php echo esc(date('M d, Y', strtotime($session['event_date']))); ?><br><span class="text-xs"><?php echo esc($session['start_time']); ?> – <?php echo esc($session['end_time']); ?></span></td>
          <td class="p-4"><span class="px-2 py-1 text-xs rounded bg-gray-700"><?php echo (int) $session['access_level'] === 1 ? 'Free' : 'Paid'; ?></span></td>
          <td class="p-4 text-right"><a href="<?php echo site_url('admin/conferences/sessions/' . $session['sessions_id'] . '/edit'); ?>" class="text-blue-300 hover:text-blue-200 hover:underline">Edit</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php $this->endSection(); ?>
