<?php $__env->startSection('title', 'Detail UKM - ' . $group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><?php echo e($group->name); ?></h1>
                <a href="<?php echo e(route('ukm.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Group Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi UKM</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama UKM:</strong> <?php echo e($group->name); ?></p>
                            <p><strong>Kode Referral:</strong> <?php echo e($group->referral_code); ?></p>
                            <p><strong>Status:</strong> 
                                <?php if($group->is_active): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Anggota:</strong> <?php echo e($members->count()); ?> orang</p>
                            <p><strong>Bergabung sejak:</strong> <?php echo e($group->created_at->format('d M Y')); ?></p>
                        </div>
                    </div>
                    
                    <?php if($group->description): ?>
                        <div class="mt-3">
                            <strong>Deskripsi:</strong>
                            <p class="mt-2"><?php echo e($group->description); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Aksi</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <?php if($isMember): ?>
                            <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="btn btn-primary">
                                <i class="fas fa-comments"></i> Chat Grup
                            </a>
                            
                            <?php if($isGroupAdmin): ?>
                                <a href="<?php echo e(route('group.admin.dashboard', $group->referral_code)); ?>" class="btn btn-warning">
                                    <i class="fas fa-cog"></i> Kelola Grup
                                </a>
                                <a href="<?php echo e(route('group.admin.members', $group->referral_code)); ?>" class="btn btn-info">
                                    <i class="fas fa-users"></i> Kelola Anggota
                                </a>
                            <?php endif; ?>
                            
                            <form method="POST" action="<?php echo e(route('ukm.leave', $group->referral_code)); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Apakah Anda yakin ingin keluar dari UKM ini?')">
                                    <i class="fas fa-sign-out-alt"></i> Keluar dari UKM
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?php echo e(route('ukm.join')); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="referral_code" value="<?php echo e($group->referral_code); ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Bergabung
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($isMember): ?>
                        <div class="mt-3">
                            <small class="text-muted">
                                Status Anda di grup ini: 
                                <?php if($userRoleInGroup === 'admin'): ?>
                                    <span class="badge bg-warning text-dark">Admin Grup</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Anggota</span>
                                <?php endif; ?>
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Daftar Anggota (<?php echo e($members->count()); ?>)</h6>
                </div>
                <div class="card-body">
                    <?php if($members->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title rounded-circle bg-light text-dark">
                                                <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fs-sm"><?php echo e($member->name); ?></h6>
                                            <small class="text-muted">
                                                <?php
                                                    $membershipPivot = $member->pivot ?? null;
                                                    $isAdminInThisGroup = $membershipPivot && $membershipPivot->is_admin;
                                                ?>
                                                
                                                <?php if($isAdminInThisGroup): ?>
                                                    <span class="badge bg-warning text-dark">Admin Grup</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Anggota</span>
                                                <?php endif; ?>
                                                
                                                <?php if($member->role === 'admin_website'): ?>
                                                    <span class="badge bg-danger ms-1">Admin Website</span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Belum ada anggota.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.fs-sm {
    font-size: 0.875rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\ukm\show.blade.php ENDPATH**/ ?>