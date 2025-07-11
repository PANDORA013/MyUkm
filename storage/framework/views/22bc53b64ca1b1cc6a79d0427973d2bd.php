<?php $__env->startSection('title', 'Riwayat Penghapusan Pengguna'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .deleted-user {
            background-color: #fff5f5;
        }
        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Penghapusan Pengguna</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(url('/admin/dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Penghapusan</li>
            </ol>
        </nav>
    </div>

    
    <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($msg)): ?>
            <div class="alert alert-<?php echo e($msg === 'error' ? 'danger' : $msg); ?> alert-dismissible fade show" role="alert">
                <?php echo e(session($msg)); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Daftar Akun yang Dihapus
            </h6>
            <div class="text-muted small">
                Total: <?php echo e($deletions->total()); ?> akun
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dihapus Oleh</th>
                            <th>Tanggal Hapus</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $deletions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deletion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><code><?php echo e($deletion->user_id); ?></code></td>
                                <td>
                                    <div class="fw-bold"><?php echo e($deletion->user_name); ?></div>
                                </td>
                                <td><?php echo e($deletion->user_nim ?? '-'); ?></td>
                                <td><?php echo e($deletion->user_email ?? '-'); ?></td>
                                <td>
                                    <?php if($deletion->user_role): ?>
                                        <span class="badge <?php echo e($deletion->user_role == 'admin_website' ? 'bg-danger' : ($deletion->user_role == 'admin_grup' ? 'bg-primary' : 'bg-secondary')); ?>">
                                            <?php echo e($deletion->user_role); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            Tidak tersedia
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($deletion->deletedBy): ?>
                                        <div class="fw-bold"><?php echo e($deletion->deletedBy->name); ?></div>
                                        <small class="text-muted"><?php echo e($deletion->deletedBy->role); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Sistem</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?php echo e($deletion->created_at->format('d/m/Y')); ?></div>
                                    <small class="text-muted"><?php echo e($deletion->created_at->format('H:i')); ?></small>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('admin.user-deletions.show', $deletion->id)); ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="Lihat Detail"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>Tidak ada data penghapusan pengguna
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($deletions->hasPages()): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($deletions->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\NganTeen\MyUkm\resources\views/admin/user_deletions/index.blade.php ENDPATH**/ ?>