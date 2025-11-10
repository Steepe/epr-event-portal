<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 10/11/2025
 * Time: 20:05
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Edit Webinar</h1>
    <p class="text-sm text-gray-400">Update webinar details or mark as past.</p>
  </div>
  <a href="<?php echo site_url('admin/webinars'); ?>"
     class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded text-sm">
     ← Back
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

<div class="bg-gray-800 border border-gray-700 rounded-lg p-6">
  <form action="<?php echo site_url('admin/webinars/update/' . $webinar['event_id']); ?>" method="post">
    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-1">Event Name</label>
        <input type="text" name="event_name"
               value="<?php echo old('event_name', $webinar['event_name']); ?>"
               class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-gray-100 focus:ring focus:ring-blue-600"
               required>
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-1">Event Date</label>
        <input type="date" name="event_date"
               value="<?php echo old('event_date', $webinar['event_date']); ?>"
               class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-gray-100 focus:ring focus:ring-blue-600"
               required>
      </div>
    </div>

    <div class="mt-6">
      <label class="block text-sm text-gray-400 mb-1">Zoom Link</label>
      <input type="url" name="zoom_link"
             value="<?php echo old('zoom_link', $webinar['zoom_link']); ?>"
             class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-gray-100 focus:ring focus:ring-blue-600">
    </div>

    <div class="mt-6">
      <label class="block text-sm text-gray-400 mb-1">Vimeo ID (Recording)</label>
      <input type="text" name="vimeo_id"
             value="<?php echo old('vimeo_id', $webinar['vimeo_id']); ?>"
             placeholder="e.g. 987654321"
             class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-gray-100 focus:ring focus:ring-blue-600">
    </div>

    <div class="mt-6">
      <label class="block text-sm text-gray-400 mb-1">Tags</label>
      <input type="text" name="tags"
             value="<?php echo old('tags', $webinar['tags']); ?>"
             placeholder="comma-separated tags"
             class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-gray-100 focus:ring focus:ring-blue-600">
    </div>

    <div class="mt-6 flex flex-wrap gap-6">
      <div class="flex items-center gap-2">
        <input type="checkbox" id="is_open" name="is_open" value="1"
               class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-600"
               <?php echo $webinar['is_open'] ? 'checked' : ''; ?>>
        <label for="is_open" class="text-sm text-gray-300">Allow attendee access</label>
      </div>

      <div class="flex items-center gap-2">
        <input type="checkbox" id="is_past" name="is_past" value="1"
               class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-600"
               <?php echo $webinar['is_past'] ? 'checked' : ''; ?>>
        <label for="is_past" class="text-sm text-gray-300">Mark as past webinar</label>
      </div>
    </div>

    <div class="mt-8 flex justify-end">
      <button type="submit"
              class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
        Update Webinar
      </button>
    </div>
  </form>
</div>

<?php $this->endSection(); ?>
