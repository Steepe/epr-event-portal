<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:14
 */

$this->extend('App\Modules\Admin\Views\layout');

 $this->section('content');
 ?>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" style="color: #0b0b0b !important;">
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm">Users</div>
      <div class="text-2xl font-bold"><?php echo isset($users_count) ? (int)$users_count : 0; ?></div>
    </div>
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm">Conferences</div>
      <div class="text-2xl font-bold"><?php echo isset($conferences_count) ? (int)$conferences_count : 0; ?></div>
    </div>
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm">Sessions</div>
      <div class="text-2xl font-bold"><?php echo isset($sessions_count) ? (int)$sessions_count : 0; ?></div>
    </div>
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm">Unread Messages</div>
      <div class="text-2xl font-bold"><?php echo isset($unread_messages) ? (int)$unread_messages : 0; ?></div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4" style="color: #0b0b0b !important;">
    <section class="col-span-2 bg-white p-4 rounded shadow">
      <h2 class="text-lg font-semibold mb-3">Recent Activity</h2>
      <p class="text-sm text-gray-600">Recent registrations, payments and session changes will appear here.</p>
    </section>

    <aside class="bg-white p-4 rounded shadow">
      <h3 class="font-semibold mb-2">Quick Actions</h3>
      <div class="flex flex-col gap-2">
        <a class="py-2 px-3 border rounded hover:bg-gray-50" href="<?php echo site_url('admin/users'); ?>">Manage Users</a>
        <a class="py-2 px-3 border rounded hover:bg-gray-50" href="<?php echo site_url('admin/conferences'); ?>">Manage Conferences</a>
      </div>
    </aside>
  </div>
<?php $this->endSection(); ?>
