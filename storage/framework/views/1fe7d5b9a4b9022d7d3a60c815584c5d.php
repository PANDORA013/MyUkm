<?php $__env->startSection('title', 'Dashboard Admin Grup'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .stats-card {
        transition: all 0.3s ease;
        border-left: 4px solid #16a085;
    }
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .member-card {
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .member-card:hover {
        border-left-color: #16a085;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .member-avatar {
        width: 40px;
        height: 40px;
        background-color: #16a085;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }
    .page-header {
        margin-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-header mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin Grup
            <?php if($group): ?>
                <span class="text-muted">- <?php echo e($group->name); ?></span>
            <?php endif; ?>
        </h4>
        
        <?php if($managedGroups->count() > 1): ?>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="groupSelector" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-exchange-alt me-2"></i>
                    <?php echo e($group ? $group->name : 'Pilih UKM'); ?>

                </button>
                <ul class="dropdown-menu" aria-labelledby="groupSelector">
                    <?php $__currentLoopData = $managedGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $managedGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item <?php echo e($group && $group->id === $managedGroup->id ? 'active' : ''); ?>" 
                               href="<?php echo e(route('grup.dashboard', ['group_id' => $managedGroup->id])); ?>">
                                <i class="fas fa-university me-2"></i><?php echo e($managedGroup->name); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    
    <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($msg)): ?>
            <div class="alert alert-<?php echo e($msg === 'error' ? 'danger' : $msg); ?> alert-dismissible fade show py-2" role="alert">
                <?php echo e(session($msg)); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <?php if(!$group): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Anda belum mengelola grup UKM manapun sebagai admin. Silakan hubungi administrator website untuk mendapatkan akses admin grup.
        </div>
    <?php else: ?>
        <!-- UKM Description Card -->
        <?php
            $ukm = \App\Models\UKM::where('code', $group->referral_code)->first();
        ?>
        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i> Tentang UKM
                </h6>
                <?php if(auth()->user()->role === 'admin_grup'): ?>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDescriptionModal" aria-label="Edit description" title="Edit deskripsi UKM">
                        <i class="fas fa-edit me-1" aria-hidden="true"></i> Edit Deskripsi
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($ukm && $ukm->description): ?>
                    <p class="mb-0"><?php echo e($ukm->description); ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        <?php if(auth()->user()->role === 'admin_grup'): ?>
                            Belum ada deskripsi untuk UKM ini. Klik tombol "Edit Deskripsi" untuk menambahkan informasi.
                        <?php else: ?>
                            Belum ada deskripsi untuk UKM ini.
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-users fa-2x text-primary me-3"></i>
                            <div>
                                <h3 class="mb-0"><?php echo e($stats['total_anggota']); ?></h3>
                                <small class="text-muted">Total Anggota</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-user-check fa-2x text-success me-3"></i>
                            <div>
                                <h3 class="mb-0"><?php echo e($stats['anggota_aktif']); ?></h3>
                                <small class="text-muted">Anggota Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-user-slash fa-2x text-warning me-3"></i>
                            <div>
                                <h3 class="mb-0"><?php echo e($stats['anggota_muted']); ?></h3>
                                <small class="text-muted">Anggota Muted</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Group Information -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i> Informasi Grup
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nama Grup:</th>
                                <td><?php echo e($group->name); ?></td>
                            </tr>
                            <tr>
                                <th>Kode Referral:</th>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded"><?php echo e($group->referral_code); ?></code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('<?php echo e($group->referral_code); ?>')" aria-label="Copy referral code" title="Salin kode referral">
                                        <i class="fas fa-copy" aria-hidden="true"></i>
                                        <span class="visually-hidden">Salin kode</span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Dibuat:</th>
                                <td><?php echo e($group->created_at->format('d M Y')); ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-success">Aktif</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php if($group->description): ?>
                    <div class="mt-3">
                        <strong>Deskripsi:</strong>
                        <p class="text-muted mb-0"><?php echo e($group->description); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Members -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-1"></i> Anggota Terbaru</span>
                <a href="<?php echo e(route('grup.anggota')); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body">
                <?php if($anggota->isEmpty()): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada anggota dalam grup ini.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php $__currentLoopData = $anggota->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card member-card">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="member-avatar me-3">
                                                <?php echo e(substr($member->name, 0, 1)); ?>

                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?php echo e($member->name); ?></h6>
                                                <small class="text-muted"><?php echo e($member->nim ?? 'Admin'); ?></small>
                                                <?php if($member->pivot->is_muted): ?>
                                                    <span class="badge bg-warning ms-2">Muted</span>
                                                <?php endif; ?>
                                                <?php if($member->role === 'admin_grup'): ?>
                                                    <span class="badge bg-primary ms-2">Admin</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <?php if($member->role !== 'admin_grup'): ?>
                                                        <li>
                                                            <form action="<?php echo e(route('grup.mute', $member->id)); ?>" method="POST" class="d-inline">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="group_id" value="<?php echo e($group->id); ?>">
                                                                <input type="hidden" name="duration" value="60">
                                                                <button type="submit" class="dropdown-item <?php echo e($member->pivot->is_muted ? 'text-success' : 'text-warning'); ?>" onclick="return confirm('<?php echo e($member->pivot->is_muted ? 'Unmute' : 'Mute'); ?> anggota ini?')">
                                                                    <i class="fas fa-<?php echo e($member->pivot->is_muted ? 'volume-up' : 'volume-mute'); ?> me-2"></i>
                                                                    <?php echo e($member->pivot->is_muted ? 'Unmute' : 'Mute'); ?>

                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="<?php echo e(route('grup.keluarkan', $member->id)); ?>" method="POST" class="d-inline">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="group_id" value="<?php echo e($group->id); ?>">
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Keluarkan anggota ini dari grup?')">
                                                                    <i class="fas fa-user-minus me-2"></i>
                                                                    Keluarkan
                                                                </button>
                                                            </form>
                                                        </li>
                                                    <?php else: ?>
                                                        <li><span class="dropdown-item text-muted">Admin grup</span></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <?php if($anggota->count() > 6): ?>
                        <div class="text-center mt-3">
                            <p class="text-muted">Dan <?php echo e($anggota->count() - 6); ?> anggota lainnya...</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<!-- Modal Edit Deskripsi UKM -->
<div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('grup.update-description')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editDescriptionModalLabel">Edit Deskripsi UKM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi UKM</label>
                        <textarea class="form-control" id="description" name="description" rows="5" 
                                  placeholder="Masukkan deskripsi UKM..."><?php echo e($ukm->description ?? ''); ?></textarea>
                        <small class="text-muted">Deskripsi ini akan ditampilkan kepada semua anggota UKM.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'alert alert-success position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; width: auto;';
            toast.innerHTML = '<i class="fas fa-check me-2"></i>Kode referral berhasil disalin!';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin_grup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\grup\dashboard.blade.php ENDPATH**/ ?>