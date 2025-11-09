<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 15:35
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Sponsors</h1>
    <p class="text-sm text-gray-400">Manage all conference sponsors</p>
  </div>
  <a href="<?php echo site_url('admin/sponsors/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">+ Add Sponsor</a>
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
  <?php if (empty($sponsors)): ?>
    <p class="text-gray-400">No sponsors found.</p>
  <?php else: ?>
    <?php foreach ($sponsors as $sponsor): ?>
      <div class="bg-gray-800 border border-gray-700 rounded-lg shadow hover:shadow-lg transition p-5 text-center flex flex-col items-center">
        <?php if (!empty($sponsor['logo']) && file_exists(FCPATH . 'uploads/sponsors/' . $sponsor['logo'])): ?>
          <img src="<?php echo base_url('uploads/sponsors/' . $sponsor['logo']); ?>"
               alt="<?php echo esc($sponsor['name']); ?>"
               class="w-24 h-24 object-contain rounded-md border border-gray-700 mb-3 bg-gray-900 p-1">
        <?php else: ?>
          <div class="w-24 h-24 flex items-center justify-center bg-gray-700 text-gray-400 rounded-md border border-gray-700 mb-3">
            <i class="bx bx-image-alt text-3xl"></i>
          </div>
        <?php endif; ?>

        <h2 class="text-lg font-semibold text-gray-100"><?php echo esc($sponsor['name']); ?></h2>
        <span class="text-xs px-2 py-1 mt-1 rounded-full
              <?php echo match($sponsor['tier']) {
                  'Platinum' => 'bg-yellow-700 text-yellow-300',
                  'Gold'     => 'bg-amber-700 text-amber-300',
                  'Silver'   => 'bg-gray-600 text-gray-200',
                  'Bronze'   => 'bg-orange-700 text-orange-300',
                  default    => 'bg-gray-700 text-gray-300'
              }; ?>">
          <?php echo esc($sponsor['tier']); ?>
        </span>

        <?php if ($sponsor['is_featured']): ?>
          <p class="mt-1 text-blue-400 text-xs uppercase font-semibold tracking-wide">Featured</p>
        <?php endif; ?>

        <?php if (!empty($sponsor['website'])): ?>
          <a href="<?php echo esc($sponsor['website']); ?>" target="_blank"
             class="mt-2 text-sm text-blue-400 hover:underline">Visit Website</a>
        <?php endif; ?>

        <?php if (!empty($sponsor['description'])): ?>
          <p class="mt-3 text-gray-400 text-sm line-clamp-3">
            <?php echo esc(substr($sponsor['description'], 0, 120)); ?>...
          </p>
        <?php endif; ?>

        <div class="mt-5 flex justify-center gap-3">
          <a href="<?php echo site_url('admin/sponsors/' . $sponsor['id'] . '/edit'); ?>"
             class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Edit</a>
          <a href="<?php echo site_url('admin/sponsors/' . $sponsor['id'] . '/delete'); ?>"
             class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded"
             onclick="return confirm('Delete this sponsor?');">Delete</a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php $this->endSection(); ?>
