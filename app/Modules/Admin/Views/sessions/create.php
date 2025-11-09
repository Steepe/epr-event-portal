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

<h1 class="text-2xl font-semibold text-gray-200 mb-6">Add Session — <?php echo esc($conference['title']); ?></h1>

<form method="post" enctype="multipart/form-data"
      action="<?php echo site_url('admin/conferences/' . $conference['conference_id'] . '/sessions/store'); ?>"
      class="space-y-6 bg-gray-800 p-6 rounded-lg border border-gray-700">

  <?php echo csrf_field(); ?>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Session Name</label>
      <input type="text" name="sessions_name" required
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Access Level</label>
      <select name="access_level" class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
        <option value="1">Free</option>
        <option value="2">Paid</option>
      </select>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Event Date</label>
      <input type="date" name="event_date" required
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Start Time</label>
      <input type="time" name="start_time" required
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">End Time</label>
      <input type="time" name="end_time" required
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
  </div>

  <div>
    <label class="block text-sm text-gray-400 mb-1">Description</label>
    <textarea name="description" rows="4"
              class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded"></textarea>
  </div>

  <div>
    <label class="block text-sm text-gray-400 mb-1">Workbook (PDF/DOC)</label>
    <input type="file" name="workbook"
           class="w-full text-gray-200 border border-gray-700 bg-gray-900 rounded cursor-pointer p-2">
  </div>

    <div>
        <label class="block text-sm text-gray-400 mb-1">Vimeo ID</label>
        <input type="text" name="vimeo_id"
               value="<?php echo esc($session['vimeo_id'] ?? ''); ?>"
               class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded"
               placeholder="e.g. 987654321">

        <div id="vimeoPreview" class="mt-3">
            <?php if (!empty($session['vimeo_id'])): ?>
                <img src="https://vumbnail.com/<?php echo esc($session['vimeo_id']); ?>.jpg"
                     alt="Vimeo Thumbnail"
                     class="w-48 rounded border border-gray-700 shadow">
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const vimeoInput = document.querySelector('input[name="vimeo_id"]');
            const previewDiv = document.getElementById('vimeoPreview');

            if (vimeoInput) {
                vimeoInput.addEventListener('input', () => {
                    const id = vimeoInput.value.trim();
                    if (id.length > 0) {
                        previewDiv.innerHTML = `<img src="https://vumbnail.com/${id}.jpg" class='w-48 rounded border border-gray-700 shadow' alt='Preview'>`;
                    } else {
                        previewDiv.innerHTML = '';
                    }
                });
            }
        });
    </script>


    <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Tags</label>
      <input type="text" name="tags"
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded"
             placeholder="e.g. Leadership, Innovation">
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Create Session</button>
  </div>
</form>

<?php $this->endSection(); ?>
