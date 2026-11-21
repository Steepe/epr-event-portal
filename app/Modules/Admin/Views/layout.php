<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:13
 */
$adminRole = strtolower((string) preg_replace('/[\\s_-]+/', '', (string) session('admin_role')));
$isSuperadmin = $adminRole === 'superadmin';
?>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.1/flowbite.min.css" rel="stylesheet" />
    <!-- inside <head> -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

</head>

<body class="dark:bg-gray-900 dark:text-gray-100 flex h-screen">
<!-- Sidebar -->
<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-gray-800 text-gray-100 flex flex-col">
    <div class="p-4 border-b border-gray-700 flex items-center justify-between">
        <span class="font-semibold text-lg">Event Portal</span>
        <button data-drawer-toggle="sidebar" class="md:hidden text-gray-300 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto p-4 space-y-2 text-sm">

        <!-- Dashboard -->
        <a href="<?php echo base_url('admin/dashboard'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-home text-lg"></i> Dashboard
        </a>

        <!-- Attendees -->
        <a href="<?php echo base_url('admin/attendees'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-group text-lg"></i> Attendees
        </a>

        <!-- Conferences -->
        <a href="<?php echo base_url('admin/conferences'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-calendar text-lg"></i> Conferences
        </a>

        <a href="<?php echo base_url('admin/sessions'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-list-ul text-lg"></i> Sessions
        </a>

        <!-- Speakers -->
        <a href="<?php echo base_url('admin/speakers'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-video text-lg"></i> Speakers
        </a>

        <!-- Exhibitors -->
        <a href="<?php echo base_url('admin/exhibitors'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-store text-lg"></i> Exhibitors
        </a>

        <!-- Sponsors -->
        <a href="<?php echo base_url('admin/sponsors'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-award text-lg"></i> Sponsors
        </a>

        <!-- Webinars -->
        <a href="<?php echo base_url('admin/webinars'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-video-recording text-lg"></i> Webinars
        </a>

        <a href="<?php echo base_url('admin/emergence-booth'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-store-alt text-lg"></i> Emergence Booth
        </a>

        <a href="<?php echo base_url('admin/communities'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-world text-lg"></i> Communities
        </a>

        <!-- Admin Users (Superadmin only) -->
        <?php if ($isSuperadmin): ?>
            <a href="<?php echo base_url('admin/admins'); ?>"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
                <i class="bx bx-shield-quarter text-lg"></i> Admin Users
            </a>
            <a href="<?php echo base_url('admin/audit-logs'); ?>"
               class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
                <i class="bx bx-history text-lg"></i> Activity Log
            </a>
        <?php endif; ?>

        <!-- Messages -->
        <a href="<?php echo base_url('admin/messages'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-chat text-lg"></i> Messages
        </a>
        <a href="<?php echo base_url('admin/analytics'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-bar-chart-alt-2 text-lg"></i> Analytics
        </a>

        <a href="<?php echo base_url('admin/store'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-cart text-lg"></i> Emergence Store
        </a>

        <!-- Payments -->
        <a href="<?php echo base_url('admin/payments'); ?>"
           class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700">
            <i class="bx bx-credit-card text-lg"></i> Payments
        </a>

    </nav>

    <div class="border-t border-gray-700 p-4 text-sm">
        <a href="<?php echo base_url('admin/logout'); ?>"
           class="flex items-center gap-2 text-red-400 hover:text-red-300">
            <i class="bx bx-log-out text-lg"></i> Logout
        </a>
    </div>
</aside>


<!-- Main Content -->
<div class="flex-1 flex flex-col">
    <header class="bg-gray-800 border-b border-gray-700 p-4 flex justify-between items-center text-sm">
        <div class="font-medium">Admin Dashboard</div>
        <div class="flex items-center gap-4">
            <span><?php echo session()->get('admin_name'); ?></span>
            <button id="theme-toggle" class="text-gray-300 hover:text-white focus:outline-none">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293a8 8 0 01-11.586-11.586 8 8 0 1011.586 11.586z"/></svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 01-2 0V3a1 1 0 011-1zm4.22 1.47a1 1 0 010 1.42L13.5 5.6a1 1 0 01-1.42-1.42l.72-.72a1 1 0 011.42 0z"/></svg>
            </button>
        </div>
    </header>

    <main class="p-6 overflow-y-auto flex-1">
        <?php echo $this->renderSection('content'); ?>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.5.1/flowbite.min.js"></script>
<!-- just before </body> -->
<script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
<script src="<?php echo asset_url('js/password-toggle.js'); ?>"></script>
<script>
    window.setInterval(() => {
        fetch(<?php echo json_encode(site_url('admin/emergence-booth/presence')); ?>, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': <?php echo json_encode(csrf_hash()); ?>}
        }).catch(() => {});
    }, 45000);

    const darkIcon = document.getElementById('theme-toggle-dark-icon');
    const lightIcon = document.getElementById('theme-toggle-light-icon');
    const toggleBtn = document.getElementById('theme-toggle');
    const html = document.documentElement;
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark');
        darkIcon.classList.remove('hidden');
    } else {
        html.classList.remove('dark');
        lightIcon.classList.remove('hidden');
    }
    toggleBtn.addEventListener('click', () => {
        darkIcon.classList.toggle('hidden');
        lightIcon.classList.toggle('hidden');
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            html.classList.add('dark');
            localStorage.theme = 'dark';
        }
    });
