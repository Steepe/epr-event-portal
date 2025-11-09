<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 03:34
 */

 $this->extend('App\Modules\Admin\Views\layout');
 $this->section('content');
?>

<div class="bg-gray-900 text-gray-200 p-6 rounded-lg shadow border border-gray-800">
  <h1 class="text-xl font-semibold mb-4">Edit Conference</h1>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="text-red-400 mb-3"><?php echo session()->getFlashdata('error'); ?></div>
  <?php endif; ?>

  <form action="<?php echo site_url('admin/conferences/' . $conference['conference_id'] . '/update'); ?>" method="post" class="space-y-4" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1">Conference Name</label>
        <input type="text" name="title" value="<?php echo esc($conference['title']); ?>"
               class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
      </div>

      <div>
        <label class="block text-sm mb-1">Year</label>
        <input type="number" name="year" value="<?php echo esc($conference['year']); ?>"
               class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
      </div>

      <div>
        <label class="block text-sm mb-1">Days</label>
        <input type="number" name="days" value="<?php echo esc($conference['days']); ?>"
               class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
      </div>

      <div>
        <label class="block text-sm mb-1">Paid?</label>
        <select name="is_paid" class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
          <option value="0" <?php echo ($conference['is_paid'] == 0) ? 'selected' : ''; ?>>No</option>
          <option value="1" <?php echo ($conference['is_paid'] == 1) ? 'selected' : ''; ?>>Yes</option>
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1">Status</label>
        <select name="status" class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
          <option value="live" <?php echo ($conference['status'] == 'live') ? 'selected' : ''; ?>>Live</option>
          <option value="past" <?php echo ($conference['status'] == 'past') ? 'selected' : ''; ?>>Past</option>
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1">Start Date</label>
        <input type="datetime-local" name="start_date" value="<?php echo date('Y-m-d\TH:i', strtotime($conference['start_date'])); ?>"
               class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
      </div>

      <div>
        <label class="block text-sm mb-1">End Date</label>
        <input type="datetime-local" name="end_date" value="<?php echo date('Y-m-d\TH:i', strtotime($conference['end_date'])); ?>"
               class="bg-gray-800 border border-gray-700 rounded w-full p-2.5">
      </div>
    </div>

    <div>
      <label class="block text-sm mb-1">Description</label>
      <textarea name="description" rows="4"
                class="bg-gray-800 border border-gray-700 rounded w-full p-2.5"><?php echo esc($conference['description']); ?></textarea>
    </div>

      <div>
          <label class="block text-sm mb-1">Conference Icon</label>
          <?php if (!empty($conference['icon'])): ?>
              <img src="<?php echo base_url($conference['icon']); ?>" alt="Conference Icon"
                   class="w-20 h-20 rounded mb-2 border border-gray-700 object-cover">
          <?php endif; ?>
          <input type="file" name="icon" accept="image/*"
                 class="block w-full text-sm text-gray-300 border border-gray-700 rounded cursor-pointer bg-gray-800 focus:outline-none file:bg-gray-700 file:text-gray-200 file:mr-4 file:px-3 file:py-2 file:rounded">
          <p class="text-xs text-gray-500 mt-1">Leave blank to keep current image</p>
      </div>


      <button type="submit"
            class="mt-4 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Update Conference</button>
  </form>
</div>

<?php $this->endSection(); ?>
