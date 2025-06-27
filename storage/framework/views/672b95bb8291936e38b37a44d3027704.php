<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'MyUkm'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js', 'resources/js/profile.js']); ?>
</head>
<body class="font-[Inter]">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm py-3 px-6 border-b">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <a href="<?php echo e(url('/')); ?>" class="flex items-center space-x-2 hover:opacity-75 transition">



                        <span class="font-bold text-lg">MyUkm</span>
                    </a>
                </div>

                <?php if(Request::is('ukm/*/chat')): ?>
                    <div class="hidden sm:flex items-center">
                        <a href="<?php echo e(route('ukm.index')); ?>" class="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Beranda
                        </a>
                    </div>
                <?php endif; ?>

                <?php if(auth()->guard()->check()): ?>
                <div class="flex items-center space-x-6">
                    <a href="<?php echo e(route('profile.show')); ?>" class="flex items-center space-x-3 py-1 px-3 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-medium text-gray-700"><?php echo e(auth()->user()->name); ?></span>
                        </div>

                    </a>

                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-red-500 hover:text-red-600 font-medium text-sm transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/layouts/app.blade.php ENDPATH**/ ?>