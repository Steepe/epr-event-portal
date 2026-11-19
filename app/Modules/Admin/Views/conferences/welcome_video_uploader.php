<script src="https://cdn.jsdelivr.net/npm/tus-js-client@4.3.1/dist/tus.min.js"></script>
<script>
(() => {
    const form = document.querySelector('form[data-bunny-welcome-video-form]');
    if (!form) return;

    const input = form.querySelector('input[name="welcome_video"]');
    const videoId = form.querySelector('input[name="welcome_video_id"]');
    const credentialsUrl = form.dataset.bunnyCredentialsUrl;
    const uploadKey = (file) => `epr-bunny-welcome:${file.name}:${file.size}:${file.lastModified}`;
    const panel = () => document.getElementById('admin-upload-progress');
    const setStatus = (label, value, detail) => {
        const progressPanel = panel();
        if (!progressPanel) return;

        progressPanel.hidden = false;
        progressPanel.style.display = 'flex';
        document.getElementById('admin-upload-status').textContent = label;
        document.getElementById('admin-upload-percent').textContent = `${value}%`;
        document.getElementById('admin-upload-detail').textContent = detail;
        const fill = document.getElementById('admin-upload-fill');
        fill.style.width = `${value}%`;
        fill.setAttribute('aria-valuenow', String(value));
    };

    const showError = (message) => {
        setStatus('Upload failed.', 0, message);
        window.alert(message);
    };

    form.addEventListener('submit', async (event) => {
        const file = input.files && input.files[0];
        if (!file || videoId.value || form.dataset.directUploadActive === 'true') return;

        event.preventDefault();
        if (!window.tus) {
            showError('The direct Bunny uploader could not load. Please refresh the page and try again.');
            return;
        }

        const title = form.querySelector('input[name="title"]')?.value.trim();
        if (!title) {
            showError('Enter the conference name before uploading its welcome video.');
            return;
        }

        form.dataset.directUploadActive = 'true';
        const key = uploadKey(file);
        const requestData = new FormData();
        requestData.append('title', title);
        requestData.append('resume_video_id', localStorage.getItem(key) || '');
        const csrf = form.querySelector('input[name^="csrf"]');
        if (csrf) requestData.append(csrf.name, csrf.value);

        setStatus('Preparing secure Bunny upload…', 0, 'The video will upload directly to Bunny Stream and can resume after a connection interruption.');

        try {
            const response = await fetch(credentialsUrl, {
                method: 'POST',
                body: requestData,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                credentials: 'same-origin',
            });
            const credentials = await response.json();
            if (!response.ok) throw new Error(credentials.error || 'Could not prepare the Bunny upload.');

            localStorage.setItem(key, credentials.videoId);
            const upload = new window.tus.Upload(file, {
                endpoint: 'https://video.bunnycdn.com/tusupload',
                retryDelays: [0, 3000, 5000, 10000, 20000, 60000],
                headers: {
                    AuthorizationSignature: credentials.signature,
                    AuthorizationExpire: String(credentials.expirationTime),
                    VideoId: credentials.videoId,
                    LibraryId: credentials.libraryId,
                },
                metadata: {filetype: file.type || 'video/mp4', title: file.name},
                onProgress: (uploaded, total) => {
                    const value = total > 0 ? Math.round((uploaded / total) * 100) : 0;
                    setStatus('Uploading directly to Bunny Stream…', value, 'Keep this page open. The upload will retry and can resume if your connection drops.');
                },
                onError: (error) => {
                    form.dataset.directUploadActive = '';
                    showError(error.message || 'Bunny Stream could not upload this video.');
                },
                onSuccess: () => {
                    localStorage.removeItem(key);
                    videoId.value = credentials.videoId;
                    input.disabled = true;
                    setStatus('Video uploaded. Saving conference…', 100, 'Bunny is now encoding the video. It will play on Attendee Home when encoding finishes.');
                    form.submit();
                },
            });

            const previousUploads = await upload.findPreviousUploads();
            if (previousUploads.length && credentials.videoId === localStorage.getItem(key)) {
                upload.resumeFromPreviousUpload(previousUploads[0]);
            }
            upload.start();
        } catch (error) {
            form.dataset.directUploadActive = '';
            showError(error.message || 'Could not start the Bunny upload.');
        }
    });
})();
</script>
