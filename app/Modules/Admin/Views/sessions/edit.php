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

<h1 class="text-2xl font-semibold text-gray-200 mb-6">Edit Session — <?php echo esc($conference['title']); ?></h1>

<form method="post" enctype="multipart/form-data"
      action="<?php echo site_url('admin/conferences/sessions/' . $session['sessions_id'] . '/update'); ?>"
      class="space-y-6 bg-gray-800 p-6 rounded-lg border border-gray-700">

  <?php echo csrf_field(); ?>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Session Name</label>
      <input type="text" name="sessions_name" required
             value="<?php echo esc($session['sessions_name']); ?>"
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Access Level</label>
      <select name="access_level" class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
        <option value="1" <?php echo ($session['access_level'] == 1) ? 'selected' : ''; ?>>Free</option>
        <option value="2" <?php echo ($session['access_level'] == 2) ? 'selected' : ''; ?>>Paid</option>
      </select>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Event Date</label>
      <input type="date" name="event_date"
             value="<?php echo esc($session['event_date']); ?>"
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Start Time</label>
      <input type="time" name="start_time"
             value="<?php echo esc($session['start_time']); ?>"
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">End Time</label>
      <input type="time" name="end_time"
             value="<?php echo esc($session['end_time']); ?>"
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
  </div>

  <div>
    <label class="block text-sm text-gray-400 mb-1">Description</label>
    <textarea name="description" rows="4"
              class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded"><?php echo esc($session['description']); ?></textarea>
  </div>

  <div>
    <label class="block text-sm text-gray-400 mb-1">Workbook (Replace)</label>
    <input type="file" name="workbook"
           class="w-full text-gray-200 border border-gray-700 bg-gray-900 rounded cursor-pointer p-2">
    <?php if (!empty($session['workbook'])): ?>
      <p class="mt-2 text-sm text-blue-400">
        Current: <a href="<?php echo base_url($session['workbook']); ?>" target="_blank" class="underline">View Workbook</a>
      </p>
    <?php endif; ?>
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
             value="<?php echo esc($session['tags']); ?>"
             class="w-full p-2 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Update Session</button>
  </div>
</form>

<?php $this->endSection(); ?>
