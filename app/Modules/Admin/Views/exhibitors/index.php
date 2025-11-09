<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:39
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Exhibitors</h1>
    <p class="text-sm text-gray-400">Manage all registered exhibitors</p>
  </div>
  <a href="<?php echo site_url('admin/exhibitors/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">+ Add Exhibitor</a>
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

<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
  <?php if (empty($exhibitors)): ?>
    <p class="text-gray-400">No exhibitors found.</p>
  <?php else: ?>
    <?php foreach ($exhibitors as $exhibitor): ?>
      <div class="bg-gray-800 border border-gray-700 rounded-lg shadow hover:shadow-lg transition p-5 relative">
        <div class="flex flex-col items-center text-center">
          <?php if (!empty($exhibitor['logo']) && file_exists(FCPATH . 'uploads/exhibitors/' . $exhibitor['logo'])): ?>
            <img src="<?php echo base_url('uploads/exhibitors/' . $exhibitor['logo']); ?>"
                 alt="<?php echo esc($exhibitor['company_name']); ?>"
                 class="w-20 h-20 object-cover rounded-full border border-gray-600 mb-3">
          <?php else: ?>
            <div class="w-20 h-20 flex items-center justify-center bg-gray-700 text-gray-400 rounded-full border border-gray-600 mb-3">
              <i class="bx bx-building text-3xl"></i>
            </div>
          <?php endif; ?>

          <h2 class="text-lg font-semibold text-gray-100"><?php echo esc($exhibitor['company_name']); ?></h2>
          <?php if (!empty($exhibitor['tagline'])): ?>
            <p class="text-sm text-gray-400 italic"><?php echo esc($exhibitor['tagline']); ?></p>
          <?php endif; ?>

          <?php if (!empty($exhibitor['contact_person'])): ?>
            <p class="text-sm text-gray-400 mt-1">Contact: <?php echo esc($exhibitor['contact_person']); ?></p>
          <?php endif; ?>

          <div class="mt-3 flex flex-col items-center gap-1 text-xs text-gray-400">
            <?php if (!empty($exhibitor['email'])): ?>
              <a href="mailto:<?php echo esc($exhibitor['email']); ?>" class="hover:text-blue-400">
                <i class="bx bx-envelope mr-1"></i><?php echo esc($exhibitor['email']); ?>
              </a>
            <?php endif; ?>
            <?php if (!empty($exhibitor['telephone'])): ?>
              <p><i class="bx bx-phone mr-1"></i><?php echo esc($exhibitor['telephone']); ?></p>
            <?php endif; ?>
            <?php if (!empty($exhibitor['website'])): ?>
              <a href="<?php echo esc($exhibitor['website']); ?>" target="_blank" class="hover:text-blue-400">
                <i class="bx bx-globe mr-1"></i>Visit Site
              </a>
            <?php endif; ?>
          </div>

          <div class="mt-4 text-gray-300 text-sm line-clamp-3">
            <?php echo esc(substr($exhibitor['profile_summary'], 0, 100)); ?>...
          </div>

          <?php if (!empty($exhibitor['vimeo_id'])): ?>
            <div class="mt-4">
              <button type="button"
                      class="text-blue-400 hover:text-blue-300 text-sm underline"
                      data-vimeo="<?php echo esc($exhibitor['vimeo_id']); ?>">
                View Video
              </button>

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
            </div>
          <?php endif; ?>

          <div class="mt-5 flex justify-center gap-3">
            <a href="<?php echo site_url('admin/exhibitors/' . $exhibitor['id'] . '/edit'); ?>"
               class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Edit</a>
            <a href="<?php echo site_url('admin/exhibitors/' . $exhibitor['id'] . '/delete'); ?>"
               class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded"
               onclick="return confirm('Are you sure you want to delete this exhibitor?');">Delete</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
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
      }, 50000); // Auto close after 5 seconds
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
