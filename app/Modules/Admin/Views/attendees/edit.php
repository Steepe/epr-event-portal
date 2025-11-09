<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:57
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<h1 class="text-2xl font-semibold text-gray-200 mb-6">Edit Attendee</h1>

<form method="post" action="<?php echo site_url('admin/attendees/' . $attendee['id'] . '/update'); ?>"
      class="bg-gray-800 border border-gray-700 rounded-lg shadow p-6 space-y-6 text-sm">
  <?php echo csrf_field(); ?>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label class="block mb-1 text-gray-400">First Name</label>
      <input type="text" name="firstname" value="<?php echo esc($attendee['firstname']); ?>"
             class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <div>
      <label class="block mb-1 text-gray-400">Last Name</label>
      <input type="text" name="lastname" value="<?php echo esc($attendee['lastname']); ?>"
             class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <div>
      <label class="block mb-1 text-gray-400">Country</label>
      <input type="text" name="country" value="<?php echo esc($attendee['country']); ?>"
             class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded p-2.5 focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
      <label class="block mb-1 text-gray-400">City</label>
      <input type="text" name="city" value="<?php echo esc($attendee['city']); ?>"
             class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded p-2.5 focus:ring-blue-500 focus:border-blue-500">
    </div>
    <div>
      <label class="block mb-1 text-gray-400">State</label>
      <input type="text" name="state" value="<?php echo esc($attendee['state']); ?>"
             class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded p-2.5 focus:ring-blue-500 focus:border-blue-500">
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Save</button>
  </div>
</form>

<?php $this->endSection(); ?>
