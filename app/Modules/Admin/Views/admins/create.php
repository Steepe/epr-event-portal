<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 17:19
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="mb-6 flex items-center justify-between">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Add New Admin</h1>
    <p class="text-sm text-gray-400">Only Superadmins can create new admin users.</p>
  </div>
  <a href="<?php echo site_url('admin/admins'); ?>"
     class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-white text-sm">← Back</a>
</div>

<form method="post" action="<?php echo site_url('admin/admins/store'); ?>" class="bg-gray-800 border border-gray-700 p-6 rounded-lg space-y-6">
  <?php echo csrf_field(); ?>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Full Name</label>
      <input type="text" name="name" required class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Email</label>
      <input type="email" name="email" required class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm text-gray-400 mb-1">Password</label>
      <input type="password" name="password" required class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
    </div>
    <div>
      <label class="block text-sm text-gray-400 mb-1">Role</label>
      <select name="role" class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-200 rounded">
        <option value="manager">Manager</option>
        <option value="support">Support</option>
        <option value="superadmin">Superadmin</option>
      </select>
    </div>
  </div>

  <div class="flex justify-end">
    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">
      Create Admin
    </button>
  </div>
</form>

<?php $this->endSection(); ?>
