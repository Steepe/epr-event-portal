<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:42
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-4xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-gray-100">Add Exhibitor</h1>
      <p class="text-sm text-gray-400">Enter exhibitor details and upload a logo</p>
    </div>
    <a href="<?php echo site_url('admin/exhibitors'); ?>"
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
        action="<?php echo site_url('admin/exhibitors/store'); ?>"
        class="space-y-6">
    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
      <div>
        <label class="block text-sm text-gray-400 mb-2">Exhibitor Name</label>
        <input type="text" name="exhibitor_name" required
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Company Name</label>
        <input type="text" name="company_name"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Email</label>
        <input type="email" name="email"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Phone</label>
        <input type="text" name="phone"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Country</label>
        <input type="text" name="country"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div>
        <label class="block text-sm text-gray-400 mb-2">Website</label>
        <input type="url" name="website" placeholder="https://example.com"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Description</label>
      <textarea name="description" rows="4"
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200 focus:ring-blue-500 focus:border-blue-500"></textarea>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Logo (Optional)</label>
      <input type="file" name="logo" accept="image/*"
             onchange="previewLogo(this)"
             class="text-gray-300 text-sm w-full">
      <div id="logoPreview" class="mt-3 hidden">
        <p class="text-sm text-gray-400 mb-2">Preview:</p>
        <img id="previewImg" src="#" alt="Logo Preview"
             class="w-24 h-24 object-cover border border-gray-600 rounded-lg">
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
        Save Exhibitor
      </button>
    </div>
  </form>
</div>

<script>
  function previewLogo(input) {
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('logoPreview');
        const img = document.getElementById('previewImg');
        img.src = e.target.result;
        preview.classList.remove('hidden');
      };
      reader.readAsDataURL(file);
    }
  }
</script>

<?php $this->endSection(); ?>
