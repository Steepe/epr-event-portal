<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 03:07
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold text-gray-200">Conferences</h1>
    <p class="text-sm text-gray-400">Manage all past, live, and upcoming conferences.</p>
  </div>
  <a href="<?php echo site_url('admin/conferences/create'); ?>"
     class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
    + New Conference
  </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="p-3 mb-4 text-green-400 bg-green-900/30 border border-green-700 rounded">
    <?php echo session()->getFlashdata('success'); ?>
  </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="p-3 mb-4 text-red-400 bg-red-900/30 border border-red-700 rounded">
    <?php echo session()->getFlashdata('error'); ?>
  </div>
<?php endif; ?>

<?php if (empty($conferences)): ?>
  <div class="text-center py-20 text-gray-500">No conferences found.</div>
<?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($conferences as $conf): ?>
      <?php
        $statusColors = [
          'live' => 'bg-green-700 text-green-100 border border-green-500',
          'upcoming' => 'bg-blue-700 text-blue-100 border border-blue-500',
          'past' => 'bg-gray-700 text-gray-300 border border-gray-500'
        ];
        $statusLabel = ucfirst($conf['status']);
        $statusClass = $statusColors[$conf['status']] ?? 'bg-gray-700 text-gray-300';
      ?>
      <div class="bg-gray-800 border border-gray-700 rounded-lg shadow hover:shadow-lg transition-shadow duration-200 overflow-hidden flex flex-col">
        <div class="h-40 flex items-center justify-center bg-gray-900">
          <?php if (!empty($conf['icon'])): ?>
            <img src="<?php echo base_url($conf['icon']); ?>" alt="<?php echo esc($conf['title']); ?>"
                 class="h-20 w-20 object-contain rounded">
          <?php else: ?>
            <div class="text-gray-500 text-sm">No Icon</div>
          <?php endif; ?>
        </div>

        <div class="p-4 flex flex-col justify-between flex-grow">
          <div>
            <h2 class="text-lg font-semibold text-gray-200 mb-1"><?php echo esc($conf['title']); ?></h2>
            <p class="text-sm text-gray-400 mb-3 line-clamp-2"><?php echo esc($conf['description']); ?></p>
          </div>

          <div class="text-sm text-gray-400 space-y-1">
            <div>
              <i class="bx bx-calendar text-gray-500"></i>
              <?php echo date('M d, Y', strtotime($conf['start_date'])); ?>
              –
              <?php echo date('M d, Y', strtotime($conf['end_date'])); ?>
            </div>
            <div>
              <i class="bx bx-time-five text-gray-500"></i>
              Duration:
              <?php echo $conf['days']; ?> day<?php echo $conf['days'] > 1 ? 's' : ''; ?>
            </div>
          </div>

          <div class="flex justify-between items-center mt-4">
            <span class="px-2 py-1 text-xs rounded <?php echo $statusClass; ?>">
              <?php echo $statusLabel; ?>
            </span>
              <a href="<?php echo site_url('admin/conferences/' . $conf['conference_id'] . '/sessions'); ?>"
                 class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-sm text-white rounded">
                  View Sessions
              </a>


              <div class="flex gap-2">
              <a href="<?php echo site_url('admin/conferences/' . $conf['conference_id'] . '/edit'); ?>"
                 class="px-3 py-1 text-xs bg-blue-700 hover:bg-blue-800 text-white rounded">
                Edit
              </a>
              <button onclick="deleteConference(event, '<?php echo $conf['conference_id']; ?>')"
                      class="px-3 py-1 text-xs bg-red-700 hover:bg-red-800 text-white rounded">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<script>
function deleteConference(e, id) {
  e.preventDefault();
  if (!confirm('Are you sure you want to delete this conference?')) return;

  fetch(`<?php echo site_url('admin/conferences'); ?>/${id}/delete`, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'deleted') {
      location.reload();
    } else {
      alert('Delete failed.');
    }
  })
  .catch(() => alert('Network error.'));
}
</script>

<?php $this->endSection(); ?>
