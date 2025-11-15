<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 06:28
 */

 $this->extend('App\Modules\Admin\Views\layout');
 $this->section('content');
 ?>

<div class="mb-6 flex items-center justify-between">
  <h1 class="text-2xl font-semibold text-gray-200">Speakers</h1>
  <a href="<?php echo site_url('admin/speakers/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">+ Add Speaker</a>
</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach ($speakers as $sp): ?>
    <div class="bg-gray-800 border border-gray-700 rounded-lg shadow hover:shadow-lg transition">
      <?php if ($sp['speaker_photo']): ?>
        <img src="<?php echo base_url('uploads/speakers/' . $sp['speaker_photo']); ?>" class="w-50 h-48 object-cover rounded-t-lg">
      <?php endif; ?>
      <div class="p-5">
        <h3 class="text-lg font-semibold text-gray-100"><?php echo esc($sp['speaker_name']); ?></h3>
        <p class="text-gray-400 text-sm"><?php echo esc($sp['speaker_title']); ?></p>
        <p class="text-gray-500 text-xs mb-2"><?php echo esc($sp['speaker_company']); ?></p>
        <p class="text-gray-300 text-sm line-clamp-3"><?php echo esc(substr($sp['bio'], 0, 120)); ?>...</p>
        <div class="mt-4 flex justify-end gap-2">
            <a href="<?php echo base_url('admin/speakers/' . $sp['speaker_id'] . '/offers'); ?>"
               class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs">
                Offers
            </a>
            <a href="<?php echo site_url('admin/speakers/' . $sp['speaker_id'] . '/edit'); ?>"
             class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">Edit</a>
          <a href="<?php echo site_url('admin/speakers/' . $sp['speaker_id'] . '/delete'); ?>"
             class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded"
             onclick="return confirm('Delete this speaker?');">Delete</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php $this->endSection(); ?>
