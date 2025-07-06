<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join UKM - MyUKM</title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
</head>
<body class="min-h-screen bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-purple-600">Gabung UKM</h2>

        <?php if(session('success')): ?>
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded-lg">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded-lg">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('join.group')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Referal UKM
                </label>
                <select 
                    name="group_code" 
                    class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    required
                >
                    <option value="" disabled selected>-- Pilih UKM --</option>
                    <?php $__currentLoopData = $groupCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($code); ?>" <?php echo e(in_array($code, $alreadyJoined) ? 'disabled' : ''); ?>>
                            <?php echo e($name); ?> (<?php echo e($code); ?>) <?php echo e(in_array($code, $alreadyJoined) ? '- Sudah Tergabung' : ''); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['group_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button 
                type="submit" 
                class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-colors"
                aria-label="Gabung UKM dengan kode referral"
                title="Gabung UKM dengan kode referral yang dimasukkan"
            >
                Gabung UKM
            </button>
        </form>

        <?php if(!empty($alreadyJoined)): ?>
            <div class="mt-6">
                <h3 class="font-medium text-gray-700 mb-2">UKM Yang Sudah Diikuti:</h3>
                <div class="bg-gray-50 rounded-lg p-3">
                    <?php $__currentLoopData = $alreadyJoined; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="text-sm text-gray-600">
                            <?php echo e($groupCodes[$code]); ?> (<?php echo e($code); ?>)
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="<?php echo e(route('ukm.index')); ?>" class="text-purple-600 hover:underline text-sm">
                ← Kembali ke Daftar UKM
            </a>
        </div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\join-group.blade.php ENDPATH**/ ?>