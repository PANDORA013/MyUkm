<?php $__env->startSection('title', 'Anggota UKM - ' . ($ukm->nama ?? $ukm->name)); ?>

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
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .stat-card.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .stat-card.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .stat-card.amber { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
        .stat-card.indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .stat-card.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .status-online {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .status-offline {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
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
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users me-2"></i>Anggota UKM: <?php echo e($ukm->nama ?? $ukm->name); ?>

        </h1>
        <a href="<?php echo e(route('admin.ukms')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar UKM
        </a>
    </div>

    <?php if($ukm->description): ?>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>Deskripsi UKM
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-0"><?php echo e($ukm->description); ?></p>
        </div>
    </div>
    <?php endif; ?>

    
    <?php $__currentLoopData = ['success', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session($msg)): ?>
            <div class="alert alert-<?php echo e($msg === 'error' ? 'danger' : $msg); ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo e($msg === 'success' ? 'check-circle' : ($msg === 'error' ? 'exclamation-circle' : 'info-circle')); ?> me-2"></i>
                <?php echo e(session($msg)); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi UKM
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Nama UKM:</strong><br>
                            <span class="text-primary"><?php echo e($ukm->nama ?? $ukm->name); ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Kode UKM:</strong><br>
                            <code class="bg-light px-2 py-1 rounded"><?php echo e($ukm->kode ?? $ukm->referral_code); ?></code>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Anggota:</strong><br>
                            <span class="badge bg-info fs-6"><?php echo e($anggota->count()); ?> anggota</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Dibuat:</strong><br>
                            <small class="text-muted"><?php echo e($ukm->created_at ? $ukm->created_at->format('d/m/Y H:i') : '-'); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row mb-4">
        <!-- Total Anggota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card blue h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Anggota</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo e($anggota->count()); ?></div>
                            <div class="text-xs mt-1 opacity-75">Terdaftar</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Grup -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card purple h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Admin Grup</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo e($anggota->where('pivot.is_admin', true)->count()); ?></div>
                            <div class="text-xs mt-1 opacity-75">Di grup ini</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anggota Online -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card green h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Anggota Online</div>
                            <?php 
                                $onlineCount = $anggota->filter(function($user) { 
                                    return $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5)); 
                                })->count();
                            ?>
                            <div class="h5 mb-0 font-weight-bold"><?php echo e($onlineCount); ?></div>
                            <div class="text-xs mt-1 opacity-75">Aktif sekarang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anggota Biasa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card teal h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Anggota Biasa</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo e($anggota->where('role', 'anggota')->count()); ?></div>
                            <div class="text-xs mt-1 opacity-75">Member</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Anggota
                    </h5>
                    <div class="d-flex gap-2">
                        <!-- Search Form -->
                        <form method="GET" class="d-flex">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari anggota..." value="<?php echo e(request('search')); ?>">
                                <button class="btn btn-outline-secondary" type="submit" aria-label="Cari anggota" title="Cari anggota berdasarkan nama atau NIM">
                                    <i class="fas fa-search"></i>
                                </button>
                                <?php if(request('search')): ?>
                                    <a href="<?php echo e(route('admin.ukm.anggota', $ukm->id)); ?>" class="btn btn-outline-danger" aria-label="Hapus pencarian" title="Hapus filter pencarian">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <?php if($anggota->isEmpty()): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                <?php if(request('search')): ?>
                                    Tidak ada anggota yang ditemukan untuk pencarian "<?php echo e(request('search')); ?>"
                                <?php else: ?>
                                    Belum ada anggota pada UKM ini
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted">
                                <?php if(request('search')): ?>
                                    Coba ubah kata kunci pencarian Anda
                                <?php else: ?>
                                    Anggota akan muncul di sini setelah bergabung dengan UKM
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Terakhir Login</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo e($user->name); ?></strong>
                                                        <?php if($user->email): ?>
                                                            <br><small class="text-muted"><?php echo e($user->email); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><code><?php echo e($user->nim ?? '-'); ?></code></td>
                                            <td>
                                                <?php
                                                    // Role global dan status admin per grup
                                                    $isAdminInGroup = $user->pivot && $user->pivot->is_admin;
                                                    
                                                    if ($user->role === 'admin_website') {
                                                        $badge = ['Admin Website', 'danger'];
                                                    } elseif ($isAdminInGroup) {
                                                        $badge = ['Admin Grup', 'warning'];
                                                    } else {
                                                        $badge = ['Anggota', 'primary'];
                                                    }
                                                ?>
                                                <span class="badge bg-<?php echo e($badge[1]); ?>"><?php echo e($badge[0]); ?></span>
                                                
                                                <?php if($user->role === 'admin_grup' && !$isAdminInGroup): ?>
                                                    <br><small class="text-muted">Global: Admin Grup</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $isOnline = $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5));
                                                ?>
                                                <span class="badge bg-<?php echo e($isOnline ? 'success' : 'secondary'); ?>">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.6em;"></i>
                                                    <?php echo e($isOnline ? 'Online' : 'Offline'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php if($user->last_seen_at): ?>
                                                    <small><?php echo e($user->last_seen_at->format('d/m/Y H:i')); ?></small><br>
                                                    <small class="text-muted"><?php echo e($user->last_seen_at->diffForHumans()); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Belum pernah online</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?php echo e(route('admin.member.show', $user->id)); ?>" 
                                                       class="btn btn-outline-info" 
                                                       title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if(auth()->user()->role === 'admin_website' && $user->role !== 'admin_website'): ?>
                                                        <?php
                                                            $isAdminInThisGroup = $user->pivot && $user->pivot->is_admin;
                                                        ?>
                                                        
                                                        <?php if(!$isAdminInThisGroup): ?>
                                                            <button type="button" 
                                                                    class="btn btn-outline-success" 
                                                                    title="Jadikan Admin di Grup Ini"
                                                                    onclick="confirmMakeAdminInGroup(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>', <?php echo e($ukm->id); ?>)">
                                                                <i class="fas fa-user-shield"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <button type="button" 
                                                                    class="btn btn-outline-warning" 
                                                                    title="Hapus Admin dari Grup Ini"
                                                                    onclick="confirmRemoveAdminFromGroup(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>', <?php echo e($ukm->id); ?>)">
                                                                <i class="fas fa-user-minus"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger" 
                                                                title="Keluarkan dari UKM"
                                                                onclick="confirmRemoveMember(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>')">
                                                            <i class="fas fa-user-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Function to confirm making user admin in specific group
    function confirmMakeAdminInGroup(userId, userName, ukmId) {
        if (confirm('Apakah Anda yakin ingin menjadikan "' + userName + '" sebagai Admin di grup ini?\n\nUser akan memiliki akses admin hanya di grup ini.')) {
            // Use fetch API with AJAX
            fetch('/admin/users/' + userId + '/promote-in-group', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({
                    ukm_id: ukmId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan');
            });
        }
    }
    
    // Function to confirm removing admin from specific group
    function confirmRemoveAdminFromGroup(userId, userName, ukmId) {
        if (confirm('Apakah Anda yakin ingin menghapus hak admin "' + userName + '" dari grup ini?\n\nUser akan tetap bisa jadi admin di grup lain.')) {
            // Use fetch API with AJAX
            fetch('/admin/users/' + userId + '/demote-from-group', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({
                    ukm_id: ukmId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan');
            });
        }
    }
    
    // Function to confirm removing member from UKM
    function confirmRemoveMember(userId, userName) {
        if (confirm('Apakah Anda yakin ingin mengeluarkan "' + userName + '" dari UKM ini?\n\nAnggota akan kehilangan akses ke UKM.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/ukm/<?php echo e($ukm->id); ?>/keluarkan/' + userId;
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin\ukm_anggota.blade.php ENDPATH**/ ?>