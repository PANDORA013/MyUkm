@extends('layouts.user')

@section('title', 'Detail UKM - ' . $group->name)

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">{{ $group->name }}</h1>
                <a href="{{ route('ukm.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Group Info -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi UKM</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama UKM:</strong> {{ $group->name }}</p>
                            <p><strong>Kode Referral:</strong> {{ $group->referral_code }}</p>
                            <p><strong>Status:</strong> 
                                @if($group->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Anggota:</strong> {{ $members->count() }} orang</p>
                            <p><strong>Bergabung sejak:</strong> {{ $group->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    
                    @if($group->description)
                        <div class="mt-3">
                            <strong>Deskripsi:</strong>
                            <p class="mt-2">{{ $group->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Aksi</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($isMember)
                            <a href="{{ route('ukm.chat', $group->referral_code) }}" class="btn btn-primary">
                                <i class="fas fa-comments"></i> Chat Grup
                            </a>
                            
                            @if($isGroupAdmin)
                                <a href="{{ route('group.admin.dashboard', $group->referral_code) }}" class="btn btn-warning">
                                    <i class="fas fa-cog"></i> Kelola Grup
                                </a>
                                <a href="{{ route('group.admin.members', $group->referral_code) }}" class="btn btn-info">
                                    <i class="fas fa-users"></i> Kelola Anggota
                                </a>
                            @endif
                            
                            <form method="POST" action="{{ route('ukm.leave', $group->referral_code) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Apakah Anda yakin ingin keluar dari UKM ini?')">
                                    <i class="fas fa-sign-out-alt"></i> Keluar dari UKM
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('ukm.join') }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="referral_code" value="{{ $group->referral_code }}">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Bergabung
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    @if($isMember)
                        <div class="mt-3">
                            <small class="text-muted">
                                Status Anda di grup ini: 
                                @if($userRoleInGroup === 'admin')
                                    <span class="badge bg-warning text-dark">Admin Grup</span>
                                @else
                                    <span class="badge bg-secondary">Anggota</span>
                                @endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Daftar Anggota ({{ $members->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($members->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($members as $member)
                                <div class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="avatar-title rounded-circle bg-light text-dark">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fs-sm">{{ $member->name }}</h6>
                                            <small class="text-muted">
                                                @php
                                                    $membershipPivot = $member->pivot ?? null;
                                                    $isAdminInThisGroup = $membershipPivot && $membershipPivot->is_admin;
                                                @endphp
                                                
                                                @if($isAdminInThisGroup)
                                                    <span class="badge bg-warning text-dark">Admin Grup</span>
                                                @else
                                                    <span class="badge bg-secondary">Anggota</span>
                                                @endif
                                                
                                                @if($member->role === 'admin_website')
                                                    <span class="badge bg-danger ms-1">Admin Website</span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Belum ada anggota.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 600;
}

.fs-sm {
    font-size: 0.875rem;
}
</style>
@endsection
