<?php $this->extend('App\Modules\Admin\Views\layout'); ?>
<?php $this->section('content'); ?>

<div class="mb-6">
  <h1 class="text-2xl font-semibold text-gray-100">Portal Messages</h1>
  <p class="mt-1 text-sm text-gray-400">Send a portal message and an email notification to one attendee, a selected group, or all registrants.</p>
</div>

<?php if (session()->getFlashdata('success')): ?><div class="mb-4 rounded border border-green-700 bg-green-950/40 p-3 text-sm text-green-300"><?php echo esc(session()->getFlashdata('success')); ?></div><?php endif; ?>
<?php if (session()->getFlashdata('error')): ?><div class="mb-4 rounded border border-red-700 bg-red-950/40 p-3 text-sm text-red-300"><?php echo esc(session()->getFlashdata('error')); ?></div><?php endif; ?>

<?php if (empty($ready)): ?>
  <div class="rounded-lg border border-amber-600 bg-amber-950/30 p-5 text-amber-100"><strong>Admin Messages is not ready yet.</strong><p class="mt-1 text-sm">Deploy this release, then run the pending migration when you are ready. No messages can be sent until the two message tables exist.</p></div>
<?php else: ?>
  <form method="post" action="<?php echo site_url('admin/messages/send'); ?>" class="rounded-lg border border-gray-700 bg-gray-800 p-5">
    <?php echo csrf_field(); ?>
    <div class="grid gap-4 lg:grid-cols-2">
      <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Send to</span>
        <select name="recipient_type" id="recipient_type" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100" required>
          <option value="individual" <?php echo old('recipient_type') === 'individual' ? 'selected' : ''; ?>>One attendee</option>
          <option value="filtered" <?php echo old('recipient_type') === 'filtered' ? 'selected' : ''; ?>>A filtered group</option>
          <option value="emergence_2026" <?php echo old('recipient_type') === 'emergence_2026' ? 'selected' : ''; ?>>All Emergence 2026 registrants</option>
          <option value="all" <?php echo old('recipient_type') === 'all' ? 'selected' : ''; ?>>Every portal attendee</option>
        </select>
      </label>
      <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Subject</span><input name="subject" maxlength="255" required value="<?php echo esc(old('subject')); ?>" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100" placeholder="Message subject"></label>
    </div>

    <div id="individual-fields" class="mt-4"><label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Attendee email address</span><input name="recipient_email" type="email" value="<?php echo esc(old('recipient_email')); ?>" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100" placeholder="name@example.com"></label></div>
    <div id="filtered-fields" class="mt-4 hidden grid gap-4 md:grid-cols-2">
      <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Email address contains</span><input name="email" type="search" value="<?php echo esc(old('email')); ?>" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100" placeholder="e.g. company.com"></label>
      <label class="block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Country</span><select name="country" class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100"><option value="">All countries</option><?php foreach (($countries ?? []) as $country): ?><option value="<?php echo esc($country); ?>" <?php echo old('country') === $country ? 'selected' : ''; ?>><?php echo esc($country); ?></option><?php endforeach; ?></select></label>
      <p class="text-xs text-gray-400 md:col-span-2">Use at least one filter. Every attendee matching the filters will receive the message.</p>
    </div>
    <label id="all-confirmation" class="mt-4 hidden items-center gap-2 text-sm text-amber-200"><input type="checkbox" name="confirm_all" value="1" <?php echo old('confirm_all') === '1' ? 'checked' : ''; ?>> I understand this will email every portal attendee.</label>
    <label class="mt-4 block text-sm text-gray-300"><span class="mb-1 block text-gray-400">Message</span><textarea name="body" rows="8" maxlength="10000" required class="w-full rounded border border-gray-700 bg-gray-900 p-2.5 text-gray-100" placeholder="Write your message to attendees..."><?php echo esc(old('body')); ?></textarea></label>
    <div class="mt-4 flex flex-wrap items-center gap-3"><button class="rounded bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-500" onclick="return confirm('Send this portal message and email notification to the selected recipients?');">Send message and email notification</button><span class="text-xs text-gray-400">Each email delivery result is recorded below.</span></div>
  </form>

  <section class="mt-6 rounded-lg border border-gray-700 bg-gray-800 p-5">
    <h2 class="text-lg font-semibold text-gray-100">Delivery history</h2>
    <p class="mt-1 text-sm text-gray-400">The most recent 30 message sends. Failed emails remain visible for follow-up.</p>
    <div class="mt-4 overflow-x-auto"><table class="min-w-full text-left text-sm"><thead class="border-b border-gray-700 text-xs uppercase text-gray-400"><tr><th class="p-3">Sent</th><th class="p-3">Subject</th><th class="p-3">Audience</th><th class="p-3">By</th><th class="p-3">Email delivery</th></tr></thead><tbody>
      <?php foreach (($messages ?? []) as $message): ?><tr class="border-b border-gray-800 align-top"><td class="p-3 whitespace-nowrap text-gray-400"><?php echo esc($message['created_at']); ?></td><td class="p-3"><strong class="text-gray-100"><?php echo esc($message['subject']); ?></strong><p class="mt-1 max-w-xl whitespace-pre-line text-xs text-gray-400"><?php echo esc(mb_substr($message['body'], 0, 180)); ?><?php echo mb_strlen($message['body']) > 180 ? '…' : ''; ?></p></td><td class="p-3 text-gray-300"><?php echo esc(ucwords(str_replace('_', ' ', $message['recipient_type']))); ?><br><span class="text-xs text-gray-500"><?php echo (int) $message['recipient_count']; ?> recipient(s)</span></td><td class="p-3 text-gray-300"><?php echo esc($message['sender_name'] ?: 'Unknown'); ?></td><td class="p-3"><span class="text-green-400"><?php echo (int) ($message['sent_count'] ?? 0); ?> sent</span><?php if ((int) ($message['failed_count'] ?? 0) > 0): ?><br><span class="text-red-400"><?php echo (int) $message['failed_count']; ?> failed</span><?php endif; ?></td></tr><?php endforeach; ?>
      <?php if (($messages ?? []) === []): ?><tr><td colspan="5" class="p-6 text-center text-gray-400">No administrator messages have been sent yet.</td></tr><?php endif; ?>
    </tbody></table></div>
  </section>
  <script>
    const recipientType = document.getElementById('recipient_type');
    const setRecipientFields = () => {
      const value = recipientType.value;
      document.getElementById('individual-fields').classList.toggle('hidden', value !== 'individual');
      document.getElementById('filtered-fields').classList.toggle('hidden', value !== 'filtered');
      const confirmation = document.getElementById('all-confirmation');
      confirmation.classList.toggle('hidden', value !== 'all');
      confirmation.classList.toggle('flex', value === 'all');
    };
    recipientType.addEventListener('change', setRecipientFields); setRecipientFields();
  </script>
<?php endif; ?>

<?php $this->endSection(); ?>
