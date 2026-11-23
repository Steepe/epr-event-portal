<div class="rounded border border-gray-700 bg-gray-900/50 p-4">
  <h2 class="text-sm font-semibold text-gray-100">Session replay</h2>
  <p class="mt-1 text-xs text-gray-400">After this session ends, its replay replaces the live player on the attendee session page. Replay access is granted through the selected Store product.</p>
  <?php if (! $replayFieldsReady): ?>
    <p class="mt-3 text-sm text-amber-300">Replay fields are not ready. Run the pending session replay migration before adding a video.</p>
  <?php else: ?>
    <input type="hidden" name="replay_video_id" value="">
    <div class="mt-4 grid gap-4 md:grid-cols-2">
      <div class="md:col-span-2">
        <label class="block text-sm text-gray-300 mb-2" for="replay_video_file">Upload replay video to Bunny Stream</label>
        <input id="replay_video_file" type="file" name="replay_video_file" accept="video/mp4,video/quicktime,video/webm,.m4v" class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
        <p class="mt-1 text-xs text-gray-500">The file uploads directly from this browser to Bunny Stream. Keep this tab open until the progress reaches 100%.</p>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm text-gray-300 mb-2" for="replay_bunny_embed_url">Bunny Stream replay URL</label>
        <input id="replay_bunny_embed_url" type="url" name="replay_bunny_embed_url" value="<?php echo esc($session['replay_bunny_embed_url'] ?? '', 'attr'); ?>" placeholder="https://iframe.mediadelivery.net/play/542640/video-id" class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
        <p class="mt-1 text-xs text-gray-500">Paste a Bunny play or embed link. It will be saved as an iframe embed link. Clear this field to remove the replay.</p>
      </div>
      <div>
        <label class="block text-sm text-gray-300 mb-2" for="replay_product_id">Replay access product</label>
        <select id="replay_product_id" name="replay_product_id" class="w-full p-2 rounded bg-gray-900 border border-gray-700 text-gray-200">
          <option value="">Choose replay product</option>
          <?php foreach ($replayProducts as $product): ?>
            <option value="<?php echo (int) $product['product_id']; ?>" <?php echo (int) ($session['replay_product_id'] ?? 0) === (int) $product['product_id'] ? 'selected' : ''; ?>><?php echo esc($product['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div data-replay-upload-progress hidden role="status" aria-live="polite" class="mt-4 rounded border border-blue-700 bg-gray-900 p-3 text-sm text-gray-200">
      <div class="flex justify-between gap-2"><span data-replay-upload-label>Preparing upload…</span><strong data-replay-upload-percent>0%</strong></div>
      <div class="mt-2 h-2 overflow-hidden rounded bg-gray-700"><div data-replay-upload-fill class="h-full bg-blue-500" style="width:0%"></div></div>
      <p data-replay-upload-detail class="mt-2 text-xs text-gray-400"></p>
    </div>
  <?php endif; ?>
</div>
