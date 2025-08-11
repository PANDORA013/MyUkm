@extends('layouts.user')

@section('title', 'Kelola Anggota - ' . $group->name)

@push('styles')
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
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Kelola Anggota</h1>
                    <p class="text-muted mb-0">Grup {{ $group->name }} - {{ $members->count() }} anggota</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('group.admin.dashboard', $group->referral_code) }}" class="btn btn-outline-secondary">
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
                    @if($members->count() > 0)
                        <div class="row g-3" id="membersContainer">
                            @foreach($members as $member)
                                <div class="col-lg-6 col-xl-4 member-item" data-role="{{ $member->pivot->is_admin ? 'admin' : 'member' }}">
                                    <div class="card member-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                @include('components.user-avatar', [
                                                    'user' => $member, 
                                                    'size' => 'md', 
                                                    'isGroupAdmin' => $member->pivot->is_admin
                                                ])
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ $member->name }}</h6>
                                                    <p class="text-muted mb-2 small">{{ $member->nim }}</p>
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        @if($member->pivot->is_admin)
                                                            <span class="badge bg-warning text-dark role-badge">Admin Grup</span>
                                                        @else
                                                            <span class="badge bg-secondary role-badge">Anggota</span>
                                                        @endif
                                                        
                                                        @if($member->role === 'admin_website')
                                                            <span class="badge bg-danger role-badge">Admin Website</span>
                                                        @endif
                                                        
                                                        @if($member->pivot->is_muted)
                                                            <span class="badge bg-dark role-badge">Dibisukan</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="member-actions mt-3">
                                                        <div class="btn-group btn-group-sm w-100" role="group">
                                                            @if($member->pivot->is_admin)
                                                                @if(Auth::id() !== $member->id)
                                                                    <button type="button" class="btn btn-outline-warning" 
                                                                            onclick="demoteToMember({{ $member->id }}, '{{ $member->name }}')">
                                                                        <i class="fas fa-arrow-down"></i> Turunkan
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn btn-outline-secondary" disabled>
                                                                        <i class="fas fa-user"></i> Anda
                                                                    </button>
                                                                @endif
                                                            @else
                                                                <button type="button" class="btn btn-outline-warning"
                                                                        onclick="promoteToAdmin({{ $member->id }}, '{{ $member->name }}')">
                                                                    <i class="fas fa-arrow-up"></i> Jadikan Admin
                                                                </button>
                                                            @endif
                                                            
                                                            @if(Auth::id() !== $member->id)
                                                                <button type="button" class="btn btn-outline-danger"
                                                                        onclick="removeMember({{ $member->id }}, '{{ $member->name }}')">
                                                                    <i class="fas fa-user-times"></i> Keluarkan
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>Belum Ada Anggota</h5>
                            <p class="text-muted">Bagikan kode referral untuk mengundang anggota baru.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
        fetch(`{{ route('group.admin.promote', $group->referral_code) }}`, {
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
        fetch(`{{ route('group.admin.demote', $group->referral_code) }}`, {
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
        fetch(`{{ route('group.admin.remove-member', $group->referral_code) }}`, {
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
@endpush
@endsection
