<?php $__env->startSection('title', 'Cari Anggota'); ?>

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
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cari Anggota</h1>
        <a href="<?php echo e(route('admin.members')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Anggota
        </a>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pencarian Anggota</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('admin.member.search')); ?>" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="q" class="form-label">Cari Anggota</label>
                    <input 
                        type="text" 
                        name="q" 
                        id="q"
                        class="form-control" 
                        placeholder="Masukkan nama, NIM, atau email anggota"
                        value="<?php echo e($query); ?>"
                    >
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Cari Anggota
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Pencarian</h6>
            <?php if($query): ?>
                <div class="text-muted small">
                    Pencarian: "<strong><?php echo e($query); ?></strong>" - <?php echo e($users->total()); ?> hasil ditemukan
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if($query): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Anggota</th>
                                <th>NIM</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Terakhir Akses</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </div>
                                            <div>
                                                <div class="font-weight-bold"><?php echo e($user->name); ?></div>
                                                <div class="text-muted small">ID: <?php echo e($user->id); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded"><?php echo e($user->nim ?? '-'); ?></code>
                                    </td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php if($user->role == 'admin_website'): ?>
                                            <span class="badge bg-danger">Admin Website</span>
                                        <?php elseif($user->role == 'admin_grup'): ?>
                                            <span class="badge bg-warning">Admin Grup</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Member</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo e($user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Belum pernah login'); ?>

                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo e(route('admin.member-ukms', $user->id)); ?>" 
                                               class="btn btn-outline-info" 
                                               title="Lihat UKM"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.member.show', $user->id)); ?>" 
                                               class="btn btn-outline-primary" 
                                               title="Lihat Detail"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.member.edit', $user->id)); ?>" 
                                               class="btn btn-outline-warning" 
                                               title="Edit"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if($user->role !== 'admin_website'): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Hapus Anggota"
                                                        data-bs-toggle="tooltip"
                                                        onclick="confirmDeleteMember(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-search me-2"></i>Tidak ada anggota yang ditemukan untuk pencarian "<?php echo e($query); ?>"
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <?php if($users->hasPages()): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Menampilkan <?php echo e($users->firstItem()); ?> - <?php echo e($users->lastItem()); ?> 
                            dari <?php echo e($users->total()); ?> hasil
                        </div>
                        <?php echo e($users->withQueryString()->links()); ?>

                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Pencarian Anggota</h5>
                    <p class="text-muted">
                        Masukkan nama, NIM, atau email anggota pada form pencarian di atas untuk mulai mencari.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Auto-focus search input
    document.getElementById('q').focus();
    
    // Function to confirm member deletion
    function confirmDeleteMember(memberId, memberName) {
        if (confirm('Apakah Anda yakin ingin menghapus anggota "' + memberName + '"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait anggota ini.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/member/' + memberId;
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfToken);
            
            // Add method spoofing for DELETE
            let methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin\member_search.blade.php ENDPATH**/ ?>