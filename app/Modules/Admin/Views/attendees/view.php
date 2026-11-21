<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:49
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Attendee Details</h1>
    <p class="text-sm text-gray-400">Profile for <?php echo esc($attendee['firstname'] . ' ' . $attendee['lastname']); ?></p>
  </div>
  <a href="<?php echo site_url('admin/attendees/' . $attendee['id'] . '/edit'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">Edit</a>
</div>

<div class="bg-gray-800 border border-gray-700 rounded-lg shadow p-6 text-sm">
  <dl class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-300">
    <div>
      <dt class="text-gray-400 mb-1">First Name</dt>
      <dd><?php echo esc($attendee['firstname']); ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">Last Name</dt>
      <dd><?php echo esc($attendee['lastname']); ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">Email</dt>
      <dd><?php echo esc($attendee['email']); ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">Country</dt>
      <dd><?php echo esc($attendee['country']); ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">City</dt>
      <dd><?php echo esc($attendee['city']); ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">Plan</dt>
      <dd><?php echo esc($attendee['plan'] ?? '—'); ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">Amount</dt>
      <dd><?php echo $attendee['amount'] ? '$' . $attendee['amount'] : '0'; ?></dd>
    </div>
    <div>
      <dt class="text-gray-400 mb-1">Payment Date</dt>
      <dd><?php echo esc($attendee['paymentdate'] ?? '—'); ?></dd>
    </div>
  </dl>
</div>

<section class="mt-6 rounded-lg border border-amber-700/60 bg-gray-800 p-5">
  <h2 class="font-semibold text-amber-200">Password reset</h2>
  <p class="mt-1 text-sm text-gray-400">Send a one-time password-creation link to <?php echo esc($attendee['email']); ?>. Once sent, their current password will no longer work and they must set a new password before they can sign in again.</p>
  <form method="post" action="<?php echo site_url('admin/attendees/' . $attendee['id'] . '/reset'); ?>" class="mt-4" onsubmit="return confirm('Send a password-creation email and invalidate this attendee’s current password?');">
    <?php echo csrf_field(); ?>
    <button type="submit" class="rounded bg-amber-500 px-4 py-2 text-sm font-medium text-gray-950 hover:bg-amber-400">Send password reset email</button>
  </form>
</section>

<?php $this->endSection(); ?>
