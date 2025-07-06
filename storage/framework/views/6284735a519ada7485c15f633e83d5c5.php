

<?php $__env->startSection('title', 'Kelola ' . $group->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-users-cog me-2" style="color: #f59e0b;"></i>
                Kelola <?php echo e($group->name); ?>

            </h1>
            <p class="text-muted mb-0">Kelola anggota dan aktivitas grup UKM</p>
        </div>
        <div class="admin-badge">
            <i class="fas fa-crown me-1"></i>Admin UKM
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Anggota</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['total_anggota']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Anggota Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['anggota_aktif']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Anggota Dimute</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($stats['anggota_muted']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Members List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users me-2"></i>Daftar Anggota
            </h6>
        </div>
        <div class="card-body">
            <?php if($anggota->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            <?php echo e(substr($member->name, 0, 1)); ?>

                                        </div>
                                        <div>
                                            <strong><?php echo e($member->name); ?></strong>
                                            <?php if($member->role === 'admin_grup'): ?>
                                                <span class="badge bg-warning text-dark ms-2">
                                                    <i class="fas fa-crown me-1"></i>Admin
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e($member->email); ?></td>
                                <td>
                                    <?php if($member->pivot->is_muted): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-volume-mute me-1"></i>Dimute
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-volume-up me-1"></i>Aktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(\Carbon\Carbon::parse($member->pivot->created_at)->format('d M Y')); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Mute/Unmute Button -->
                                        <form method="POST" action="<?php echo e(route('admin.groups.mute-member', [$group->id, $member->id])); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php if($member->pivot->is_muted): ?>
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Unmute Anggota">
                                                    <i class="fas fa-volume-up"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Mute Anggota">
                                                    <i class="fas fa-volume-mute"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>

                                        <!-- Remove Button -->
                                        <?php if($member->role !== 'admin_grup'): ?>
                                        <form method="POST" action="<?php echo e(route('admin.groups.remove-member', [$group->id, $member->id])); ?>" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan <?php echo e($member->name); ?> dari grup ini?')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Keluarkan Anggota">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada anggota</h5>
                    <p class="text-gray-400">Grup ini belum memiliki anggota.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to Chat Button -->
    <div class="mt-4">
        <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="btn btn-primary">
            <i class="fas fa-comments me-2"></i>Kembali ke Chat
        </a>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .border-left-primary {
        border-left: .25rem solid var(--primary-color) !important;
    }
    .border-left-success {
        border-left: .25rem solid #1cc88a !important;
    }
    .border-left-warning {
        border-left: .25rem solid #f6c23e !important;
    }
    .card {
        border-radius: 0.5rem;
    }
    .admin-badge {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_grup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin_grup\manage_group.blade.php ENDPATH**/ ?>