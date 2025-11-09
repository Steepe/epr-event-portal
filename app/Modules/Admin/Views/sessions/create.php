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
      <h1 class="text-2xl font-semibold text-gray-100">Add Session</h1>
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
        action="<?php echo site_url('admin/conferences/' . $conference['conference_id'] . '/sessions/store'); ?>"
        class="space-y-6">
    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-2">Session Name</label>
        <input type="text" name="sessions_name" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Event Date</label>
        <input type="date" name="event_date" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Start Time</label>
        <input type="time" name="start_time" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">End Time</label>
        <input type="time" name="end_time" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Access Level</label>
      <select name="access_level" required
              class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
        <option value="1">Free</option>
        <option value="2">Paid</option>
      </select>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Description</label>
      <textarea name="description" rows="5" required
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Vimeo ID</label>
      <input type="text" name="vimeo_id"
             class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"
             placeholder="Enter Vimeo video ID (e.g., 123456789)">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Tags</label>
      <input type="text" name="tags"
             class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"
             placeholder="Comma-separated tags (e.g., Leadership, Innovation)">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Workbook (PDF)</label>
      <input type="file" name="workbook" accept=".pdf,.docx,.pptx"
             class="w-full text-gray-300 text-sm">
    </div>

    <?php if ($isLive): ?>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Assign Speakers</label>
        <select name="speakers[]" multiple
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
          <?php foreach ($speakers as $sp): ?>
            <option value="<?php echo $sp['speaker_id']; ?>">
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
      <button type="submit"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Save Session</button>
    </div>
  </form>
</div>

<?php $this->endSection(); ?>
