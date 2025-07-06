

<?php $__env->startSection('title', 'Dashboard Admin - ' . $group->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .stat-card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-right: 1rem;
    }
    .member-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.8rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Dashboard Admin</h1>
                    <p class="text-muted mb-0">Kelola grup <?php echo e($group->name); ?></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('ukm.show', $group->referral_code)); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Detail UKM
                    </a>
                    <a href="<?php echo e(route('group.admin.members', $group->referral_code)); ?>" class="btn btn-primary">
                        <i class="fas fa-users"></i> Kelola Anggota
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0 bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-white bg-opacity-20">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?php echo e($members->count()); ?></h5>
                        <small class="text-white-50">Total Anggota</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 bg-warning text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-white bg-opacity-20">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?php echo e($adminCount); ?></h5>
                        <small class="text-white-50">Admin Grup</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="stat-icon bg-white bg-opacity-20">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?php echo e($memberCount); ?></h5>
                        <small class="text-white-50">Anggota Biasa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Group Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Grup</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('group.admin.settings', $group->referral_code)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Grup</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo e($group->name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo e($group->description); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Referral</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?php echo e($group->referral_code); ?>" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('<?php echo e($group->referral_code); ?>')">
                                    <i class="fas fa-copy"></i> Salin
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Admin Grup</h6>
                </div>
                <div class="card-body">
                    <?php
                        $admins = $members->where('pivot.is_admin', true);
                    ?>
                    
                    <?php if($admins->count() > 0): ?>
                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="member-avatar me-2">
                                            <?php echo e(strtoupper(substr($admin->name, 0, 1))); ?>

                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fs-6"><?php echo e($admin->name); ?></h6>
                                            <small class="text-muted"><?php echo e($admin->nim); ?></small>
                                        </div>
                                        <span class="badge bg-warning text-dark">Admin</span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Tidak ada admin.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('group.admin.members', $group->referral_code)); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-users"></i> Kelola Anggota
                        </a>
                        <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="btn btn-outline-info">
                            <i class="fas fa-comments"></i> Buka Chat Grup
                        </a>
                        <a href="<?php echo e(route('ukm.show', $group->referral_code)); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-eye"></i> Lihat Halaman Grup
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create a temporary toast notification
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    Kode referral berhasil disalin!
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\group\admin\dashboard.blade.php ENDPATH**/ ?>