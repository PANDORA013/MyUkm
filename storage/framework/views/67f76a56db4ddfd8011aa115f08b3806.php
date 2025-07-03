

<?php $__env->startSection('title', 'UKM Tersedia'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .ukm-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    .ukm-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .ukm-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .join-form {
        max-width: 400px;
        margin: 0 auto;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Gabung UKM</h5>
                </div>
                <div class="card-body">
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

                    <?php if(session('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo e(session('info')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('ukm.join')); ?>" class="join-form">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="group_code" class="form-label">Kode Referral UKM</label>
                            <div class="input-group">
                                <input type="text" class="form-control <?php $__errorArgs = ['group_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    id="group_code" name="group_code" placeholder="Masukkan kode 4 digit" 
                                    maxlength="4" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane me-1"></i> Gabung
                                </button>
                            </div>
                            <?php $__errorArgs = ['group_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Dapatkan kode referral dari admin atau ketua UKM.</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if($joinedGroups->count() > 0): ?>
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3">UKM yang Diikuti</h5>
        </div>
        <?php $__currentLoopData = $joinedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card ukm-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0"><?php echo e($group->name); ?></h5>
                    <span class="badge bg-success ukm-badge">Anggota</span>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo e($group->description ?? 'Tidak ada deskripsi tersedia.'); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-users me-1"></i> <?php echo e($group->users->count()); ?> Anggota
                            </span>
                        </div>
                        <div class="btn-group">
                            <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-comments me-1"></i> Chat
                            </a>
                            <form action="<?php echo e(route('ukm.leave', $group->referral_code)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin keluar dari UKM ini?')">
                                    <i class="fas fa-sign-out-alt me-1"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <?php if($availableGroups->count() > 0): ?>
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3">UKM Lainnya</h5>
        </div>
        <?php $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card ukm-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h5 class="mb-0"><?php echo e($group->name); ?></h5>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo e($group->description ?? 'Tidak ada deskripsi tersedia.'); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-users me-1"></i> <?php echo e($group->users->count()); ?> Anggota
                            </span>
                        </div>
                        <div>
                            <span class="text-muted small">Masukkan kode referral untuk bergabung</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <?php if($joinedGroups->count() === 0 && $availableGroups->count() === 0): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i> Belum ada UKM yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/ukm/user_index.blade.php ENDPATH**/ ?>