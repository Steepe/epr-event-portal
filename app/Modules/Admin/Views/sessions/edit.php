<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 04:42
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-5xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-gray-100">Edit Session</h1>
      <p class="text-sm text-gray-400">Conference: <?php echo esc($conference['title']); ?></p>
    </div>
    <a href="<?php echo site_url('admin/conferences/' . $conference['conference_id'] . '/sessions'); ?>"
       class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-100 rounded text-sm">← Back</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 text-red-400 bg-gray-900 border border-red-700 p-3 rounded">
      <?php echo session()->getFlashdata('error'); ?>
    </div>
  <?php elseif (session()->getFlashdata('success')): ?>
    <div class="mb-4 text-green-400 bg-gray-900 border border-green-700 p-3 rounded">
      <?php echo session()->getFlashdata('success'); ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data"
        action="<?php echo site_url('admin/conferences/sessions/' . $session['sessions_id'] . '/update'); ?>"
        class="space-y-6">
    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-2">Session Name</label>
        <input type="text" name="sessions_name" value="<?php echo esc($session['sessions_name']); ?>" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Event Date</label>
        <input type="date" name="event_date" value="<?php echo esc($session['event_date']); ?>" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Start Time</label>
        <input type="time" name="start_time" value="<?php echo esc($session['start_time']); ?>" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">End Time</label>
        <input type="time" name="end_time" value="<?php echo esc($session['end_time']); ?>" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Access Level</label>
      <select name="access_level" class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
        <option value="1" <?php echo ($session['access_level'] == '1') ? 'selected' : ''; ?>>Free</option>
        <option value="2" <?php echo ($session['access_level'] == '2') ? 'selected' : ''; ?>>Paid</option>
      </select>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Description</label>
      <textarea name="description" rows="5" required
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"><?php echo esc($session['description']); ?></textarea>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-2">Vimeo ID</label>
        <input type="text" name="vimeo_id" value="<?php echo esc($session['vimeo_id']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"
               placeholder="Enter Vimeo video ID">

        <?php if (!empty($session['vimeo_id'])): ?>
          <div class="mt-3">
            <button type="button" class="text-blue-400 hover:text-blue-300 text-sm underline"
                    onclick="openPreview('<?php echo esc($session['vimeo_id']); ?>')">
              View Video
            </button>
          </div>
        <?php endif; ?>
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Tags</label>
        <input type="text" name="tags" value="<?php echo esc($session['tags']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"
               placeholder="Comma-separated tags (e.g., Leadership, Innovation)">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Workbook (PDF)</label>
      <input type="file" name="workbook" accept=".pdf,.docx,.pptx"
             class="w-full text-gray-300 text-sm">

      <?php if (!empty($session['workbook'])): ?>
        <div class="mt-2">
          <a href="<?php echo base_url('uploads/workbooks/' . basename($session['workbook'])); ?>"
             target="_blank"
             class="text-blue-400 hover:underline text-sm">
            View Current Workbook
          </a>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($isLive): ?>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Assign Speakers</label>
        <select name="speakers[]" multiple
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
          <?php foreach ($speakers as $sp): ?>
            <option value="<?php echo $sp['speaker_id']; ?>"
              <?php echo in_array($sp['speaker_id'], $assignedSpeakers) ? 'selected' : ''; ?>>
              <?php echo esc($sp['speaker_name']); ?>
              <?php if ($sp['speaker_company']): ?>
                — <?php echo esc($sp['speaker_company']); ?>
              <?php endif; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="text-xs text-gray-500 mt-1">Hold Ctrl or Cmd to select multiple speakers.</p>
      </div>
    <?php else: ?>
      <div class="p-4 bg-gray-900 border border-gray-700 rounded">
        <p class="text-sm text-gray-400">
          ⚠️ This conference is <strong class="text-yellow-400"><?php echo esc($conference['status']); ?></strong>.
          Speaker assignment is disabled.
        </p>
      </div>
    <?php endif; ?>

    <div class="flex justify-end">
      <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
        Update Session
      </button>
    </div>
  </form>
</div>

<!-- Video Preview Modal -->
<div id="vimeoModal" class="hidden fixed inset-0 bg-black/80 flex items-center justify-center z-50">
  <div class="bg-gray-900 rounded-lg shadow-lg overflow-hidden border border-gray-700 w-[480px]">
    <div class="flex justify-between items-center p-3 border-b border-gray-700">
      <h3 class="text-sm text-gray-300">Video Preview</h3>
      <button onclick="closePreview()" class="text-gray-400 hover:text-gray-100">✕</button>
    </div>
    <div class="p-3">
      <iframe id="vimeoPlayer" class="rounded-md w-full h-64"
              src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
    </div>
  </div>
</div>

<script>
  function openPreview(vimeoId) {
    const modal = document.getElementById('vimeoModal');
    const iframe = document.getElementById('vimeoPlayer');
    iframe.src = `https://player.vimeo.com/video/${vimeoId}?autoplay=1&muted=0`;
    modal.classList.remove('hidden');
  }

  function closePreview() {
    const modal = document.getElementById('vimeoModal');
    const iframe = document.getElementById('vimeoPlayer');
    iframe.src = '';
    modal.classList.add('hidden');
  }
</script>

<?php $this->endSection(); ?>
