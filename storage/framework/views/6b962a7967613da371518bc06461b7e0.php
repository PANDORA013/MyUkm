

<?php $__env->startSection('title', 'Daftar UKM'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">Daftar UKM & Grup</h1>
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Grup Yang Anda Kelola</h5>
                </div>
                <div class="card-body">
                    <?php if($managedGroups->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $managedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo e($group->name); ?></h5>
                                        <p class="card-text text-muted small">Kode: <?php echo e($group->referral_code); ?></p>
                                        <p class="card-text"><?php echo e(Str::limit($group->description, 100)); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success">Admin</span>
                                            <a href="<?php echo e(route('grup.dashboard', ['group_id' => $group->id])); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog me-1"></i> Kelola
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Anda belum menjadi admin di grup manapun.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Grup Yang Anda Ikuti</h5>
                </div>
                <div class="card-body">
                    <?php if($joinedGroups->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $joinedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!$group->isUserAdminInGroup): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo e($group->name); ?></h5>
                                            <p class="card-text text-muted small">Kode: <?php echo e($group->referral_code); ?></p>
                                            <p class="card-text"><?php echo e(Str::limit($group->description, 100)); ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-info"><?php echo e(ucfirst($group->userRoleInGroup ?? 'Anggota')); ?></span>
                                                <div>
                                                    <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-comments me-1"></i> Chat
                                                    </a>
                                                    <form action="<?php echo e(route('ukm.leave', $group->referral_code)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin keluar dari grup ini? Ini akan menghapus akses Anda ke semua pesan dan informasi grup.');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-sign-out-alt me-1"></i> Keluar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($joinedGroups->count() == 0 || $joinedGroups->where('isUserAdminInGroup', false)->count() == 0): ?>
                            <p class="text-muted mb-0">Anda tidak tergabung dengan grup lain selain yang Anda kelola.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Anda belum tergabung dengan grup manapun.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Gabung Grup Baru</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('ukm.join')); ?>" method="POST" class="row g-3">
                        <?php echo csrf_field(); ?>
                        <div class="col-md-4">
                            <label for="group_code" class="form-label">Kode Referral Grup</label>
                            <input type="text" class="form-control" id="group_code" name="group_code" placeholder="Masukkan kode 4 karakter" maxlength="4" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-1"></i> Gabung
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_grup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/grup/ukm_index.blade.php ENDPATH**/ ?>