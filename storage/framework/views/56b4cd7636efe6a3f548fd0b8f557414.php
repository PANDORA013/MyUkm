

<?php $__env->startSection('title', 'Kelola Anggota UKM'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .member-card {
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .member-card:hover {
        border-left-color: #16a085;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .member-avatar {
        width: 50px;
        height: 50px;
        background-color: #16a085;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.2rem;
    }
    .page-header {
        margin-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }
    .search-box {
        max-width: 400px;
    }
    .member-status {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .action-buttons {
        display: flex;
        gap: 0.25rem;
    }
    .filter-tabs {
        margin-bottom: 1rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <h4 class="page-header">
        <i class="fas fa-users me-2"></i>Kelola Anggota UKM
        <?php if($group): ?>
            <span class="text-muted">- <?php echo e($group->name); ?></span>
        <?php endif; ?>
    </h4>
    
    
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
            Anda belum tergabung dalam grup UKM manapun. Silakan hubungi administrator untuk bergabung dengan grup.
        </div>
    <?php else: ?>
        <!-- UKM Description Card -->
        <?php
            $ukm = \App\Models\UKM::where('code', $group->referral_code)->first();
        ?>
        
        <?php if($ukm && $ukm->description): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i> Tentang UKM
                </h6>
                <?php if(auth()->user()->role === 'admin_grup'): ?>
                    <a href="<?php echo e(route('grup.dashboard')); ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i> Edit Deskripsi
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <p class="mb-0"><?php echo e($ukm->description); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="search-box">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchMember" placeholder="Cari anggota..." onkeyup="filterMembers()">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="statusFilter" id="all" value="all" checked onchange="filterByStatus()">
                            <label class="btn btn-outline-primary btn-sm" for="all">Semua</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="active" value="active" onchange="filterByStatus()">
                            <label class="btn btn-outline-success btn-sm" for="active">Aktif</label>
                            
                            <input type="radio" class="btn-check" name="statusFilter" id="muted" value="muted" onchange="filterByStatus()">
                            <label class="btn btn-outline-warning btn-sm" for="muted">Muted</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-1"></i> Daftar Anggota (<?php echo e($anggota->count()); ?>)</span>
                <small class="text-muted">Klik pada anggota untuk melihat detail</small>
            </div>
            <div class="card-body">
                <?php if($anggota->isEmpty()): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada anggota dalam grup ini</h5>
                        <p class="text-muted">Bagikan kode referral <code class="bg-light px-2 py-1 rounded"><?php echo e($group->referral_code); ?></code> untuk mengundang anggota baru.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3" id="membersContainer">
                        <?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-6 col-xl-4 member-item" 
                                 data-name="<?php echo e(strtolower($member->name)); ?>" 
                                 data-nim="<?php echo e(strtolower($member->nim ?? '')); ?>"
                                 data-status="<?php echo e($member->pivot->is_muted ? 'muted' : 'active'); ?>"
                                 data-role="<?php echo e($member->role); ?>">
                                <div class="card member-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start">
                                            <div class="member-avatar me-3">
                                                <?php echo e(substr($member->name, 0, 1)); ?>

                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold"><?php echo e($member->name); ?></h6>
                                                        <p class="mb-1 text-muted small">
                                                            <?php if($member->role === 'admin_grup'): ?>
                                                                <i class="fas fa-crown text-warning me-1"></i>Admin Grup
                                                            <?php else: ?>
                                                                <i class="fas fa-id-card me-1"></i><?php echo e($member->nim); ?>

                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                    <?php if($member->role !== 'admin_grup'): ?>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <form action="<?php echo e(route('grup.mute', $member->id)); ?>" method="POST" class="d-inline">
                                                                        <?php echo csrf_field(); ?>
                                                                        <button type="submit" class="dropdown-item <?php echo e($member->pivot->is_muted ? 'text-success' : 'text-warning'); ?>" onclick="return confirm('<?php echo e($member->pivot->is_muted ? 'Unmute' : 'Mute'); ?> anggota <?php echo e($member->name); ?>?')">
                                                                            <i class="fas fa-<?php echo e($member->pivot->is_muted ? 'volume-up' : 'volume-mute'); ?> me-2"></i>
                                                                            <?php echo e($member->pivot->is_muted ? 'Unmute' : 'Mute'); ?>

                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <form action="<?php echo e(route('grup.keluarkan', $member->id)); ?>" method="POST" class="d-inline">
                                                                        <?php echo csrf_field(); ?>
                                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Keluarkan <?php echo e($member->name); ?> dari grup? Tindakan ini tidak dapat dibatalkan.')">
                                                                            <i class="fas fa-user-minus me-2"></i>
                                                                            Keluarkan
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="member-status">
                                                    <?php if($member->role === 'admin_grup'): ?>
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-crown me-1"></i>Admin
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">
                                                            <i class="fas fa-user me-1"></i>Anggota
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($member->pivot->is_muted): ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-volume-mute me-1"></i>Muted
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-volume-up me-1"></i>Aktif
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-plus me-1"></i>
                                                        Bergabung: <?php echo e(\Carbon\Carbon::parse($member->pivot->created_at)->format('d M Y')); ?>

                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <div id="noResults" class="text-center py-5" style="display: none;">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada anggota yang ditemukan</h5>
                        <p class="text-muted">Coba ubah kata kunci pencarian atau filter yang digunakan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <?php if($anggota->count() > 0): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i> Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Bagikan Kode Referral</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?php echo e($group->referral_code); ?>" readonly>
                                <button class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo e($group->referral_code); ?>')">
                                    <i class="fas fa-copy me-1"></i>Salin
                                </button>
                            </div>
                            <small class="text-muted">Bagikan kode ini untuk mengundang anggota baru</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Statistik Grup</h6>
                            <div class="d-flex gap-3">
                                <span class="badge bg-primary fs-6"><?php echo e($anggota->count()); ?> Total</span>
                                <span class="badge bg-success fs-6"><?php echo e($anggota->where('pivot.is_muted', false)->count()); ?> Aktif</span>
                                <span class="badge bg-warning fs-6"><?php echo e($anggota->where('pivot.is_muted', true)->count()); ?> Muted</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function filterMembers() {
        const searchTerm = document.getElementById('searchMember').value.toLowerCase();
        const members = document.querySelectorAll('.member-item');
        let visibleCount = 0;
        
        members.forEach(member => {
            const name = member.dataset.name;
            const nim = member.dataset.nim;
            
            if (name.includes(searchTerm) || nim.includes(searchTerm)) {
                member.style.display = 'block';
                visibleCount++;
            } else {
                member.style.display = 'none';
            }
        });
        
        document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
    }
    
    function filterByStatus() {
        const selectedStatus = document.querySelector('input[name="statusFilter"]:checked').value;
        const members = document.querySelectorAll('.member-item');
        let visibleCount = 0;
        
        members.forEach(member => {
            const memberStatus = member.dataset.status;
            const memberRole = member.dataset.role;
            
            if (selectedStatus === 'all' || memberStatus === selectedStatus) {
                member.style.display = 'block';
                visibleCount++;
            } else {
                member.style.display = 'none';
            }
        });
        
        // Apply search filter after status filter
        filterMembers();
    }
    
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

<?php echo $__env->make('layouts.admin_grup', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\grup\anggota.blade.php ENDPATH**/ ?>