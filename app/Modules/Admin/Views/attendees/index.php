<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:48
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Attendees</h1>
    <p class="text-sm text-gray-400">Manage all registered attendees</p>
  </div>
  <button data-modal-target="groupUploadModal" data-modal-toggle="groupUploadModal"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium">
    + Group Upload
  </button>
</div>

<!-- Alerts -->
<?php if (session()->getFlashdata('error')): ?>
  <div class="mb-4 p-3 border border-red-600 text-red-400 bg-gray-800 rounded">
    <?php echo session()->getFlashdata('error'); ?>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
  <div class="mb-4 p-3 border border-green-600 text-green-400 bg-gray-800 rounded">
    <?php echo session()->getFlashdata('success'); ?>
  </div>
<?php endif; ?>

<!-- Table -->
<div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-700 bg-gray-900">
  <table class="w-full text-sm text-left text-gray-300">
    <thead class="uppercase bg-gray-800 text-gray-400 text-xs">
      <tr>
        <th class="px-4 py-3">First Name</th>
        <th class="px-4 py-3">Last Name</th>
        <th class="px-4 py-3">Email</th>
        <th class="px-4 py-3">Telephone</th>
        <th class="px-4 py-3">Country</th>
        <th class="px-4 py-3">City</th>
        <th class="px-4 py-3">State</th>
        <th class="px-4 py-3">Reg. Date</th>
        <th class="px-4 py-3 text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($attendees)): ?>
        <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500">No attendees found.</td></tr>
      <?php else: ?>
        <?php foreach ($attendees as $attendee): ?>
          <tr class="border-t border-gray-700 hover:bg-gray-800">
            <td class="px-4 py-3"><?php echo esc($attendee['firstname']); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['lastname']); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['useremail'] ?? ''); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['telephone'] ?? ''); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['country'] ?? ''); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['city'] ?? ''); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['state'] ?? ''); ?></td>
            <td class="px-4 py-3"><?php echo esc($attendee['registration_timestamp'] ?? ''); ?></td>
            <td class="px-4 py-3 text-center">
              <div class="flex justify-center gap-3">
                <button data-modal-target="editModal_<?php echo $attendee['id']; ?>"
                        data-modal-toggle="editModal_<?php echo $attendee['id']; ?>"
                        class="text-blue-400 hover:text-blue-300">
                  <i class="bx bx-edit-alt text-lg"></i>
                </button>

                <button type="button"
                        onclick="deleteAttendee(event, '<?php echo $attendee['id']; ?>')"
                        class="text-red-500 hover:text-red-300">
                  <i class="bx bx-trash text-lg"></i>
                </button>
              </div>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div id="editModal_<?php echo $attendee['id']; ?>" tabindex="-1" aria-hidden="true"
               class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <div class="relative bg-gray-800 rounded-lg shadow border border-gray-700">
                <div class="flex items-center justify-between p-4 border-b border-gray-700">
                  <h3 class="text-lg font-semibold text-gray-200">Edit Attendee</h3>
                  <button type="button" class="text-gray-400 hover:text-gray-100"
                          data-modal-hide="editModal_<?php echo $attendee['id']; ?>">✕</button>
                </div>
                <div class="p-6 space-y-4">
                  <form method="post" class="edit_attendee"
                        action="<?php echo base_url('admin/attendees/' . $attendee['id'] . '/update'); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block mb-1 text-sm text-gray-400">First Name</label>
                        <input type="text" name="firstname" value="<?php echo esc($attendee['firstname']); ?>"
                               class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5" required>
                      </div>
                      <div>
                        <label class="block mb-1 text-sm text-gray-400">Last Name</label>
                        <input type="text" name="lastname" value="<?php echo esc($attendee['lastname']); ?>"
                               class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5" required>
                      </div>
                      <div>
                        <label class="block mb-1 text-sm text-gray-400">Telephone</label>
                        <input type="text" name="telephone" value="<?php echo esc($attendee['telephone'] ?? ''); ?>"
                               class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5">
                      </div>
                      <div>
                        <label class="block mb-1 text-sm text-gray-400">Country</label>
                        <input type="text" name="country" value="<?php echo esc($attendee['country'] ?? ''); ?>"
                               class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5">
                      </div>
                      <div>
                        <label class="block mb-1 text-sm text-gray-400">City</label>
                        <input type="text" name="city" value="<?php echo esc($attendee['city'] ?? ''); ?>"
                               class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5">
                      </div>
                      <div>
                        <label class="block mb-1 text-sm text-gray-400">State</label>
                        <input type="text" name="state" value="<?php echo esc($attendee['state'] ?? ''); ?>"
                               class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5">
                      </div>
                    </div>

                    <div class="flex justify-end mt-6 space-x-2">
                      <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                        Save
                      </button>
                      <button type="button" data-modal-hide="editModal_<?php echo $attendee['id']; ?>"
                              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                        Close
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Group Upload Modal -->
<div id="groupUploadModal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
  <div class="relative p-4 w-full max-w-2xl max-h-full">
    <div class="relative bg-gray-800 rounded-lg shadow border border-gray-700">
      <div class="flex items-center justify-between p-4 border-b border-gray-700">
        <h3 class="text-lg font-semibold text-gray-200">Group Upload</h3>
        <button type="button" class="text-gray-400 hover:text-gray-100" data-modal-hide="groupUploadModal">✕</button>
      </div>
      <div class="p-6 space-y-4">
        <form method="post" id="group_upload" action="<?php echo base_url('admin/group_upload/upload_excel'); ?>" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label class="block mb-1 text-sm text-gray-400">Choose Excel Sheet</label>
            <input type="file" name="excel_sheet"
                   class="bg-gray-900 border border-gray-700 text-gray-200 text-sm rounded w-full p-2.5" required>
          </div>
          <div class="flex justify-end mt-6 space-x-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">Upload</button>
            <button type="reset" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded text-sm">Clear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php $this->endSection(); ?>
