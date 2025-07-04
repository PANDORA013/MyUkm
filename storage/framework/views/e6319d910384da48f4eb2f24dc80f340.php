

<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }
        .profile-header {
            text-align: center;
            padding: 1.25rem;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f2ff;
        }
        .profile-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .profile-nim {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .section-title {
            border-bottom: 2px solid #e3e6f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .password-form {
            background-color: #f8f9fc;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .form-label {
            font-weight: 500;
            color: #5a5c69;
        }
        .danger-zone {
            background-color: #fdeded;
            border-left: 4px solid #dc3545;
            padding: 1rem;
        }
        .membership-card {
            border-left: 4px solid #4e73df;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        .membership-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
        .page-header {
            margin-bottom: 1rem;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 0.75rem;
        }
        table.table-bordered td, 
        table.table-bordered th {
            padding: 0.5rem 0.75rem;
        }
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.25rem rgba(67, 56, 202, 0.1);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <h4 class="page-header">
        <i class="fas fa-user-circle me-2"></i>Profil Saya
    </h4>
    
    
    <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($msg)): ?>
            <div class="alert alert-<?php echo e($msg === 'error' ? 'danger' : $msg); ?> alert-dismissible fade show py-2" role="alert">
                <?php echo e(session($msg)); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <div class="row g-3">
        <!-- Profil dan Foto -->
        <div class="col-lg-4">
            <div class="card">
                <div class="profile-header">
                    <?php if($user->photo): ?>
                        <img src="<?php echo e(asset('storage/' . $user->photo)); ?>" alt="Profile" class="profile-img">
                    <?php else: ?>
                        <div class="profile-img">
                            <span class="h1 text-primary"><?php echo e(substr($user->name, 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>
                    <h5 class="profile-name"><?php echo e($user->name); ?></h5>
                    <div class="profile-nim"><?php echo e($user->nim); ?></div>
                    
                    <form method="POST" action="<?php echo e(route('profile.updatePhoto')); ?>" enctype="multipart/form-data" class="mt-3">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="photo" class="form-label d-block small">Upload Foto Baru</label>
                            <input type="file" name="photo" id="photo" class="form-control form-control-sm" accept="image/*" required>
                            <small class="text-muted d-block mt-1">Max 2MB, format JPG/PNG</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i> Upload Foto
                        </button>
                    </form>
                </div>
                
                <div class="card-body">
                    <h5 class="section-title">Informasi Dasar</h5>
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="40%">Nama</th>
                            <td><?php echo e($user->name); ?></td>
                        </tr>
                        <tr>
                            <th>NIM</th>
                            <td><?php echo e($user->nim); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-info text-white">
                                    <?php echo e(ucfirst($user->role)); ?>

                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Bergabung</th>
                            <td><?php echo e($user->created_at->format('d M Y')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Ubah Password & Keanggotaan -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i> Ubah Password
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('profile.updatePassword')); ?>" class="password-form">
                        <?php echo csrf_field(); ?>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" id="current_password" name="current_password" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password Baru</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Keanggotaan UKM -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> Keanggotaan UKM
                </div>
                <div class="card-body">
                    <?php if($memberships->count() > 0): ?>
                        <?php $__currentLoopData = $memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card membership-card">
                                <div class="card-body py-2">
                                    <h5 class="card-title h6 mb-1"><?php echo e($membership->ukm_name); ?></h5>
                                    <div class="d-flex justify-content-between">
                                        <p class="card-text text-muted mb-0">
                                            <small>Bergabung sejak: <?php echo e(\Carbon\Carbon::parse($membership->joined_at)->format('d M Y')); ?></small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="alert alert-info py-2 mb-0">
                            <i class="fas fa-info-circle me-2"></i> Anda belum tergabung dalam UKM manapun.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Hapus Akun -->
            <div class="card danger-zone">
                <div class="card-body">
                    <h5 class="text-danger mb-2"><i class="fas fa-exclamation-triangle me-2"></i> Zona Berbahaya</h5>
                    <p class="mb-3">Menghapus akun akan menghapus semua data Anda dan tidak dapat dipulihkan.</p>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-1"></i> Hapus Akun Saya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Konfirmasi Penghapusan Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Apakah Anda yakin ingin menghapus akun Anda?</p>
                <p>Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.</p>
                <form method="POST" action="<?php echo e(route('profile.destroy')); ?>" id="deleteAccountForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <div class="mb-3">
                        <label for="confirm_deletion" class="form-label">Ketik "HAPUS" untuk mengkonfirmasi</label>
                        <input type="text" class="form-control" id="confirm_deletion" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>Hapus Akun Saya</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmInput = document.getElementById('confirm_deletion');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const deleteForm = document.getElementById('deleteAccountForm');
        
        confirmInput.addEventListener('input', function() {
            confirmBtn.disabled = this.value !== 'HAPUS';
        });
        
        confirmBtn.addEventListener('click', function() {
            if (confirmInput.value === 'HAPUS') {
                deleteForm.submit();
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/profile/user.blade.php ENDPATH**/ ?>