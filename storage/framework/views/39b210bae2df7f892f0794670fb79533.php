<?php $__env->startSection('title', 'Dashboard Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-50 min-h-screen py-10 px-6">
    <div class="max-w-5xl mx-auto space-y-8">
        
        <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session($msg)): ?>
                <div class="px-4 py-3 rounded-lg shadow-sm border <?php echo e($msg==='success' ? 'bg-green-100 border-green-200 text-green-700' : ($msg==='error' ? 'bg-red-100 border-red-200 text-red-700' : 'bg-blue-100 border-blue-200 text-blue-700')); ?>">
                    <?php echo e(session($msg)); ?>

                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Card Total Anggota -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Anggota</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($totalMembers)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Seluruh UKM</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.member.search')); ?>" 
                   class="mt-auto w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i> Cari Anggota
                </a>
            </div>
            
            <!-- Card Total UKM -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total UKM</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($totalUkms)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Terdaftar</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <i class="fas fa-building text-green-500 text-2xl"></i>
                    </div>
                </div>
            </div>
            
            <!-- Card Riwayat Penghapusan -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Riwayat Penghapusan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($totalDeletedAccounts)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Akun Dihapus</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <i class="fas fa-history text-red-500 text-2xl"></i>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.user-deletions.index')); ?>" 
                   class="mt-4 block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i> Lihat Riwayat
                </a>
            </div>
            
            <!-- Card Admin Grup -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Grup</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($totalAdmins)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Aktif</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <i class="fas fa-user-shield text-purple-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card Pengguna Aktif Bulan Ini -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Aktif Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e(number_format($activeUsersThisMonth)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Pengguna</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <i class="fas fa-user-clock text-amber-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris Kedua Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <!-- Card Pengguna Baru Bulan Ini -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna Baru</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">+<?php echo e(number_format($newUsersThisMonth)); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Bulan Ini</p>
                    </div>
                    <div class="p-3 bg-teal-50 rounded-lg">
                        <i class="fas fa-user-plus text-teal-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Placeholder untuk statistik tambahan -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Rata-rata Keanggotaan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo e($totalUkms > 0 ? number_format($totalMembers / $totalUkms, 1) : 0); ?></p>
                        <p class="text-xs text-gray-500 mt-1">Anggota per UKM</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <i class="fas fa-chart-bar text-indigo-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Placeholder untuk statistik tambahan -->
            <div class="bg-white border border-gray-200 shadow rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pertumbuhan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            <?php echo e($newUsersThisMonth > 0 ? round(($newUsersThisMonth / $totalMembers) * 100, 1) : 0); ?>%
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Pertumbuhan Pengguna</p>
                    </div>
                    <div class="p-3 bg-pink-50 rounded-lg">
                        <i class="fas fa-chart-line text-pink-500 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <section class="bg-white border border-gray-200 shadow-sm rounded-xl p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Kelola UKM</h2>

            
            <form action="<?php echo e(url('/admin/ukm/tambah')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama UKM</label>
                        <input type="text" name="nama" required maxlength="255" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode (4 huruf/angka)</label>
                        <input type="text" name="kode" required pattern=".{4,4}" maxlength="4" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium w-full">Tambah</button>
                    </div>
                </div>
            </form>

            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Kode</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Anggota</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $ukms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ukm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap"><?php echo e($ukm->nama); ?></td>
                                <td class="px-4 py-2 whitespace-nowrap font-mono"><?php echo e($ukm->kode); ?></td>
                                <td class="px-4 py-2 text-center"><?php echo e($ukm->members_count); ?></td>
                                <td class="px-4 py-2 text-center space-x-3">
                                    <a href="<?php echo e(url('/admin/ukm/'.$ukm->id.'/anggota')); ?>" class="text-blue-600 hover:underline">Anggota</a>
                                    <a href="<?php echo e(url('/admin/ukm/edit/'.$ukm->id)); ?>" class="text-yellow-600 hover:underline">Edit</a>
                                    <form action="<?php echo e(url('/admin/ukm/hapus/'.$ukm->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus UKM?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada UKM terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>