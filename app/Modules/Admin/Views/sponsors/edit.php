<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 15:36
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex items-center justify-between">
  <h1 class="text-2xl font-semibold text-gray-200">Edit Sponsor</h1>
  <a href="<?php echo site_url('admin/sponsors'); ?>"
     class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-white text-sm">← Back</a>
</div>

<form method="post" action="<?php echo site_url('admin/sponsors/' . $sponsor['id'] . '/update'); ?>" enctype="multipart/form-data"
      class="bg-gray-800 border border-gray-700 p-6 rounded-lg space-y-6">

  <?php echo csrf_field(); ?>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Sponsor Name</label>
      <input type="text" name="name" value="<?php echo esc($sponsor['name']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded" required>
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Tier</label>
      <select name="tier"
              class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
        <?php foreach (['Platinum', 'Gold', 'Silver', 'Bronze'] as $tier): ?>
          <option value="<?php echo $tier; ?>" <?php echo ($sponsor['tier'] === $tier ? 'selected' : ''); ?>>
            <?php echo $tier; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Website</label>
      <input type="url" name="website" value="<?php echo esc($sponsor['website']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Logo</label>
      <input type="file" name="logo"
             class="block w-full text-sm text-gray-400 border border-gray-700 rounded cursor-pointer bg-gray-900">
      <?php if (!empty($sponsor['logo'])): ?>
        <div class="mt-2">
          <img src="<?php echo base_url('uploads/sponsors/' . $sponsor['logo']); ?>"
               alt="Sponsor Logo"
               class="w-24 h-24 object-contain rounded border border-gray-700 bg-gray-900 p-1">
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <label class="flex items-center gap-2 text-gray-300 text-sm">
      <input type="checkbox" name="is_featured" value="1"
             <?php echo ($sponsor['is_featured'] ? 'checked' : ''); ?>
             class="w-4 h-4 text-blue-600 border-gray-700 bg-gray-900 rounded">
      Featured Sponsor
    </label>
  </div>

  <div>
    <label class="block text-sm text-gray-400 mb-1">Description</label>
    <textarea name="description" rows="4"
              class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded"><?php echo esc($sponsor['description']); ?></textarea>
  </div>

  <div class="flex justify-end">
    <button type="submit"
            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">
      Save Changes
    </button>
  </div>
</form>

<?php $this->endSection(); ?>
