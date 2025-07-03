<?php $__env->startSection('title', 'Profil Saya'); ?>

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
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
        .profile-photo {
            height: 120px;
            width: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .profile-photo-placeholder {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e2e8f0;
            font-size: 2rem;
            font-weight: bold;
            color: #4e73df;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(url('/admin/dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
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

    <!-- Main Content -->
    <div class="row">
        <!-- Profile Details Card -->
        <div class="col-lg-4 mb-4">
            <!-- Photo Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-circle me-2"></i>Foto Profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-4">
                        <div class="profile-photo">
                            <?php if($user->photo): ?>
                                <img src="<?php echo e(Storage::url($user->photo)); ?>" 
                                     alt="<?php echo e($user->name); ?>" 
                                     class="h-100 w-100 object-cover"
                                     id="currentPhoto">
                            <?php else: ?>
                                <div class="profile-photo-placeholder"
                                     id="photoPlaceholder">
                                    <?php echo e(strtoupper(substr($user->name ?? 'U', 0, 1))); ?>

                                </div>
                            <?php endif; ?>
                            <img id="photoPreview" class="h-100 w-100 object-cover d-none" alt="Preview">
                        </div>
                    </div>
                    <h4 class="font-weight-bold text-gray-800"><?php echo e($user->name ?? 'Pengguna'); ?></h4>
                    <p class="text-muted mb-4"><?php echo e($user->nim ?? 'No NIM'); ?></p>
                    <form action="<?php echo e(route('profile.updatePhoto')); ?>" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="mb-4">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="photo" class="form-label small text-muted">JPG atau PNG. Maksimal 2MB</label>
                            <input type="file" 
                                   name="photo" 
                                   id="photo" 
                                   accept="image/jpeg,image/png"
                                   class="form-control form-control-sm">
                            <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="small text-danger mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <button type="submit" 
                                class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i> Upload Foto
                        </button>
                    </form>
                </div>
            </div>

            <!-- Basic Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="40%">Nama</th>
                            <td><?php echo e($user->name); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">NIM</th>
                            <td><?php echo e($user->nim); ?></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Role</th>
                            <td>
                                <?php
                                    $role = $user->role ?? 'anggota';
                                    
                                    $badgeClass = [
                                        'admin_website' => 'bg-primary',
                                        'admin_grup' => 'bg-success',
                                        'anggota' => 'bg-secondary',
                                        'member' => 'bg-secondary'
                                    ][$role] ?? 'bg-secondary';
                                    
                                    $roleText = [
                                        'admin_website' => 'Admin Website',
                                        'admin_grup' => 'Admin Grup',
                                        'anggota' => 'Anggota',
                                        'member' => 'Member'
                                    ][$role] ?? ucfirst($role);
                                ?>
                                <span class="badge <?php echo e($badgeClass); ?>">
                                    <?php echo e($roleText); ?>

                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Bergabung</th>
                            <td><?php echo e($user->created_at ? $user->created_at->format('d M Y') : 'Tidak tersedia'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('profile.updatePassword')); ?>" 
                          method="POST" 
                          id="passwordForm">
                        <?php echo csrf_field(); ?>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       required
                                       class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       required
                                       minlength="8"
                                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       required
                                       minlength="8"
                                       class="form-control">
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

            <!-- Membership Table -->
            <?php if(Auth::user()->role !== 'admin_website'): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Keanggotaan UKM
                    </h6>
                    <span class="badge bg-primary">
                        Total: <?php echo e(count($memberships ?? [])); ?> UKM
                    </span>
                </div>
                <div class="card-body">
                    <?php if(isset($memberships) && count($memberships) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama UKM</th>
                                    <th>Tanggal Bergabung</th>
                                    <th>Status</th>
                                    <th>Terakhir Dilihat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $memberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                        <?php echo e(strtoupper(substr($membership->ukm_name ?? 'U', 0, 1))); ?>

                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?php echo e($membership->ukm_name ?? 'UKM Tidak Ditemukan'); ?></div>
                                                    <small class="text-muted"><?php echo e($membership->joined_at ? \Carbon\Carbon::parse($membership->joined_at)->format('d/m/Y') : 'N/A'); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo e($membership->joined_at ? \Carbon\Carbon::parse($membership->joined_at)->translatedFormat('d F Y') : 'N/A'); ?>

                                        </td>
                                        <td>
                                            <?php
                                                $userRole = Auth::user() ? Auth::user()->role : 'anggota';
                                                $badgeClass = $userRole === 'admin_grup' ? 'bg-primary' : 'bg-secondary';
                                                $roleText = $userRole === 'admin_grup' ? 'Admin Grup' : 'Anggota';
                                            ?>
                                            <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($roleText); ?></span>
                                        </td>
                                        <td>
                                            <?php if(isset($membership->is_online) && $membership->is_online): ?>
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-circle text-success me-1" style="font-size: 0.6rem;"></i>Online
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo e(isset($membership->last_seen) && $membership->last_seen ? \Carbon\Carbon::parse($membership->last_seen)->diffForHumans() : 'Belum pernah online'); ?>

                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-users-slash fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Tidak Ada Keanggotaan UKM</h5>
                        <p class="text-muted">Anda belum terdaftar sebagai anggota UKM manapun.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Delete Account -->
            <div class="card shadow mb-4 border-danger">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Hapus Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-exclamation-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Peringatan!</h5>
                                <p>
                                    Setelah akun dihapus, semua data akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                                    Pastikan Anda telah mencadangkan data penting sebelum melanjutkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="<?php echo e(route('profile.destroy')); ?>" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i> Hapus Akun Saya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="d-flex justify-content-between mt-3">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Preview image before upload
        document.getElementById('photo').addEventListener('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentPhoto').classList.add('d-none');
                    document.getElementById('photoPlaceholder').classList.add('d-none');
                    var preview = document.getElementById('photoPreview');
                    preview.classList.remove('d-none');
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/profile/index.blade.php ENDPATH**/ ?>