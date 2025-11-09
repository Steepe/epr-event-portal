<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:21
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-4xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
  <h1 class="text-2xl font-semibold text-gray-100 mb-6">Edit Speaker</h1>

  <form method="post" enctype="multipart/form-data"
        action="<?php echo site_url('admin/speakers/' . $speaker['speaker_id'] . '/update'); ?>"
        class="space-y-6">

    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-2">Name</label>
        <input type="text" name="speaker_name"
               value="<?php echo esc($speaker['speaker_name']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Title</label>
        <input type="text" name="speaker_title"
               value="<?php echo esc($speaker['speaker_title']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Company</label>
        <input type="text" name="speaker_company"
               value="<?php echo esc($speaker['speaker_company']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Bio</label>
      <textarea name="bio" rows="5"
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200"><?php echo esc($speaker['bio']); ?></textarea>
    </div>

    <div class="flex items-center gap-6">
      <div class="w-32 h-32 rounded overflow-hidden border border-gray-700 bg-gray-900 flex items-center justify-center">
        <?php if (!empty($speaker['speaker_photo'])): ?>
          <img id="photoPreview" src="<?php echo base_url('uploads/speakers/' . $speaker['speaker_photo']); ?>"
               alt="Preview" class="object-cover w-full h-full">
        <?php else: ?>
          <span class="text-gray-500 text-sm">No photo</span>
        <?php endif; ?>
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Change Photo</label>
        <input type="file" name="speaker_photo" accept="image/*"
               onchange="previewImage(event)"
               class="text-gray-300">
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Save Changes</button>
    </div>
  </form>
</div>

<script>
function previewImage(event) {
  const file = event.target.files[0];
  if (file) {
    const preview = document.getElementById('photoPreview');
    preview.src = URL.createObjectURL(file);
  }
}
</script>

<?php $this->endSection(); ?>
