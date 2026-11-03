<?php $this->extend('App\Modules\Admin\Views\layout'); ?>
<?php $this->section('content'); ?>

<div class="mb-6">
  <h1 class="text-2xl font-semibold text-gray-200">Sessions</h1>
  <p class="text-sm text-gray-400">Select a conference to view and manage its sessions.</p>
</div>

<?php if (empty($conferences)): ?>
  <div class="bg-gray-800 border border-gray-700 rounded-lg p-8 text-center text-gray-400">No conferences found.</div>
<?php else: ?>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <?php foreach ($conferences as $conference): ?>
      <a href="<?php echo site_url('admin/conferences/' . $conference['conference_id'] . '/sessions'); ?>"
         class="block bg-gray-800 border border-gray-700 rounded-lg p-5 hover:border-blue-500 hover:bg-gray-750 transition-colors">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h2 class="text-lg font-semibold text-gray-100"><?php echo esc($conference['title']); ?></h2>
            <p class="mt-1 text-sm text-gray-400"><?php echo esc((string) $conference['year']); ?></p>
          </div>
          <span class="shrink-0 px-2.5 py-1 rounded bg-blue-900/50 border border-blue-700 text-blue-200 text-xs">
            <?php echo (int) $conference['session_count']; ?> session<?php echo (int) $conference['session_count'] === 1 ? '' : 's'; ?>
          </span>
        </div>
        <div class="mt-5 flex items-center justify-between text-sm text-blue-300">
          <span>View Sessions</span><i class="bx bx-right-arrow-alt text-lg"></i>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php $this->endSection(); ?>
