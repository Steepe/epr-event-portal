<?php
/**
 * Admin forgot password form.
 */
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Reset Admin Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
<div class="w-full max-w-sm bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-2">Reset Password</h2>
    <p class="mb-4 text-sm text-gray-600">Enter your admin email address.</p>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-3 text-red-600 text-sm">
            <?php echo esc(session()->getFlashdata('error')); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo site_url('admin/forgot-password'); ?>">
        <?php echo csrf_field(); ?>
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="<?php echo esc(old('email')); ?>" required class="w-full border p-2 rounded" />
        </div>
        <button class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Send Reset Link
        </button>
    </form>

    <p class="mt-4 text-center text-sm">
        <a href="<?php echo site_url('admin/login'); ?>" class="text-blue-600 hover:underline">Back to login</a>
    </p>
</div>
</body>
</html>
