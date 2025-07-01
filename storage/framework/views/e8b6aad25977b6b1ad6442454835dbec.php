<?php $__env->startSection('content'); ?>
<div class="bg-gray-50 min-h-screen py-12 px-6">
    <div class="max-w-4xl mx-auto space-y-10">
        
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('info')): ?>
            <div class="bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg shadow-sm">
                <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?>

        
        <section class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Gabung dengan Kode Referral</h2>
            <form action="<?php echo e(route('ukm.join')); ?>" method="POST" class="flex flex-col sm:flex-row gap-3 items-center justify-center">
                <?php echo csrf_field(); ?>
                <input name="group_code" type="text" 
                    class="w-full sm:w-2/3 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    placeholder="Masukkan kode referral"
                    maxlength="4" pattern=".{4,4}" required />
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    Gabung
                </button>
            </form>
            <?php $__errorArgs = ['group_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-2 text-sm text-red-600 text-center"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </section>

        
        <section class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Daftar UKM</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3 hover:shadow-sm transition">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800"><?php echo e($group->name); ?></h3>
                            <p class="text-xs text-gray-500 italic text-gray-400">Tanyakan kode ke pengurus UKM</p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center col-span-2">Tidak ada UKM tersedia untuk kamu bergabung saat ini.</p>
                <?php endif; ?>
            </div>
        </section>

        
        <section class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">UKM yang Kamu Ikuti</h2>
            <?php if($joinedGroups->isEmpty()): ?>
                <p class="text-gray-500 text-center">Kamu belum bergabung dengan UKM manapun.</p>
            <?php else: ?>
                <div class="grid md:grid-cols-2 gap-6">
                    <?php $__currentLoopData = $joinedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo e($group->name); ?></h3>
                                <p class="text-sm text-gray-500"><?php echo e($group->users->count()); ?> anggota</p>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                    Masuk Chat
                                </a>
                                <form action="<?php echo e(route('ukm.leave', $group->referral_code)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" 
                                        onclick="return confirm('Yakin ingin keluar dari UKM ini?')"
                                        class="text-red-500 hover:text-red-600 text-sm font-semibold">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/ukm/index.blade.php ENDPATH**/ ?>