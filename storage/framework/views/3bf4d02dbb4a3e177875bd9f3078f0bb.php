<?php $__env->startSection('title', 'Kelola UKM'); ?>

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
        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
        .form-group label {
            font-weight: 600;
            color: #5a5c69;
        }
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola UKM</h1>
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gabung dengan Kode Referral</h6>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('ukm.join')); ?>" method="POST" class="row g-3 align-items-end">
                <?php echo csrf_field(); ?>
                <div class="col-md-8">
                    <label for="group_code" class="form-label">Kode Referral</label>
                    <input 
                        name="group_code" 
                        type="text" 
                        class="form-control"
                        id="group_code"
                        placeholder="Masukkan kode 4 digit"
                        maxlength="4" 
                        pattern=".{4,4}" 
                        required 
                    />
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100" aria-label="Gabung UKM dengan kode referral" title="Masukkan kode referral untuk bergabung dengan UKM">
                        <i class="fas fa-plus me-2"></i>Gabung UKM
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar UKM</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama UKM</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php if(!empty($joinedGroups) && count($joinedGroups) > 0): ?>
                            <?php $__currentLoopData = $joinedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ukm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="table-success">
                                <td>
                                    <strong><?php echo e($ukm->name); ?></strong>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded"><?php echo e($ukm->referral_code); ?></code>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Tergabung
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="<?php echo e(route('ukm.leave', $ukm->referral_code)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin keluar dari UKM ini?')"
                                            title="Keluar dari UKM"
                                            aria-label="Keluar dari UKM <?php echo e($ukm->name); ?>"
                                            data-bs-toggle="tooltip"
                                        >
                                            <i class="fas fa-sign-out-alt"></i> Keluar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                        
                        <?php $__empty_1 = true; $__currentLoopData = $availableGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ukm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <strong><?php echo e($ukm->name); ?></strong>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded"><?php echo e($ukm->referral_code); ?></code>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-users me-1"></i>Tersedia
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="<?php echo e(route('ukm.join')); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="group_code" value="<?php echo e($ukm->referral_code); ?>">
                                    <button 
                                        type="submit" 
                                        class="btn btn-sm btn-primary"
                                        title="Gabung UKM"
                                        aria-label="Gabung UKM <?php echo e($ukm->name); ?>"
                                        data-bs-toggle="tooltip"
                                    >
                                        <i class="fas fa-plus"></i> Gabung
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <?php if(empty($joinedGroups) || count($joinedGroups) == 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>Belum ada UKM yang tersedia
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\ukm\index.blade.php ENDPATH**/ ?>