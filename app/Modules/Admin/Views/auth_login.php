<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 09/11/2025
 * Time: 01:15
 */
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
<div class="w-full max-w-sm bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Admin Login</h2>
    <?php if (session()->getFlashdata('login_error')): ?>
        <div class="mb-3 text-red-600 text-sm">
            <?php echo session()->getFlashdata('login_error'); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?php echo site_url('admin/login'); ?>">
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" required class="w-full border p-2 rounded" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full border p-2 rounded" />
        </div>
        <button class="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Sign In
        </button>
    </form>
</div>
</body>
</html>
