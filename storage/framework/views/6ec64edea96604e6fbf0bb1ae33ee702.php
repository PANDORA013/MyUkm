<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyUkm - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm p-4">
        <?php if($errors->any()): ?>
        <div class="p-3 bg-red-300 mb-3 rounded">
            <?php echo e($errors->first()); ?>

        </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-blue-500 h-48 rounded-b-full relative flex items-center justify-center">
                <h1 class="text-3xl font-bold text-white">MyUkm</h1>
            </div>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="px-6 py-6">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <input type="text" name="nim" value="<?php echo e(old('nim')); ?>" placeholder="NIM" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-6">
                    <input type="password" name="password" placeholder="Password" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-500 text-white rounded-full font-semibold hover:bg-blue-600 transition">
                    Log In
                </button>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Don't have an account?
                    <a href="<?php echo e(route('register')); ?>" class="text-blue-500 font-semibold underline">Registration</a>
                </p>
            </form>
        </div>
    </div>

    <?php if(session('success')): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                text: "<?php echo e(session('success')); ?>",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\auth\login.blade.php ENDPATH**/ ?>