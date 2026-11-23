<script src="https://cdn.jsdelivr.net/npm/tus-js-client@4.3.1/dist/tus.min.js"></script>
<script>
(() => {
  const form = document.querySelector('form[data-session-replay-upload]');
  if (!form) return;
  const fileInput = form.querySelector('input[name="replay_video_file"]');
  const videoId = form.querySelector('input[name="replay_video_id"]');
  const product = form.querySelector('select[name="replay_product_id"]');
  const progress = form.querySelector('[data-replay-upload-progress]');
  if (!fileInput || !videoId || !progress) return;

  const status = (label, percentage, detail, failed = false) => {
    progress.hidden = false;
    progress.classList.toggle('border-red-700', failed);
    progress.querySelector('[data-replay-upload-label]').textContent = label;
    progress.querySelector('[data-replay-upload-percent]').textContent = percentage + '%';
    progress.querySelector('[data-replay-upload-fill]').style.width = percentage + '%';
    progress.querySelector('[data-replay-upload-detail]').textContent = detail;
  };

  form.addEventListener('submit', async (event) => {
    const file = fileInput.files?.[0];
    if (!file || videoId.value) return;
    event.preventDefault();
    if (form.dataset.uploading === '1') return;
    if (!window.tus) {
      status('Upload unavailable', 0, 'The uploader could not load. Refresh and try again, or paste a Bunny embed URL.', true);
      return;
    }
    if (!product?.value) {
      status('Choose replay access', 0, 'Select the replay product before uploading.', true);
      product?.focus();
      return;
    }
    if (!/^video\/(mp4|quicktime|webm)$/.test(file.type) && !/\.(mp4|m4v|mov|webm)$/i.test(file.name)) {
      status('Unsupported file', 0, 'Choose an MP4, M4V, MOV, or WebM recording.', true);
      return;
    }

    form.dataset.uploading = '1';
    const key = `epr-session-replay:${form.action}:${file.name}:${file.size}:${file.lastModified}`;
    const data = new FormData();
    data.append('title', form.querySelector('input[name="sessions_name"]')?.value.trim() || file.name);
    data.append('resume_video_id', localStorage.getItem(key) || '');
    const csrf = form.querySelector('input[name^="csrf"]');
    if (csrf) data.append(csrf.name, csrf.value);
    status('Preparing Bunny upload', 0, 'The recording will upload directly from your browser.');

    try {
      const response = await fetch(form.dataset.replayCredentialsUrl, {
        method: 'POST', body: data, credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      const credentials = await response.json();
      if (csrf && credentials.csrfHash) csrf.value = credentials.csrfHash;
      if (!response.ok) throw new Error(credentials.error || 'Could not prepare the upload.');
      localStorage.setItem(key, credentials.videoId);

      const upload = new window.tus.Upload(file, {
        endpoint: 'https://video.bunnycdn.com/tusupload',
        retryDelays: [0, 3000, 5000, 10000, 20000, 60000],
        headers: {
          AuthorizationSignature: credentials.signature,
          AuthorizationExpire: String(credentials.expirationTime),
          VideoId: credentials.videoId,
          LibraryId: credentials.libraryId
        },
        metadata: {filetype: file.type || 'video/mp4', title: file.name},
        onProgress: (uploaded, total) => status('Uploading to Bunny Stream', total ? Math.round(uploaded / total * 100) : 0, 'Keep this tab open until the session is saved.'),
        onError: error => {
          form.dataset.uploading = '';
          status('Upload failed', 0, error.message || 'Please retry the recording upload.', true);
        },
        onSuccess: () => {
          localStorage.removeItem(key);
          videoId.value = credentials.videoId;
          fileInput.disabled = true;
          status('Upload complete', 100, 'Saving the session. Bunny may need time to encode the recording.');
          form.requestSubmit();
        }
      });
      const previous = await upload.findPreviousUploads();
      if (previous.length) upload.resumeFromPreviousUpload(previous[0]);
      upload.start();
    } catch (error) {
      form.dataset.uploading = '';
      status('Upload failed', 0, error.message || 'Could not start the Bunny upload.', true);
    }
  });
})();
</script>
