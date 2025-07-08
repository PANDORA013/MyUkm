@extends('layouts.admin_grup')

@section('title', 'Dashboard Admin Grup')

@push('styles')
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
@endpush

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-header mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin Grup
            @if($group)
                <span class="text-muted">- {{ $group->name }}</span>
            @endif
        </h4>
        
        @if($managed_groups->count() > 1)
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="groupSelector" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-exchange-alt me-2"></i>
                    {{ $group ? $group->name : 'Pilih UKM' }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="groupSelector">
                    @foreach($managed_groups as $managedGroup)
                        <li>
                            <a class="dropdown-item {{ $group && $group->id === $managedGroup->id ? 'active' : '' }}" 
                               href="{{ route('grup.dashboard', ['group_id' => $managedGroup->id]) }}">
                                <i class="fas fa-university me-2"></i>{{ $managedGroup->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    
    {{-- Flash Messages --}}
    @foreach (['success', 'error', 'info'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show py-2" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach
    
    @if(!$group)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Anda belum mengelola grup UKM manapun sebagai admin. Silakan hubungi administrator website untuk mendapatkan akses admin grup.
        </div>
    @else
        <!-- UKM Description Card -->
        @php
            $ukm = \App\Models\UKM::where('code', $group->referral_code)->first();
        @endphp
        
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i> Tentang UKM
                </h6>
                @if(auth()->user()->role === 'admin_grup')
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDescriptionModal" aria-label="Edit description" title="Edit deskripsi UKM">
                        <i class="fas fa-edit me-1" aria-hidden="true"></i> Edit Deskripsi
                    </button>
                @endif
            </div>
            <div class="card-body">
                @if($ukm && $ukm->description)
                    <p class="mb-0">{{ $ukm->description }}</p>
                @else
                    <p class="text-muted mb-0">
                        @if(auth()->user()->role === 'admin_grup')
                            Belum ada deskripsi untuk UKM ini. Klik tombol "Edit Deskripsi" untuk menambahkan informasi.
                        @else
                            Belum ada deskripsi untuk UKM ini.
                        @endif
                    </p>
                @endif
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
                                <h3 class="mb-0">{{ $stats['total_anggota'] }}</h3>
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
                                <h3 class="mb-0">{{ $stats['anggota_aktif'] }}</h3>
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
                                <h3 class="mb-0">{{ $stats['anggota_muted'] }}</h3>
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
                                <td>{{ $group->name }}</td>
                            </tr>
                            <tr>
                                <th>Kode Referral:</th>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $group->referral_code }}</code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="copyToClipboard('{{ $group->referral_code }}')" aria-label="Copy referral code" title="Salin kode referral">
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
                                <td>{{ $group->created_at->format('d M Y') }}</td>
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
                @if($group->description)
                    <div class="mt-3">
                        <strong>Deskripsi:</strong>
                        <p class="text-muted mb-0">{{ $group->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Members -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-users me-1"></i> Anggota Terbaru</span>
                <a href="{{ route('grup.anggota') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($anggota->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada anggota dalam grup ini.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($anggota->take(6) as $member)
                            <div class="col-lg-4 col-md-6">
                                <div class="card member-card">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="member-avatar me-3">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $member->name }}</h6>
                                                <small class="text-muted">{{ $member->nim ?? 'Admin' }}</small>
                                                @if($member->pivot->is_muted)
                                                    <span class="badge bg-warning ms-2">Muted</span>
                                                @endif
                                                @if($member->role === 'admin_grup')
                                                    <span class="badge bg-primary ms-2">Admin</span>
                                                @endif
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if($member->role !== 'admin_grup')
                                                        <li>
                                                            <form action="{{ route('grup.mute', $member->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                                                <input type="hidden" name="duration" value="60">
                                                                <button type="submit" class="dropdown-item {{ $member->pivot->is_muted ? 'text-success' : 'text-warning' }}" onclick="return confirm('{{ $member->pivot->is_muted ? 'Unmute' : 'Mute' }} anggota ini?')">
                                                                    <i class="fas fa-{{ $member->pivot->is_muted ? 'volume-up' : 'volume-mute' }} me-2"></i>
                                                                    {{ $member->pivot->is_muted ? 'Unmute' : 'Mute' }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('grup.keluarkan', $member->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Keluarkan anggota ini dari grup?')">
                                                                    <i class="fas fa-user-minus me-2"></i>
                                                                    Keluarkan
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li><span class="dropdown-item text-muted">Admin grup</span></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($anggota->count() > 6)
                        <div class="text-center mt-3">
                            <p class="text-muted">Dan {{ $anggota->count() - 6 }} anggota lainnya...</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

<!-- Modal Edit Deskripsi UKM -->
<div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-labelledby="editDescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('grup.update-description') }}">
                @csrf
                <input type="hidden" name="ukm_id" value="{{ $group->ukm_id }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDescriptionModalLabel">Edit Deskripsi UKM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi UKM</label>
                        <textarea class="form-control" id="description" name="description" rows="5" 
                                  placeholder="Masukkan deskripsi UKM...">{{ $ukm->description ?? '' }}</textarea>
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

@push('scripts')
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
@endpush
