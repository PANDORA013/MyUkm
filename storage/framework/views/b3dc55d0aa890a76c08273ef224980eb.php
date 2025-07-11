<?php $__env->startSection('title', 'Riwayat Anggota Keluar'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <h4 class="page-header mb-4">
        <i class="fas fa-history me-2"></i>Riwayat Anggota Keluar
        <?php if($group): ?>
            <span class="text-muted">- <?php echo e($group->name); ?></span>
        <?php endif; ?>
    </h4>
    <div class="card">
        <div class="card-header bg-light">
            <span><i class="fas fa-user-slash me-1"></i> Anggota yang pernah keluar (<?php echo e($ex_members->count()); ?>)</span>
        </div>
        <div class="card-body">
            <?php if($ex_members->isEmpty()): ?>
                <div class="text-center py-4">
                    <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada riwayat anggota keluar</h6>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php $__currentLoopData = $ex_members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="member-avatar me-3 bg-danger">
                                            <?php echo e(substr($ex->name, 0, 1)); ?>

                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-danger"><?php echo e($ex->name); ?></h6>
                                            <p class="mb-1 text-muted small">
                                                <i class="fas fa-id-card me-1"></i><?php echo e($ex->nim); ?>

                                            </p>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-plus me-1"></i>
                                                    Bergabung: <?php echo e(\Carbon\Carbon::parse($ex->pivot->created_at)->format('d M Y')); ?><br>
                                                    <i class="fas fa-calendar-minus me-1"></i>
                                                    Keluar: <?php echo e(\Carbon\Carbon::parse($ex->pivot->deleted_at)->format('d M Y')); ?>

                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_grup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/grup/riwayat_anggota.blade.php ENDPATH**/ ?>