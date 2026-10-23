<?php
/**
 * Admin reset password form.
 */
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Set Admin Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
<div class="w-full max-w-sm bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Set New Password</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-3 text-red-600 text-sm">
            <?php echo esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo site_url('admin/reset-password/' . $token); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-4">
            <label class="block text-sm font-medium">New Password</label>
            <input type="password" name="password" minlength="8" required class="w-full border p-2 rounded" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirm" minlength="8" required class="w-full border p-2 rounded" />
        </div>
        <button class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Update Password
        </button>
    </form>
</div>
</body>
</html>
