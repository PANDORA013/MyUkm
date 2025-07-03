@extends('layouts.user')

@section('title', 'Profil Saya')

@push('styles')
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
        .profile-header {
            text-align: center;
            padding: 20px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
        }
        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .profile-nim {
            color: #6c757d;
            margin-bottom: 20px;
        }
        .btn-upload {
            margin-top: 10px;
        }
        .section-title {
            border-bottom: 2px solid #e3e6f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .password-form {
            background-color: #f8f9fc;
            border-radius: 0.5rem;
            padding: 20px;
        }
        .form-label {
            font-weight: 500;
            color: #5a5c69;
        }
        .danger-zone {
            background-color: #fdeded;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-top: 30px;
        }
        .membership-card {
            border-left: 4px solid #4e73df;
            margin-bottom: 15px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
    </div>
    
    {{-- Flash Messages --}}
    @foreach (['success', 'error', 'info'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach
    
    <div class="row">
        <!-- Profil dan Foto -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="profile-header">
                    @if ($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile" class="profile-img">
                    @else
                        <div class="profile-img d-flex align-items-center justify-content-center bg-light">
                            <span class="display-4 text-muted">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <h5 class="profile-name">{{ $user->name }}</h5>
                    <div class="profile-nim">{{ $user->nim }}</div>
                    
                    <form method="POST" action="{{ route('profile.updatePhoto') }}" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="photo" class="form-label d-block">Upload Foto Baru</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
                            <small class="text-muted">Max 2MB, format JPG/PNG</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i> Upload Foto
                        </button>
                    </form>
                </div>
                
                <div class="card-body">
                    <h5 class="section-title">Informasi Dasar</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>NIM</th>
                            <td>{{ $user->nim }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-info text-white">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Bergabung</th>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Ubah Password -->
        <div class="col-lg-8 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i> Ubah Password
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.updatePassword') }}" class="password-form">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" id="current_password" name="current_password" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password Baru</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Keanggotaan UKM -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i> Keanggotaan UKM
                </div>
                <div class="card-body">
                    @if ($memberships->count() > 0)
                        @foreach($memberships as $membership)
                            <div class="card membership-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $membership->ukm_name }}</h5>
                                    <div class="d-flex justify-content-between">
                                        <p class="card-text text-muted">
                                            <small>Bergabung sejak: {{ \Carbon\Carbon::parse($membership->joined_at)->format('d M Y') }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Anda belum tergabung dalam UKM manapun.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Hapus Akun -->
            <div class="card danger-zone mt-4">
                <div class="card-body">
                    <h5 class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Zona Berbahaya</h5>
                    <p>Menghapus akun akan menghapus semua data Anda dan tidak dapat dipulihkan.</p>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-1"></i> Hapus Akun Saya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Konfirmasi Penghapusan Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Apakah Anda yakin ingin menghapus akun Anda?</p>
                <p>Tindakan ini tidak dapat dibatalkan. Semua data Anda akan dihapus secara permanen.</p>
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="confirm_deletion" class="form-label">Ketik "HAPUS" untuk mengkonfirmasi</label>
                        <input type="text" class="form-control" id="confirm_deletion" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>Hapus Akun Saya</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmInput = document.getElementById('confirm_deletion');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const deleteForm = document.getElementById('deleteAccountForm');
        
        confirmInput.addEventListener('input', function() {
            confirmBtn.disabled = this.value !== 'HAPUS';
        });
        
        confirmBtn.addEventListener('click', function() {
            if (confirmInput.value === 'HAPUS') {
                deleteForm.submit();
            }
        });
    });
</script>
@endpush
