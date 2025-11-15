<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 15/11/2025
 * Time: 06:20
 */

$this->extend('App\Modules\Admin\Views\layout');
$this->section('content');
?>

<div class="max-w-5xl mx-auto bg-gray-800 border border-gray-700 rounded-lg p-8 shadow">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-100">Speaker Offers</h1>

        <a href="<?php echo site_url('admin/speakers/' . $speaker['speaker_id'] . '/offers/create'); ?>"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
            + Add Offer
        </a>
    </div>

    <!-- Speaker summary info -->
    <div class="mb-6 flex items-center gap-4">
        <img src="<?php echo base_url('uploads/speakers/' . $speaker['speaker_photo']); ?>"
             class="w-20 h-20 rounded object-cover"
             onerror="this.src='<?php echo base_url('assets/admin/img/placeholder-avatar.png'); ?>';">

        <div>
            <h2 class="text-xl text-gray-200 font-bold">
                <?php echo esc($speaker['speaker_name']); ?>
            </h2>
            <p class="text-gray-400 text-sm">
                <?php echo esc($speaker['speaker_title'] . ', ' . $speaker['speaker_company']); ?>
            </p>
        </div>
    </div>

    <?php if (empty($offers)): ?>
        <div class="text-center py-10 text-gray-400">
            No offers found. Click “Add Offer” to create one.
        </div>
    <?php else: ?>

        <table class="w-full text-left border border-gray-700 rounded overflow-hidden">
            <thead class="bg-gray-900 text-gray-300">
            <tr>
                <th class="p-3">Title</th>
                <th class="p-3">Price</th>
                <th class="p-3">CTA Link</th>
                <th class="p-3 text-right">Actions</th>
            </tr>
            </thead>

            <tbody class="text-gray-200">
            <?php foreach ($offers as $offer): ?>
                <tr class="border-b border-gray-700">
                    <td class="p-3">
                        <strong><?php echo esc($offer['title']); ?></strong><br>
                        <span class="text-sm text-gray-400"><?php echo esc($offer['summary']); ?></span>
                    </td>

                    <td class="p-3">
                        <?php echo esc($offer['price'] ?? '—'); ?>
                    </td>

                    <td class="p-3">
                        <?php if (!empty($offer['cta_link'])): ?>
                            <a href="<?php echo esc($offer['cta_link']); ?>" target="_blank"
                               class="text-blue-400 underline">
                                <?php echo esc($offer['cta_link']); ?>
                            </a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>

                    <td class="p-3 text-right">
                        <a href="<?php echo site_url('admin/speaker-offers/' . $offer['id'] . '/edit'); ?>"
                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm">
                            Edit
                        </a>

                        <a href="<?php echo site_url('admin/speaker-offers/' . $offer['id'] . '/delete'); ?>"
                           onclick="return confirm('Delete this offer?');"
                           class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm ml-2">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>

    <?php endif; ?>
</div>

<?php $this->endSection(); ?>