</script>
<script>
    document.querySelectorAll('.edit_attendee').forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(form.action, { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        location.reload();
                    } else {
                        alert('Update failed. Try again.');
                    }
                })
                .catch(() => alert('Network error.'));
        });
    });
</script>

<script>
    function deleteAttendee(e, id) {
        e.preventDefault();

        if (!id) {
            alert('Missing attendee ID.');
            return;
        }

        if (!confirm('Are you sure you want to delete this attendee?')) return;

        fetch(`<?php echo site_url('admin/attendees'); ?>/${id}/delete`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?php echo csrf_hash(); ?>'
            },
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

<script>
    function toggleMenu(id) {
        const menu = document.getElementById(id);
        menu.classList.toggle('hidden');
    }
</script>

<div id="admin-upload-progress" hidden aria-live="polite" style="position:fixed;inset:0;z-index:1000;background:rgba(3,7,18,.78);align-items:center;justify-content:center;padding:24px">
    <div style="width:min(460px,100%);border:1px solid rgba(148,163,184,.35);border-radius:14px;background:#111827;padding:22px;box-shadow:0 24px 60px rgba(0,0,0,.45)">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;color:#f8fafc;font-size:14px;font-weight:600"><span id="admin-upload-status">Uploading file…</span><span id="admin-upload-percent">0%</span></div>
        <div style="height:9px;overflow:hidden;border-radius:999px;background:#374151;margin-top:14px"><div id="admin-upload-fill" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="height:100%;width:0;border-radius:inherit;background:linear-gradient(90deg,#2563eb,#c026d3);transition:width .15s ease"></div></div>
        <p id="admin-upload-detail" style="margin:10px 0 0;color:#cbd5e1;font-size:12px">Please keep this page open while the file is saved.</p>
    </div>
</div>

<script>
(() => {
    const panel = document.getElementById('admin-upload-progress');
    const fill = document.getElementById('admin-upload-fill');
    const status = document.getElementById('admin-upload-status');
    const percent = document.getElementById('admin-upload-percent');
    const detail = document.getElementById('admin-upload-detail');

    const show = () => {
        panel.hidden = false;
        panel.style.display = 'flex';
    };
    const hide = () => {
        panel.hidden = true;
        panel.style.display = 'none';
    };
    const setProgress = (value) => {
        fill.style.width = `${value}%`;
        fill.setAttribute('aria-valuenow', String(value));
        percent.textContent = `${value}%`;
    };

    document.addEventListener('submit', (event) => {
        if (event.defaultPrevented || !(event.target instanceof HTMLFormElement)) return;

        const form = event.target;
        const inputs = [...form.querySelectorAll('input[type="file"]')];
        const selectedFiles = inputs.flatMap((input) => [...input.files]);
        if (selectedFiles.length === 0) return;

        event.preventDefault();
        const submitButtons = [...form.querySelectorAll('button[type="submit"], input[type="submit"]')];
        submitButtons.forEach((button) => {
            button.disabled = true;
            button.setAttribute('aria-disabled', 'true');
            button.style.opacity = '.65';
        });
        status.textContent = `Uploading ${selectedFiles.length === 1 ? 'file' : `${selectedFiles.length} files`}…`;
        detail.textContent = 'Please keep this page open while the file is saved.';
        setProgress(0);
        show();

        const request = new XMLHttpRequest();
        request.open((form.method || 'POST').toUpperCase(), form.action || window.location.href);
        request.upload.addEventListener('progress', (upload) => {
            if (!upload.lengthComputable) return;
            const value = Math.min(100, Math.round((upload.loaded / upload.total) * 100));
            setProgress(value);
            if (value === 100) {
                status.textContent = 'Processing upload…';
                detail.textContent = 'The server is saving the file. This can take a moment for videos.';
            }
        });
        request.addEventListener('load', () => {
            if (request.status >= 200 && request.status < 400) {
                status.textContent = 'Upload complete. Refreshing…';
                setProgress(100);
                window.location.assign(request.responseURL || window.location.href);
                return;
            }
            status.textContent = 'Upload failed.';
            detail.textContent = 'Please try again.';
            submitButtons.forEach((button) => {
                button.disabled = false;
                button.removeAttribute('aria-disabled');
                button.style.opacity = '';
            });
        });
        request.addEventListener('error', () => {
            status.textContent = 'Upload failed.';
            detail.textContent = 'Check your connection and try again.';
            submitButtons.forEach((button) => {
                button.disabled = false;
                button.removeAttribute('aria-disabled');
                button.style.opacity = '';
            });
        });
        request.send(new FormData(form));
    });
})();
</script>
</body>
</html>
