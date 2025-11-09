<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 14:39
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Edit Exhibitor</h1>
    <p class="text-sm text-gray-400">Update details for <?php echo esc($exhibitor['company_name']); ?></p>
  </div>
  <a href="<?php echo site_url('admin/exhibitors'); ?>"
     class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-white text-sm">← Back to List</a>
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

<form method="post"
      action="<?php echo site_url('admin/exhibitors/' . $exhibitor['id'] . '/update'); ?>"
      enctype="multipart/form-data"
      class="bg-gray-800 border border-gray-700 p-6 rounded-lg space-y-6">

  <?php echo csrf_field(); ?>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Company Name</label>
      <input type="text" name="company_name" value="<?php echo esc($exhibitor['company_name']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded" required>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Contact Person</label>
      <input type="text" name="contact_person" value="<?php echo esc($exhibitor['contact_person']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Email</label>
      <input type="email" name="email" value="<?php echo esc($exhibitor['email']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Telephone</label>
      <input type="text" name="telephone" value="<?php echo esc($exhibitor['telephone']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Website</label>
      <input type="text" name="website" value="<?php echo esc($exhibitor['website']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Tagline</label>
      <input type="text" name="tagline" value="<?php echo esc($exhibitor['tagline']); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Vimeo Video ID</label>
      <div class="flex items-center gap-2">
        <input type="text" name="vimeo_id" value="<?php echo esc($exhibitor['vimeo_id']); ?>"
               class="flex-1 p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
        <?php if (!empty($exhibitor['vimeo_id'])): ?>
          <button type="button"
                  data-vimeo="<?php echo esc($exhibitor['vimeo_id']); ?>"
                  class="text-blue-400 hover:text-blue-300 text-sm underline">
            View Video
          </button>
        <?php endif; ?>
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-1">Logo</label>
      <input type="file" name="logo"
             class="block w-full text-sm text-gray-400 border border-gray-700 rounded cursor-pointer bg-gray-900 focus:outline-none">
      <?php if (!empty($exhibitor['logo'])): ?>
        <div class="mt-2">
          <img src="<?php echo base_url('uploads/exhibitors/' . $exhibitor['logo']); ?>"
               alt="Exhibitor Logo"
               class="w-24 h-24 rounded border border-gray-700 object-cover">
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <label class="block text-sm text-gray-400 mb-1">Profile Summary</label>
    <textarea name="profile_summary" rows="4"
              class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded"><?php echo esc($exhibitor['profile_summary']); ?></textarea>
  </div>

  <div class="flex flex-col gap-4">
    <label class="flex items-center gap-2 text-gray-300 text-sm">
      <input type="checkbox" name="has_promotion" value="1"
             <?php echo ($exhibitor['has_promotion'] ? 'checked' : ''); ?>
             class="w-4 h-4 text-blue-600 border-gray-700 bg-gray-900 rounded toggle-promo">
      Has Promotion
    </label>

    <div class="promo-section <?php echo ($exhibitor['has_promotion'] ? '' : 'hidden'); ?>">
      <label class="block text-sm text-gray-400 mb-1">Promotion Details</label>
      <textarea name="promotion_text" rows="3"
                class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded"
                placeholder="Describe the offer, discount, or promo code"><?php echo esc($exhibitor['promotion_text']); ?></textarea>
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit"
            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">
      Save Changes
    </button>
  </div>
</form>

<?php if (!empty($exhibitor['vimeo_id'])): ?>
  <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm"
       data-preview="<?php echo esc($exhibitor['vimeo_id']); ?>">
    <div class="relative w-full max-w-lg aspect-video bg-black border border-gray-700 rounded-lg shadow-lg overflow-hidden">
      <iframe class="w-full h-full rounded-lg"
              src=""
              allow="autoplay; fullscreen; picture-in-picture"
              allowfullscreen></iframe>
      <button type="button"
              class="absolute top-2 right-2 text-gray-300 hover:text-white text-lg"
              data-close="<?php echo esc($exhibitor['vimeo_id']); ?>">✕</button>
    </div>
  </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Toggle promotion section
  const toggle = document.querySelector('.toggle-promo');
  const section = document.querySelector('.promo-section');
  if (toggle) {
    toggle.addEventListener('change', () => {
      section.classList.toggle('hidden', !toggle.checked);
    });
  }

  // Vimeo modal preview logic
  document.querySelectorAll('[data-vimeo]').forEach(button => {
    const vid = button.getAttribute('data-vimeo');
    const modal = document.querySelector(`[data-preview="${vid}"]`);
    const iframe = modal.querySelector('iframe');
    const closeBtn = modal.querySelector(`[data-close="${vid}"]`);

    button.addEventListener('click', () => {
      iframe.src = `https://player.vimeo.com/video/${vid}?autoplay=1&muted=1&loop=0`;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      setTimeout(() => {
        iframe.src = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
      }, 5000);
    });

    closeBtn.addEventListener('click', () => {
      iframe.src = '';
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    });
  });
});
</script>

<?php $this->endSection(); ?>
