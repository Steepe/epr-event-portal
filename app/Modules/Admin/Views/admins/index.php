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

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Admin Users</h1>
    <p class="text-sm text-gray-400">Manage system administrators and support staff.</p>
  </div>
  <a href="<?php echo site_url('admin/admins/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">+ Add Admin</a>
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

<div class="overflow-x-auto rounded-lg border border-gray-700">
  <table class="w-full text-sm text-left text-gray-300">
    <thead class="uppercase bg-gray-800 text-gray-400 text-xs">
      <tr>
        <th class="px-4 py-3">Name</th>
        <th class="px-4 py-3">Email</th>
        <th class="px-4 py-3">Role</th>
        <th class="px-4 py-3">Status</th>
        <th class="px-4 py-3">Last Login</th>
        <th class="px-4 py-3 text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($admins)): ?>
        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No admins found.</td></tr>
      <?php else: ?>
        <?php foreach ($admins as $admin): ?>
          <tr class="border-t border-gray-700 hover:bg-gray-800">
            <td class="px-4 py-3"><?php echo esc($admin['name']); ?></td>
            <td class="px-4 py-3"><?php echo esc($admin['email']); ?></td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 text-xs rounded
                <?php echo match($admin['role']) {
                    'superadmin' => 'bg-purple-800/40 text-purple-300 border border-purple-700',
                    'manager' => 'bg-blue-800/40 text-blue-300 border border-blue-700',
                    default => 'bg-gray-700 text-gray-300 border border-gray-600'
                }; ?>">
                <?php echo ucfirst($admin['role']); ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 text-xs rounded <?php echo $admin['status'] === 'active' ? 'bg-green-800/40 text-green-300 border border-green-700' : 'bg-red-800/40 text-red-300 border border-red-700'; ?>">
                <?php echo ucfirst($admin['status']); ?>
              </span>
            </td>
            <td class="px-4 py-3"><?php echo $admin['last_login'] ?: '-'; ?></td>
            <td class="px-4 py-3 text-center space-x-2">
              <a href="<?php echo site_url('admin/admins/' . $admin['id'] . '/edit'); ?>"
                 class="text-blue-400 hover:text-blue-300"><i class="bx bx-edit text-lg"></i></a>
              <a href="<?php echo site_url('admin/admins/' . $admin['id'] . '/toggle'); ?>"
                 class="text-yellow-400 hover:text-yellow-300"
                 onclick="return confirm('Toggle status for this admin?');">
                 <i class="bx bx-refresh text-lg"></i></a>
              <a href="<?php echo site_url('admin/admins/' . $admin['id'] . '/delete'); ?>"
                 class="text-red-500 hover:text-red-300"
                 onclick="return confirm('Delete this admin?');"><i class="bx bx-trash text-lg"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php $this->endSection(); ?>
