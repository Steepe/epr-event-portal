<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 20:04
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Webinars</h1>
    <p class="text-sm text-gray-400">Manage upcoming and past webinars.</p>
  </div>
  <a href="<?php echo site_url('admin/webinars/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
     + Add Webinar
  </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
  <div class="mb-4 text-red-400 bg-gray-800 border border-red-600 p-3 rounded">
    <?php echo session()->getFlashdata('error'); ?>
  </div>
<?php elseif (session()->getFlashdata('success')): ?>
  <div class="mb-4 text-green-400 bg-gray-800 border border-green-600 p-3 rounded">
    <?php echo session()->getFlashdata('success'); ?>
  </div>
<?php endif; ?>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
  <?php if (empty($webinars)): ?>
    <p class="text-gray-400">No webinars found.</p>
  <?php else: ?>
    <?php foreach ($webinars as $webinar): ?>
      <div class="bg-gray-800 border border-gray-700 rounded-lg shadow hover:shadow-lg transition">
        <div class="p-5">
          <h2 class="text-lg font-semibold text-gray-100">
            <?php echo esc($webinar['event_name']); ?>
          </h2>

          <p class="text-sm text-gray-400 mt-1">
            <?php echo date('M d, Y', strtotime($webinar['event_date'])); ?>
            <?php if (!empty($webinar['start_time'])): ?>
              • <?php echo esc($webinar['start_time']); ?>
            <?php endif; ?>
          </p>

          <?php if (!empty($webinar['tags'])):
            $tags = explode(',', $webinar['tags']); ?>
            <div class="mt-3 flex flex-wrap gap-1">
              <?php foreach ($tags as $tag): ?>
                <span class="px-2 py-1 text-xs rounded bg-blue-800/50 text-blue-300 border border-blue-700">
                  <?php echo esc(trim($tag)); ?>
                </span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-gray-400">
            <?php if ($webinar['is_past']): ?>
              <span class="px-2 py-1 rounded bg-gray-700 text-gray-300">Past Webinar</span>
            <?php elseif ($webinar['is_open']): ?>
              <span class="px-2 py-1 rounded bg-green-800/40 text-green-300 border border-green-700">Open</span>
            <?php else: ?>
              <span class="px-2 py-1 rounded bg-yellow-800/40 text-yellow-300 border border-yellow-700">Closed</span>
            <?php endif; ?>
          </div>

          <div class="mt-3 space-y-1">
            <?php if (!empty($webinar['zoom_link'])): ?>
              <a href="<?php echo esc($webinar['zoom_link']); ?>"
                 target="_blank"
                 class="text-blue-400 hover:underline text-sm">
                 Join Zoom Session
              </a>
            <?php endif; ?>

            <?php if (!empty($webinar['vimeo_id'])): ?>
              <button type="button"
                      class="text-blue-400 text-sm underline hover:text-blue-300"
                      onclick="openVimeoPreview('<?php echo esc($webinar['vimeo_id']); ?>')">
                View Recording
              </button>
            <?php endif; ?>
          </div>

          <div class="mt-4 flex justify-end gap-3">
            <a href="<?php echo site_url('admin/webinars/edit/' . $webinar['event_id']); ?>"
               class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">
               Edit
            </a>
            <a href="<?php echo site_url('admin/webinars/delete/' . $webinar['event_id']); ?>"
               class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded"
               onclick="return confirm('Delete this webinar?');">
               Delete
            </a>
            <a href="<?php echo site_url('admin/webinars/toggle/' . $webinar['event_id']); ?>"
               class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded">
               <?php echo $webinar['is_open'] ? 'Close Access' : 'Open Access'; ?>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Global Vimeo Preview Modal -->
<div id="vimeoModal"
     class="hidden fixed inset-0 z-50 bg-black/70 flex items-center justify-center p-4">
  <div class="bg-gray-900 border border-gray-700 rounded-lg overflow-hidden shadow-lg relative w-full max-w-xl">
    <iframe id="vimeoPlayer" class="w-full h-64 rounded-lg"
            src="" frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
            allowfullscreen></iframe>
    <button id="closeVimeoModal"
            class="absolute top-2 right-2 text-gray-400 hover:text-white text-xl font-bold">
      &times;
    </button>
  </div>
</div>

<script>
function openVimeoPreview(vimeoId) {
  const modal = document.getElementById('vimeoModal');
  const player = document.getElementById('vimeoPlayer');
  modal.classList.remove('hidden');
  player.src = `https://player.vimeo.com/video/${vimeoId}?autoplay=1&muted=1`;

  // Auto-close after 50s
  setTimeout(() => closeVimeoPreview(), 50000);
}

function closeVimeoPreview() {
  const modal = document.getElementById('vimeoModal');
  const player = document.getElementById('vimeoPlayer');
  player.src = '';
  modal.classList.add('hidden');
}

document.getElementById('closeVimeoModal').addEventListener('click', closeVimeoPreview);
document.getElementById('vimeoModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeVimeoPreview();
});
</script>

<?php $this->endSection(); ?>
