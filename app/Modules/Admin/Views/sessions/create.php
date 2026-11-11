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

  <div class="mb-6 rounded-lg border border-amber-500/70 bg-amber-950/30 p-4 text-amber-100">
    <p class="font-semibold">Important: enter every session date and time in GMT (UTC+0).</p>
    <p class="mt-1 text-sm text-amber-200/85">The portal automatically converts the saved GMT time to each attendee’s local time zone.</p>
  </div>

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
        <label class="block text-sm text-gray-400 mb-2">Event Date (GMT / UTC+0)</label>
        <input type="date" name="event_date" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Start Time (GMT / UTC+0)</label>
        <input type="time" name="start_time" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">End Time (GMT / UTC+0)</label>
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
      <label class="block text-sm text-gray-400 mb-2">Livepush Video ID</label>
      <input type="text" name="livepush_id" pattern="[A-Za-z0-9_-]+"
             class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"
             placeholder="e.g., emMVd4NBsIW87CXc">
      <p class="mt-1 text-xs text-gray-500">Paste only the ID after <code class="text-gray-400">/live/</code>, not the full iframe code.</p>
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
        <?php if ($speakers): ?>
          <div class="max-h-64 overflow-y-auto rounded border border-gray-700 bg-gray-900 divide-y divide-gray-800">
            <?php foreach ($speakers as $sp): ?>
              <label class="flex cursor-pointer items-center gap-3 px-3 py-2.5 text-sm text-gray-200 hover:bg-gray-800">
                <input type="checkbox" name="speakers[]" value="<?php echo (int) $sp['speaker_id']; ?>"
                       class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-blue-600 focus:ring-blue-500">
                <span>
                  <?php echo esc($sp['speaker_name']); ?>
                  <?php if ($sp['speaker_company']): ?>
                    <span class="text-gray-400">— <?php echo esc($sp['speaker_company']); ?></span>
                  <?php endif; ?>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
          <p class="text-xs text-gray-500 mt-1">Tick each speaker who will appear in this session.</p>
        <?php else: ?>
          <p class="rounded border border-gray-700 bg-gray-900 p-3 text-sm text-gray-400">No speakers are available yet.</p>
        <?php endif; ?>
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
