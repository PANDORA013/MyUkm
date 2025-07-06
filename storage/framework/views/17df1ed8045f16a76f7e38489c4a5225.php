

<?php $__env->startSection('title', 'Kelola Anggota - ' . $group->name); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .member-card {
        transition: all 0.3s ease;
    }
    .member-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .member-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 1.1rem;
    }
    .member-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .member-card:hover .member-actions {
        opacity: 1;
    }
    .role-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Kelola Anggota</h1>
                    <p class="text-muted mb-0">Grup <?php echo e($group->name); ?> - <?php echo e($members->count()); ?> anggota</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('group.admin.dashboard', $group->referral_code)); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Members List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Anggota</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="filterRole" onchange="filterMembers()">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin Grup</option>
                                <option value="member">Anggota</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($members->count() > 0): ?>
                        <div class="row g-3" id="membersContainer">
                            <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-6 col-xl-4 member-item" data-role="<?php echo e($member->pivot->is_admin ? 'admin' : 'member'); ?>">
                                    <div class="card member-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="member-avatar me-3">
                                                    <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?php echo e($member->name); ?></h6>
                                                    <p class="text-muted mb-2 small"><?php echo e($member->nim); ?></p>
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <?php if($member->pivot->is_admin): ?>
                                                            <span class="badge bg-warning text-dark role-badge">Admin Grup</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary role-badge">Anggota</span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if($member->role === 'admin_website'): ?>
                                                            <span class="badge bg-danger role-badge">Admin Website</span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if($member->pivot->is_muted): ?>
                                                            <span class="badge bg-dark role-badge">Dibisukan</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div class="member-actions mt-3">
                                                        <div class="btn-group btn-group-sm w-100" role="group">
                                                            <?php if($member->pivot->is_admin): ?>
                                                                <?php if(Auth::id() !== $member->id): ?>
                                                                    <button type="button" class="btn btn-outline-warning" 
                                                                            onclick="demoteToMember(<?php echo e($member->id); ?>, '<?php echo e($member->name); ?>')">
                                                                        <i class="fas fa-arrow-down"></i> Turunkan
                                                                    </button>
                                                                <?php else: ?>
                                                                    <button type="button" class="btn btn-outline-secondary" disabled>
                                                                        <i class="fas fa-user"></i> Anda
                                                                    </button>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-outline-warning"
                                                                        onclick="promoteToAdmin(<?php echo e($member->id); ?>, '<?php echo e($member->name); ?>')">
                                                                    <i class="fas fa-arrow-up"></i> Jadikan Admin
                                                                </button>
                                                            <?php endif; ?>
                                                            
                                                            <?php if(Auth::id() !== $member->id): ?>
                                                                <button type="button" class="btn btn-outline-danger"
                                                                        onclick="removeMember(<?php echo e($member->id); ?>, '<?php echo e($member->name); ?>')">
                                                                    <i class="fas fa-user-times"></i> Keluarkan
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>Belum Ada Anggota</h5>
                            <p class="text-muted">Bagikan kode referral untuk mengundang anggota baru.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function filterMembers() {
    const filter = document.getElementById('filterRole').value;
    const members = document.querySelectorAll('.member-item');
    
    members.forEach(member => {
        if (filter === '' || member.dataset.role === filter) {
            member.style.display = 'block';
        } else {
            member.style.display = 'none';
        }
    });
}

function promoteToAdmin(userId, userName) {
    if (confirm(`Apakah Anda yakin ingin menjadikan ${userName} sebagai admin grup?`)) {
        fetch(`<?php echo e(route('group.admin.promote', $group->referral_code)); ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan jaringan');
        });
    }
}

function demoteToMember(userId, userName) {
    if (confirm(`Apakah Anda yakin ingin menurunkan ${userName} menjadi anggota biasa?`)) {
        fetch(`<?php echo e(route('group.admin.demote', $group->referral_code)); ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan jaringan');
        });
    }
}

function removeMember(userId, userName) {
    if (confirm(`Apakah Anda yakin ingin mengeluarkan ${userName} dari grup?`)) {
        fetch(`<?php echo e(route('group.admin.remove-member', $group->referral_code)); ?>`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan jaringan');
        });
    }
}

function showAlert(type, message) {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertContainer.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertContainer.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertContainer);
    
    setTimeout(() => {
        alertContainer.remove();
    }, 5000);
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\group\admin\members.blade.php ENDPATH**/ ?>