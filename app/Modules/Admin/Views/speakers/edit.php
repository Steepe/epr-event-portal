<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 07:21
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-4xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
  <h1 class="text-2xl font-semibold text-gray-100 mb-6">Edit Speaker</h1>

  <form method="post" enctype="multipart/form-data"
        action="<?php echo site_url('admin/speakers/' . $speaker['speaker_id'] . '/update'); ?>"
        class="space-y-6">

    <?php echo csrf_field(); ?>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm text-gray-400 mb-2">Name</label>
            <input type="text" name="speaker_name"
                   value="<?php echo esc($speaker['speaker_name']); ?>"
                   class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
        </div>
        <div>
            <label class="block text-sm text-gray-400 mb-2">Email</label>
            <input type="email" name="speaker_email"
                   value="<?php echo esc($speaker['speaker_email']); ?>"
                   class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
        </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Title</label>
        <input type="text" name="speaker_title"
               value="<?php echo esc($speaker['speaker_title']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Company</label>
        <input type="text" name="speaker_company"
               value="<?php echo esc($speaker['speaker_company']); ?>"
               class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
      </div>
    </div>

    <div>
      <label class="block text-sm text-gray-400 mb-2">Bio</label>
      <textarea name="bio" rows="5"
                class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200"><?php echo esc($speaker['bio']); ?></textarea>
    </div>

    <div class="flex items-center gap-6">
      <div class="w-32 h-32 rounded overflow-hidden border border-gray-700 bg-gray-900 flex items-center justify-center">
        <?php if (!empty($speaker['speaker_photo'])): ?>
          <img id="photoPreview" src="<?php echo base_url('uploads/speakers/' . $speaker['speaker_photo']); ?>"
               alt="Preview" class="object-cover w-full h-full">
        <?php else: ?>
          <span class="text-gray-500 text-sm">No photo</span>
        <?php endif; ?>
      </div>
      <div>
        <label class="block text-sm text-gray-400 mb-2">Change Photo</label>
        <input type="file" name="speaker_photo" accept="image/*"
               onchange="previewImage(event)"
               class="text-gray-300">
      </div>

        <!-- ============================
     SPEAKER OFFERS SECTION
============================= -->
        <div class="mt-10 bg-gray-900 border border-gray-700 rounded-lg p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-200">Offers & Deals</h2>
                <button type="button"
                        onclick="openOfferModal()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                    + Add Offer
                </button>
            </div>

            <?php if (!empty($offers)): ?>
                <table class="w-full text-left text-gray-300 text-sm border-separate border-spacing-y-2">
                    <thead>
                    <tr class="text-gray-400 text-xs">
                        <th class="py-2">Title</th>
                        <th class="py-2">Price</th>
                        <th class="py-2 w-32">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($offers as $offer): ?>
                        <tr class="bg-gray-800 border border-gray-700">
                            <td class="p-3"><?php echo esc($offer['title']); ?></td>
                            <td class="p-3"><?php echo esc($offer['price'] ?? '-'); ?></td>
                            <td class="p-3 flex gap-3">

                                <!-- Edit -->
                                <button type="button"
                                        onclick="editOffer(<?php echo $offer['id']; ?>)"
                                        class="text-blue-400 hover:underline">
                                    Edit
                                </button>

                                <!-- Delete -->
                                <a href="<?php echo site_url('admin/speakers/'.$speaker['speaker_id'].'/offers/'.$offer['id'].'/delete'); ?>"
                                   onclick="return confirm('Delete this offer?');"
                                   class="text-red-400 hover:underline">
                                    Delete
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-400 italic">No offers added for this speaker.</p>
            <?php endif; ?>

        </div>

    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Save Changes</button>
    </div>
  </form>
</div>

<script>
function previewImage(event) {
  const file = event.target.files[0];
  if (file) {
    const preview = document.getElementById('photoPreview');
    preview.src = URL.createObjectURL(file);
  }
}
</script>

<!-- OFFER MODAL -->
<div id="offerModal"
     class="fixed inset-0 bg-black bg-opacity-60 hidden justify-center items-center z-50">

    <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 w-full max-w-lg">
        <h2 id="offerModalTitle" class="text-xl font-semibold text-gray-200 mb-4">Add Offer</h2>

        <form id="offerForm" method="post" action="">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="offer_id" id="offer_id">

            <div class="mb-4">
                <label class="block text-gray-400 text-sm mb-1">Offer Title</label>
                <input type="text" name="title" id="offer_title"
                       required
                       class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
            </div>

            <div class="mb-4">
                <label class="block text-gray-400 text-sm mb-1">Summary</label>
                <textarea name="summary" id="offer_summary" rows="4"
                          class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Price</label>
                    <input type="text" name="price" id="offer_price"
                           class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
                </div>

                <div>
                    <label class="block text-gray-400 text-sm mb-1">CTA Link</label>
                    <input type="text" name="cta_link" id="offer_cta"
                           class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button" onclick="closeOfferModal()"
                        class="px-4 py-2 bg-gray-700 text-gray-200 rounded">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                    Save Offer
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function openOfferModal() {
        document.getElementById('offerForm').action =
            "<?php echo site_url('admin/speakers/'.$speaker['speaker_id'].'/offers/store'); ?>";

        document.getElementById('offer_id').value = "";
        document.getElementById('offer_title').value = "";
        document.getElementById('offer_summary').value = "";
        document.getElementById('offer_price').value = "";
        document.getElementById('offer_cta').value = "";

        document.getElementById('offerModalTitle').textContent = "Add Offer";

        document.getElementById('offerModal').classList.remove('hidden');
    }

    function editOffer(id) {
        fetch("<?php echo site_url('admin/speakers/'.$speaker['speaker_id'].'/offers'); ?>/" + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('offerForm').action =
                    "<?php echo site_url('admin/speakers/'.$speaker['speaker_id'].'/offers'); ?>/" + id + "/update";

                document.getElementById('offer_id').value = data.id;
                document.getElementById('offer_title').value = data.title;
                document.getElementById('offer_summary').value = data.summary;
                document.getElementById('offer_price').value = data.price;
                document.getElementById('offer_cta').value = data.cta_link;

                document.getElementById('offerModalTitle').textContent = "Edit Offer";

                document.getElementById('offerModal').classList.remove('hidden');
            });
    }

    function closeOfferModal() {
        document.getElementById('offerModal').classList.add('hidden');
    }
</script>


<?php $this->endSection(); ?>
