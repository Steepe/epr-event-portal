<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:27
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-4xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
  <h1 class="text-2xl font-semibold text-gray-100 mb-6">Add New Speaker</h1>

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
        action="<?php echo site_url('admin/speakers/store'); ?>"
        class="space-y-6">
    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-2">Full Name</label>
        <input type="text" name="speaker_name" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Title</label>
        <input type="text" name="speaker_title"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Company</label>
        <input type="text" name="speaker_company"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Bio</label>
      <textarea name="bio" rows="5"
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Write a short biography..."></textarea>
    </div>

    <div class="flex items-center gap-6">
      <div class="w-32 h-32 rounded overflow-hidden border border-gray-700 bg-gray-900 flex items-center justify-center">
        <img id="photoPreview" src="<?php echo base_url('assets/admin/img/placeholder-avatar.png'); ?>"
             alt="Preview" class="object-cover w-full h-full opacity-60">
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Upload Photo</label>
        <input type="file" name="speaker_photo" accept="image/*"
               onchange="previewImage(event)"
               class="text-gray-300 text-sm">
        <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, WEBP. Max size: 2MB.</p>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Save Speaker</button>
    </div>
  </form>
</div>

<script>
function previewImage(event) {
  const file = event.target.files[0];
  if (file) {
    const preview = document.getElementById('photoPreview');
    preview.src = URL.createObjectURL(file);
    preview.classList.remove('opacity-60');
  }
}
</script>

<?php $this->endSection(); ?>
