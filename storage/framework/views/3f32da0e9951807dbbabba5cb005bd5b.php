<?php $__env->startSection('title', 'Kelola UKM'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users-cog"></i> Kelola UKM
                    </h4>
                    <div class="d-flex gap-2">
                        <!-- Search Form -->
                        <form method="GET" action="<?php echo e(route('admin.ukms')); ?>" class="d-flex">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari UKM..." value="<?php echo e(request('search')); ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                <?php if(request('search')): ?>
                                    <a href="<?php echo e(route('admin.ukms')); ?>" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUkmModal">
                            <i class="fas fa-plus"></i> Tambah UKM
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($ukms->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama UKM</th>
                                        <th>Kode/Referral</th>
                                        <th>Jumlah Anggota</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $ukms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ukm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($ukms->firstItem() + $index); ?></td>
                                            <td>
                                                <strong><?php echo e($ukm->nama ?? $ukm->name); ?></strong>
                                            </td>
                                            <td>
                                                <code><?php echo e($ukm->kode ?? $ukm->referral_code); ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo e($ukm->members_count ?? 0); ?> anggota
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo e($ukm->created_at ? $ukm->created_at->format('d/m/Y H:i') : '-'); ?>

                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?php echo e(url('admin/ukm/' . $ukm->id . '/anggota')); ?>" 
                                                       class="btn btn-outline-info" 
                                                       title="Lihat Anggota">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="<?php echo e(url('admin/ukm/edit/' . $ukm->id)); ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit UKM">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            title="Hapus UKM"
                                                            onclick="confirmDeleteUKM(<?php echo e($ukm->id); ?>, '<?php echo e($ukm->nama ?? $ukm->name); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Menampilkan <?php echo e($ukms->firstItem()); ?> - <?php echo e($ukms->lastItem()); ?> 
                                dari <?php echo e($ukms->total()); ?> UKM
                            </div>
                            <?php echo e($ukms->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                <?php if(request('search')): ?>
                                    Tidak ada UKM yang ditemukan untuk pencarian "<?php echo e(request('search')); ?>"
                                <?php else: ?>
                                    Belum ada UKM yang terdaftar
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted">
                                <?php if(request('search')): ?>
                                    Coba ubah kata kunci pencarian Anda
                                <?php else: ?>
                                    Klik tombol "Tambah UKM" untuk menambahkan UKM pertama
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah UKM -->
<div class="modal fade" id="tambahUkmModal" tabindex="-1" aria-labelledby="tambahUkmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('admin.tambah-ukm')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahUkmModalLabel">
                        <i class="fas fa-plus"></i> Tambah UKM Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama UKM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label">Kode UKM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" maxlength="4" required>
                        <div class="form-text">Kode unik untuk UKM (maksimal 4 karakter, akan digunakan sebagai referral code)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
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
    
    // Function to confirm UKM deletion
    function confirmDeleteUKM(ukmId, ukmName) {
        if (confirm('Apakah Anda yakin ingin menghapus UKM "' + ukmName + '"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait UKM ini.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/ukm/' + ukmId;
            
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/admin/ukms/index.blade.php ENDPATH**/ ?>