@extends(Auth::user()->role === 'admin_grup' ? 'layouts.admin_grup' : 'layouts.user')

@section('title', 'UKM Tersedia')

@push('styles')
<style>
    .ukm-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
        margin-bottom: 0;
    }
    .ukm-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .ukm-badge {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .join-form {
        max-width: 400px;
        margin: 0 auto;
    }
    .card-header {
        padding: 0.75rem 1rem;
    }
    .section-title {
        margin-bottom: 1rem;
        font-size: 1.2rem;
        font-weight: 600;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.5rem;
    }
    .card-body {
        padding: 1rem;
    }
    .alert {
        margin-bottom: 1rem;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .page-header {
        margin-bottom: 1rem;
        border-bottom: 1px solid #e3e6f0;
        padding-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <h4 class="page-header">
        <i class="fas fa-university me-2"></i>Daftar UKM Tersedia
    </h4>
    
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Gabung UKM</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show py-2" role="alert">
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('ukm.join') }}" class="join-form">
                        @csrf
                        <div class="mb-0">
                            <label for="group_code" class="form-label">Kode Referral UKM</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('group_code') is-invalid @enderror" 
                                    id="group_code" name="group_code" placeholder="Masukkan kode 4 digit" 
                                    maxlength="4" required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane me-1"></i> Gabung
                                </button>
                            </div>
                            @error('group_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Dapatkan kode referral dari admin atau ketua UKM.</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($joinedGroups->count() > 0)
    <div class="mt-3">
        <div class="section-title">
            <i class="fas fa-check-circle me-2"></i>UKM yang Diikuti
        </div>
        <div class="row g-3">
            @foreach($joinedGroups as $group)
            <div class="col-lg-4 col-md-6">
                <div class="card ukm-card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-2">
                        <h5 class="mb-0">{{ $group->name }}</h5>
                        @if($group->isUserAdminInGroup ?? false)
                            <span class="badge bg-warning text-dark ukm-badge">Admin Grup</span>
                        @else
                            <span class="badge bg-success ukm-badge">Anggota</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @php
                            $ukm = \App\Models\UKM::where('code', $group->referral_code)->first();
                        @endphp
                        <p class="card-text">{{ $ukm && $ukm->description ? $ukm->description : 'Tidak ada deskripsi tersedia.' }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-users me-1"></i> {{ $group->members->count() }} Anggota
                                </span>
                                @if($group->userRoleInGroup ?? null)
                                    <small class="text-muted d-block mt-1">
                                        Status: 
                                        @if($group->userRoleInGroup === 'admin')
                                            <strong class="text-warning">Admin Grup</strong>
                                        @else
                                            <strong class="text-success">Anggota</strong>
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="btn-group mt-3 w-100" role="group">
                            <a href="{{ route('ukm.show', $group->referral_code) }}" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                            <a href="{{ route('ukm.chat', $group->referral_code) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-comments me-1"></i> Chat
                            </a>
                            @if($group->isUserAdminInGroup ?? false)
                                <a href="{{ route('group.admin.dashboard', $group->referral_code) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-cog me-1"></i> Kelola
                                </a>
                            @endif
                        </div>
                        
                        <div class="mt-2">
                            <form action="{{ route('ukm.leave', $group->referral_code) }}" method="POST" class="d-inline w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Yakin ingin keluar dari UKM ini?')">
                                    <i class="fas fa-sign-out-alt me-1"></i> Keluar dari UKM
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($availableGroups->count() > 0)
    <div class="mt-3">
        <div class="section-title">
            <i class="fas fa-list-alt me-2"></i>UKM Lainnya
        </div>
        <div class="row g-3">
            @foreach($availableGroups as $group)
            <div class="col-lg-4 col-md-6">
                <div class="card ukm-card">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-2">
                        <h5 class="mb-0">{{ $group->name }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $group->description ?? 'Tidak ada deskripsi tersedia.' }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-users me-1"></i> {{ $group->members->count() }} Anggota
                                </span>
                            </div>
                            <div>
                                <span class="text-muted small">Masukkan kode referral untuk bergabung</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($joinedGroups->count() === 0 && $availableGroups->count() === 0)
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle me-2"></i> Belum ada UKM yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.
    </div>
    @endif
</div>
@endsection
