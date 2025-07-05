

<?php $__env->startSection('title', 'UKM Tersedia'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .ukm-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
        margin-bottom: 0;
    }
    .ukm-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
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
    .card-header {
        padding: 0.75rem 1rem;
    }
    .section-title {
        margin-bottom: 1rem;
        font-size: 1.2rem;
        font-weight: 600;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.5rem;
    }
    .card-body {
        padding: 1rem;
    }
    .alert {
        margin-bottom: 1rem;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .page-header {
        margin-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <h4 class="page-header">
        <i class="fas fa-university me-2"></i>Daftar UKM Tersedia
    </h4>
    
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Gabung UKM</h5>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show py-2" role="alert">
                            <?php echo e(session('info')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('ukm.join')); ?>" class="join-form">
                        <?php echo csrf_field(); ?>
                        <div class="mb-0">
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
    <div class="mt-3">
        <div class="section-title">
            <i class="fas fa-check-circle me-2"></i>UKM yang Diikuti
        </div>
        <div class="row g-3">
            <?php $__currentLoopData = $joinedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6">
                <div class="card ukm-card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-2">
                        <h5 class="mb-0"><?php echo e($group->name); ?></h5>
                        <?php if($group->isUserAdminInGroup ?? false): ?>
                            <span class="badge bg-warning text-dark ukm-badge">Admin Grup</span>
                        <?php else: ?>
                            <span class="badge bg-success ukm-badge">Anggota</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php
                            $ukm = \App\Models\UKM::where('code', $group->referral_code)->first();
                        ?>
                        <p class="card-text"><?php echo e($ukm && $ukm->description ? $ukm->description : 'Tidak ada deskripsi tersedia.'); ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-users me-1"></i> <?php echo e($group->members->count()); ?> Anggota
                                </span>
                                <?php if($group->userRoleInGroup ?? null): ?>
                                    <small class="text-muted d-block mt-1">
                                        Status: 
                                        <?php if($group->userRoleInGroup === 'admin'): ?>
                                            <strong class="text-warning">Admin Grup</strong>
                                        <?php else: ?>
                                            <strong class="text-success">Anggota</strong>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="btn-group mt-3 w-100" role="group">
                            <a href="<?php echo e(route('ukm.show', $group->referral_code)); ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                            <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-comments me-1"></i> Chat
                            </a>
                            <?php if($group->isUserAdminInGroup ?? false): ?>
                                <a href="<?php echo e(route('group.admin.dashboard', $group->referral_code)); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-cog me-1"></i> Kelola
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-2">
                            <form action="<?php echo e(route('ukm.leave', $group->referral_code)); ?>" method="POST" class="d-inline w-100">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Yakin ingin keluar dari UKM ini?')">
                                    <i class="fas fa-sign-out-alt me-1"></i> Keluar dari UKM
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if($availableGroups->count() > 0): ?>
    <div class="mt-3">
        <div class="section-title">
            <i class="fas fa-list-alt me-2"></i>UKM Lainnya
        </div>
        <div class="row g-3">
            <?php $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6">
                <div class="card ukm-card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-2">
                        <h5 class="mb-0"><?php echo e($group->name); ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo e($group->description ?? 'Tidak ada deskripsi tersedia.'); ?></p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-users me-1"></i> <?php echo e($group->members->count()); ?> Anggota
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
    </div>
    <?php endif; ?>

    <?php if($joinedGroups->count() === 0 && $availableGroups->count() === 0): ?>
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i> Belum ada UKM yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(Auth::user()->role === 'admin_grup' ? 'layouts.admin_grup' : 'layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/ukm/user_index.blade.php ENDPATH**/ ?>