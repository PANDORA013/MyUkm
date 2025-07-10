<?php $__env->startSection('title', 'Detail Riwayat Penghapusan'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .info-card {
        border-left: 4px solid #4e73df;
        border-radius: 4px;
    }
    .info-card .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
    .info-label {
        font-weight: 600;
        color: #5a5c69;
        width: 200px;
    }
    .info-value {
        color: #3a3b45;
    }
    .metadata {
        font-size: 0.85rem;
        color: #858796;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-trash-alt text-danger me-2"></i>Detail Riwayat Penghapusan
        </h1>
        <a href="<?php echo e(route('admin.user-deletions.index')); ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- User Information Card -->
            <div class="card info-card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-circle me-2"></i>Informasi Pengguna
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td class="info-label">ID Pengguna</td>
                                <td class="info-value">
                                    <span class="badge bg-light text-dark">#<?php echo e($deletion->user_id); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Nama Lengkap</td>
                                <td class="info-value"><?php echo e($deletion->user_name); ?></td>
                            </tr>
                            <tr>
                                <td class="info-label">NIM</td>
                                <td class="info-value"><?php echo e($deletion->user_nim ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="info-label">Email</td>
                                <td class="info-value"><?php echo e($deletion->user_email ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="info-label">Role</td>
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
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Deletion Details Card -->
            <div class="card info-card mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Detail Penghapusan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td class="info-label">Waktu Penghapusan</td>
                                <td class="info-value">
                                    <i class="far fa-clock me-2"></i>
                                    <?php echo e($deletion->created_at->translatedFormat('l, d F Y H:i')); ?>

                                    <div class="metadata mt-1">
                                        (<?php echo e($deletion->created_at->diffForHumans()); ?>)
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Dihapus Oleh</td>
                                <td class="info-value">
                                    <?php if($deletion->deletedBy): ?>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-shield me-2"></i>
                                            <div>
                                                <div><?php echo e($deletion->deletedBy->name); ?></div>
                                                <div class="metadata"><?php echo e($deletion->deletedBy->email); ?></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sistem</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Alasan Penghapusan</td>
                                <td class="info-value">
                                    <div class="alert alert-light border" role="alert">
                                        <i class="fas fa-quote-left me-2 text-muted"></i>
                                        <?php echo e($deletion->reason ?? 'Tidak ada informasi yang dicantumkan'); ?>

                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-cog me-2"></i>Aksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('admin.user-deletions.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Informasi
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Data pengguna yang telah dihapus tidak dapat dipulihkan.
                    </p>
                    <p class="small mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Riwayat ini dicatat untuk keperluan audit dan pelacakan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Enable tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin\user_deletions\show.blade.php ENDPATH**/ ?>