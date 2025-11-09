<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 04:41
 */


$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">
      Sessions — <?php echo esc($conference['title']); ?>
    </h1>
    <p class="text-sm text-gray-400">All sessions under this conference.</p>
  </div>
  <a href="<?php echo site_url('admin/conferences/' . $conference['conference_id'] . '/sessions/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
     + Add Session
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
  <?php if (empty($sessions)): ?>
    <p class="text-gray-400">No sessions found for this conference.</p>
  <?php else: ?>
    <?php foreach ($sessions as $session): ?>
      <div class="bg-gray-800 border border-gray-700 rounded-lg shadow hover:shadow-lg transition">
        <div class="p-5">
          <h2 class="text-lg font-semibold text-gray-100">
            <?php echo esc($session['sessions_name']); ?>
          </h2>
          <p class="text-sm text-gray-400 mt-1">
            <?php echo date('M d, Y', strtotime($session['event_date'])); ?>
            • <?php echo esc($session['start_time']); ?> - <?php echo esc($session['end_time']); ?>
          </p>

          <p class="mt-3 text-gray-300 text-sm line-clamp-3">
            <?php echo esc(substr($session['description'], 0, 150)); ?>...
          </p>

          <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-gray-400">
            <span class="px-2 py-1 rounded bg-gray-700">
              <?php echo ($session['access_level'] == 1) ? 'Free' : 'Paid'; ?>
            </span>

            <?php if (!empty($session['tags'])):
              $tags = explode(',', $session['tags']); ?>
              <div class="flex flex-wrap gap-1">
                <?php foreach ($tags as $tag): ?>
                  <span class="px-2 py-1 text-xs rounded bg-blue-800/50 text-blue-300 border border-blue-700">
                    <?php echo esc(trim($tag)); ?>
                  </span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!empty($session['workbook'])): ?>
            <div class="mt-3">
              <a href="<?php echo base_url('uploads/workbooks/' . $session['workbook']); ?>" target="_blank"
                 class="text-blue-400 hover:underline text-sm">View Workbook</a>
            </div>
          <?php endif; ?>

          <?php if (!empty($session['vimeo_id'])): ?>
            <div class="mt-3">
              <button type="button"
                      class="text-blue-400 text-sm underline hover:text-blue-300"
                      onclick="openVimeoPreview('<?php echo esc($session['vimeo_id']); ?>')">
                View Video
              </button>
            </div>
          <?php endif; ?>

          <div class="mt-4 flex justify-end gap-3">
            <a href="<?php echo site_url('admin/conferences/sessions/' . $session['sessions_id'] . '/edit'); ?>"
               class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">
               Edit
            </a>
            <a href="<?php echo site_url('admin/conferences/sessions/' . $session['sessions_id'] . '/delete'); ?>"
               class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded"
               onclick="return confirm('Delete this session?');">
               Delete
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

  // Auto-close after 5 seconds
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
