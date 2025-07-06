

<?php $__env->startSection('title', 'Riwayat Penghapusan User'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Riwayat Penghapusan User
                    </h3>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if($deletions->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="deletionsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama User</th>
                                        <th>NIM</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Alasan Penghapusan</th>
                                        <th>Dihapus Oleh</th>
                                        <th>Waktu Penghapusan</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $deletions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $deletion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($deletions->firstItem() + $index); ?></td>
                                            <td>
                                                <strong><?php echo e($deletion->deleted_user_name); ?></strong>
                                                <small class="text-muted d-block">ID: <?php echo e($deletion->deleted_user_id); ?></small>
                                            </td>
                                            <td><?php echo e($deletion->deleted_user_nim); ?></td>
                                            <td><?php echo e($deletion->deleted_user_email ?? '-'); ?></td>
                                            <td>
                                                <?php if($deletion->deleted_user_role === 'admin_website'): ?>
                                                    <span class="badge bg-danger">Admin Website</span>
                                                <?php elseif($deletion->deleted_user_role === 'admin_grup'): ?>
                                                    <span class="badge bg-warning">Admin Grup</span>
                                                <?php elseif($deletion->deleted_user_role === 'member'): ?>
                                                    <span class="badge bg-info">Member</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e($deletion->deleted_user_role); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?php echo e($deletion->deletion_reason); ?></span>
                                            </td>
                                            <td>
                                                <?php if($deletion->deletedBy): ?>
                                                    <strong><?php echo e($deletion->deletedBy->name); ?></strong>
                                                    <small class="text-muted d-block"><?php echo e($deletion->deletedBy->nim); ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo e($deletion->created_at->format('d/m/Y H:i')); ?></strong>
                                                <small class="text-muted d-block"><?php echo e($deletion->created_at->diffForHumans()); ?></small>
                                            </td>
                                            <td>
                                                <?php echo e($deletion->deletion_notes ?? '-'); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($deletions->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-info-circle fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Belum ada riwayat penghapusan user</h5>
                            <p class="text-muted">Riwayat penghapusan akan muncul di sini ketika admin website menghapus user.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTable for better UX
    if ($('#deletionsTable').length > 0) {
        $('#deletionsTable').DataTable({
            "pageLength": 25,
            "responsive": true,
            "order": [[ 7, "desc" ]], // Sort by deletion date (newest first)
            "columnDefs": [
                {
                    "targets": [0, 7, 8], // No, Waktu Penghapusan, Catatan
                    "orderable": false
                }
            ],
            "language": {
                "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada entri yang tersedia",
                "infoFiltered": "(disaring dari _MAX_ total entri)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin\riwayat-penghapusan.blade.php ENDPATH**/ ?>