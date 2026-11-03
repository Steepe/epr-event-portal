<?php
$isEdit = !empty($is_edit) && !empty($offer);
$action = $isEdit
    ? site_url('admin/speakers/offers/' . $offer['id'] . '/update')
    : site_url('admin/speakers/' . $speaker_id . '/offers/store');
$backUrl = site_url('admin/speakers/' . $speaker_id . '/offers');

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-3xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
  <div class="flex items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-gray-100"><?php echo $isEdit ? 'Edit Speaker Offer' : 'Add Speaker Offer'; ?></h1>
      <?php if (!empty($speaker)): ?>
        <p class="text-sm text-gray-400 mt-1">For <?php echo esc($speaker['speaker_name']); ?></p>
      <?php endif; ?>
    </div>
    <a href="<?php echo $backUrl; ?>" class="text-sm text-blue-300 hover:text-blue-200 hover:underline">Back to offers</a>
  </div>

  <form method="post" action="<?php echo $action; ?>" class="space-y-5">
    <?php echo csrf_field(); ?>

    <div>
      <label for="title" class="block text-sm text-gray-300 mb-1">Offer title</label>
      <input id="title" name="title" required value="<?php echo esc($offer['title'] ?? old('title')); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-100 rounded focus:border-blue-500 focus:outline-none">
    </div>

    <div>
      <label for="summary" class="block text-sm text-gray-300 mb-1">Summary</label>
      <textarea id="summary" name="summary" rows="5"
                class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-100 rounded focus:border-blue-500 focus:outline-none"><?php echo esc($offer['summary'] ?? old('summary')); ?></textarea>
    </div>

    <div>
      <label for="price" class="block text-sm text-gray-300 mb-1">Price</label>
      <input id="price" name="price" type="number" min="0" step="0.01" value="<?php echo esc($offer['price'] ?? old('price')); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-100 rounded focus:border-blue-500 focus:outline-none">
    </div>

    <div>
      <label for="cta_link" class="block text-sm text-gray-300 mb-1">Call-to-action link</label>
      <input id="cta_link" name="cta_link" type="url" placeholder="https://..." value="<?php echo esc($offer['cta_link'] ?? old('cta_link')); ?>"
             class="w-full p-2.5 bg-gray-900 border border-gray-700 text-gray-100 rounded focus:border-blue-500 focus:outline-none">
    </div>

    <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded">
      <?php echo $isEdit ? 'Update Offer' : 'Create Offer'; ?>
    </button>
  </form>
</div>

<?php $this->endSection(); ?>
