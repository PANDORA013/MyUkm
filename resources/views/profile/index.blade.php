@extends('layouts.admin')

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
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
        .profile-photo {
            height: 150px;
            width: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .profile-photo:hover {
            transform: scale(1.05);
            box-shadow: 0 0.8rem 1.5rem rgba(0,0,0,0.25);
        }
        .profile-photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 2;
        }
        .profile-photo:hover .profile-photo-overlay {
            opacity: 1;
        }
        .profile-photo-placeholder {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .admin-avatar {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }
        .user-avatar {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }
        .avatar-icon {
            width: 70%;
            height: 70%;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
        .avatar-shine {
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: skewX(-12deg);
            opacity: 0.6;
        }
        .admin-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #fbbf24;
            color: white;
            font-size: 1.2rem;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        /* Instagram-like Photo Upload Modal */
        .photo-upload-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .photo-upload-container {
            background: white;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        .photo-upload-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e3e3e3;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }
        .photo-upload-content {
            padding: 20px;
        }
        .photo-preview-container {
            width: 100%;
            height: 300px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .photo-preview-container.has-image {
            border: none;
        }
        .photo-preview {
            max-width: 100%;
            max-height: 100%;
            border-radius: 8px;
        }
        .upload-placeholder {
            text-align: center;
            color: #666;
        }
        .upload-placeholder i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #ddd;
        }
        .crop-container {
            width: 100%;
            height: 300px;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        .crop-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px auto;
        }
        .photo-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-instagram {
            background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-instagram:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
        }
        .btn-secondary-custom {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
        }
        .btn-secondary-custom:hover {
            background: #e9ecef;
            color: #495057;
        }
        .photo-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #666;
        }
        .loading-overlay {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border-radius: 8px;
        }
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
            </ol>
        </nav>
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

    <!-- Main Content -->
    <div class="row">
        <!-- Profile Details Card -->
        <div class="col-lg-4 mb-4">
            <!-- Photo Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-circle me-2"></i>Foto Profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-4">
                        <div class="profile-photo" onclick="openPhotoUpload()">
                            @if($user->photo && Storage::exists($user->photo))
                                <img src="{{ Storage::url($user->photo) }}" 
                                     alt="{{ $user->name }}" 
                                     class="h-100 w-100 object-cover"
                                     id="currentPhoto">
                            @else
                                <div class="profile-photo-placeholder {{ $user->role === 'admin' ? 'admin-avatar' : 'user-avatar' }}"
                                     id="photoPlaceholder">
                                    @if($user->role === 'admin')
                                        <!-- Admin Crown SVG -->
                                        <svg class="avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 16L3 7l5.5 5L12 4l3.5 8L21 7l-2 9H5zm2.7-2h8.6l.9-4.4-2.1 1.8L12 8.5l-3.1 2.9-2.1-1.8L7.7 14z"/>
                                            <circle cx="7" cy="7" r="1"/>
                                            <circle cx="12" cy="3" r="1"/>
                                            <circle cx="17" cy="7" r="1"/>
                                        </svg>
                                    @else
                                        <!-- User Pawn SVG -->
                                        <svg class="avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2M21 9V7L12 9L3 7V9C3 10.1 3.9 11 5 11V12.5L6.5 18H17.5L19 12.5V11C20.1 11 21 10.1 21 9Z"/>
                                        </svg>
                                    @endif
                                    <div class="avatar-shine"></div>
                                </div>
                            @endif
                            
                            <!-- Hover Overlay -->
                            <div class="profile-photo-overlay">
                                <div class="text-white text-center">
                                    <i class="fas fa-camera fs-4 mb-2"></i>
                                    <div style="font-size: 12px;">
                                        {{ $user->photo ? 'Ubah Foto' : 'Tambah Foto' }}
                                    </div>
                                </div>
                            </div>
                            
                            @if($user->role === 'admin')
                                <div class="admin-badge">
                                    ★
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->role === 'admin' ? 'Administrator' : 'Member' }}</p>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-instagram" onclick="openPhotoUpload()">
                            <i class="fas fa-camera me-2"></i>
                            {{ $user->photo ? 'Ganti Foto Profil' : 'Upload Foto Profil' }}
                        </button>
                        
                        @if($user->photo)
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removePhoto()">
                                <i class="fas fa-trash me-2"></i>Hapus Foto
                            </button>
                        @endif
                    </div>
                        </div>
                    </div>
                    <h4 class="font-weight-bold text-gray-800">{{ $user->name ?? 'Pengguna' }}</h4>
                    <p class="text-muted mb-4">{{ $user->nim ?? 'No NIM' }}</p>
                    <form action="{{ route('profile.updatePhoto') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="photo" class="form-label small text-muted">JPG atau PNG. Maksimal 2MB</label>
                            <input type="file" 
                                   name="photo" 
                                   id="photo" 
                                   accept="image/jpeg,image/png"
                                   class="form-control form-control-sm">
                            @error('photo')
                                <div class="small text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" 
                                class="btn btn-primary btn-sm">
                            <i class="fas fa-upload me-1"></i> Upload Foto
                        </button>
                    </form>
                </div>
            </div>

            <!-- Basic Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th class="bg-light" width="40%">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">NIM</th>
                            <td>{{ $user->nim }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Role</th>
                            <td>
                                @php
                                    $role = $user->role ?? 'anggota';
                                    
                                    $badgeClass = [
                                        'admin_website' => 'bg-primary',
                                        'admin_grup' => 'bg-success',
                                        'anggota' => 'bg-secondary',
                                        'member' => 'bg-secondary'
                                    ][$role] ?? 'bg-secondary';
                                    
                                    $roleText = [
                                        'admin_website' => 'Admin Website',
                                        'admin_grup' => 'Admin Grup',
                                        'anggota' => 'Anggota',
                                        'member' => 'Member'
                                    ][$role] ?? ucfirst($role);
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $roleText }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Bergabung</th>
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'Tidak tersedia' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Change Password -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.updatePassword') }}" 
                          method="POST" 
                          id="passwordForm">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       required
                                       class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       required
                                       minlength="8"
                                       class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       required
                                       minlength="8"
                                       class="form-control">
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

            <!-- Membership Table -->
            @if(Auth::user()->role !== 'admin_website')
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Keanggotaan UKM
                    </h6>
                    <span class="badge bg-primary">
                        Total: {{ count($memberships ?? []) }} UKM
                    </span>
                </div>
                <div class="card-body">
                    @if(isset($memberships) && count($memberships) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama UKM</th>
                                    <th>Tanggal Bergabung</th>
                                    <th>Status</th>
                                    <th>Terakhir Dilihat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($memberships as $membership)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($membership->ukm_name ?? 'U', 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $membership->ukm_name ?? 'UKM Tidak Ditemukan' }}</div>
                                                    <small class="text-muted">{{ $membership->joined_at ? \Carbon\Carbon::parse($membership->joined_at)->format('d/m/Y') : 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $membership->joined_at ? \Carbon\Carbon::parse($membership->joined_at)->translatedFormat('d F Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            @php
                                                $userRole = Auth::user() ? Auth::user()->role : 'anggota';
                                                $badgeClass = $userRole === 'admin_grup' ? 'bg-primary' : 'bg-secondary';
                                                $roleText = $userRole === 'admin_grup' ? 'Admin Grup' : 'Anggota';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $roleText }}</span>
                                        </td>
                                        <td>
                                            @if(isset($membership->is_online) && $membership->is_online)
                                                <span class="text-success fw-bold">
                                                    <i class="fas fa-circle text-success me-1" style="font-size: 0.6rem;"></i>Online
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ isset($membership->last_seen) && $membership->last_seen ? \Carbon\Carbon::parse($membership->last_seen)->diffForHumans() : 'Belum pernah online' }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-users-slash fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Tidak Ada Keanggotaan UKM</h5>
                        <p class="text-muted">Anda belum terdaftar sebagai anggota UKM manapun.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Delete Account -->
            <div class="card shadow mb-4 border-danger">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Hapus Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-exclamation-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Peringatan!</h5>
                                <p>
                                    Setelah akun dihapus, semua data akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                                    Pastikan Anda telah mencadangkan data penting sebelum melanjutkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('profile.destroy') }}" method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <div class="text-center">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i> Hapus Akun Saya
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Instagram-like Photo Upload Modal -->
    <div class="photo-upload-modal" id="photoUploadModal">
        <div class="photo-upload-container">
            <div class="photo-upload-header">
                <span style="float: left; cursor: pointer;" onclick="closePhotoUpload()">
                    <i class="fas fa-times"></i>
                </span>
                <span id="modalTitle">Upload Foto Profil</span>
                <span style="float: right;"></span>
            </div>
            
            <div class="photo-upload-content">
                <div class="photo-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Tips:</strong> Gunakan foto persegi untuk hasil terbaik. Ukuran maksimal 5MB.
                </div>
                
                <!-- Step 1: Choose Photo -->
                <div id="step1" class="upload-step">
                    <div class="photo-preview-container" id="photoPreviewContainer">
                        <div class="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p class="mb-2"><strong>Pilih foto dari komputer Anda</strong></p>
                            <p class="text-muted small">Format yang didukung: JPG, PNG, GIF</p>
                            <button type="button" class="btn btn-instagram mt-2" onclick="document.getElementById('photoInput').click()">
                                <i class="fas fa-folder-open me-2"></i>Pilih Foto
                            </button>
                        </div>
                    </div>
                    
                    <input type="file" id="photoInput" accept="image/*" style="display: none;">
                </div>
                
                <!-- Step 2: Crop Photo -->
                <div id="step2" class="upload-step d-none">
                    <div class="crop-container" id="cropContainer">
                        <img id="cropImage" style="display: none; max-width: 100%;">
                    </div>
                    
                    <div class="crop-preview" id="cropPreview"></div>
                    
                    <div class="photo-actions">
                        <button type="button" class="btn btn-secondary-custom flex-fill" onclick="backToStep1()">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </button>
                        <button type="button" class="btn btn-instagram flex-fill" onclick="uploadPhoto()">
                            <i class="fas fa-check me-2"></i>Upload Foto
                        </button>
                    </div>
                </div>
                
                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="loading-spinner"></div>
                    <p>Mengupload foto...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for photo upload -->
    <form id="photoUploadForm" action="{{ route('profile.updatePhoto') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="photo" id="hiddenPhotoInput">
    </form>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
let cropper;
let currentFile;

function openPhotoUpload() {
    document.getElementById('photoUploadModal').style.display = 'flex';
    resetModal();
}

function closePhotoUpload() {
    document.getElementById('photoUploadModal').style.display = 'none';
    resetModal();
}

function resetModal() {
    document.getElementById('step1').classList.remove('d-none');
    document.getElementById('step2').classList.add('d-none');
    document.getElementById('modalTitle').textContent = 'Upload Foto Profil';
    
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    
    document.getElementById('photoInput').value = '';
    document.getElementById('cropImage').style.display = 'none';
    
    // Reset preview container
    const container = document.getElementById('photoPreviewContainer');
    container.className = 'photo-preview-container';
    container.innerHTML = `
        <div class="upload-placeholder">
            <i class="fas fa-cloud-upload-alt"></i>
            <p class="mb-2"><strong>Pilih foto dari komputer Anda</strong></p>
            <p class="text-muted small">Format yang didukung: JPG, PNG, GIF</p>
            <button type="button" class="btn btn-instagram mt-2" onclick="document.getElementById('photoInput').click()">
                <i class="fas fa-folder-open me-2"></i>Pilih Foto
            </button>
        </div>
    `;
}

function backToStep1() {
    document.getElementById('step2').classList.add('d-none');
    document.getElementById('step1').classList.remove('d-none');
    document.getElementById('modalTitle').textContent = 'Upload Foto Profil';
    
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

// Photo input change handler
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Silakan pilih file gambar yang valid.');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 5MB.');
        return;
    }
    
    currentFile = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        // Show preview in step 1
        const container = document.getElementById('photoPreviewContainer');
        container.className = 'photo-preview-container has-image';
        container.innerHTML = `
            <img src="${e.target.result}" class="photo-preview" alt="Preview">
            <div style="position: absolute; bottom: 10px; right: 10px;">
                <button type="button" class="btn btn-sm btn-instagram" onclick="goToStep2()">
                    <i class="fas fa-crop me-2"></i>Crop & Upload
                </button>
            </div>
        `;
        
        // Prepare crop image
        document.getElementById('cropImage').src = e.target.result;
    };
    reader.readAsDataURL(file);
});

function goToStep2() {
    document.getElementById('step1').classList.add('d-none');
    document.getElementById('step2').classList.remove('d-none');
    document.getElementById('modalTitle').textContent = 'Crop Foto Profil';
    
    // Initialize cropper
    const image = document.getElementById('cropImage');
    image.style.display = 'block';
    
    cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 2,
        dragMode: 'move',
        background: false,
        autoCropArea: 0.8,
        restore: false,
        guides: false,
        center: false,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        preview: '#cropPreview'
    });
}

function uploadPhoto() {
    if (!cropper || !currentFile) return;
    
    // Show loading
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    // Get cropped canvas
    const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });
    
    // Convert to blob
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('photo', blob, 'profile-photo.jpg');
        formData.append('_token', '{{ csrf_token() }}');
        
        // Upload via fetch
        fetch('{{ route("profile.updatePhoto") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingOverlay').style.display = 'none';
            
            if (data.success) {
                // Update profile photo
                updateProfilePhoto(data.photo_url);
                closePhotoUpload();
                
                // Show success message
                showAlert('success', 'Foto profil berhasil diupdate!');
            } else {
                showAlert('error', data.message || 'Gagal mengupload foto.');
            }
        })
        .catch(error => {
            document.getElementById('loadingOverlay').style.display = 'none';
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat mengupload foto.');
        });
    }, 'image/jpeg', 0.9);
}

function updateProfilePhoto(photoUrl) {
    const currentPhoto = document.getElementById('currentPhoto');
    const placeholder = document.getElementById('photoPlaceholder');
    
    if (currentPhoto) {
        currentPhoto.src = photoUrl;
    } else if (placeholder) {
        // Replace placeholder with actual image
        const profilePhoto = placeholder.parentElement;
        profilePhoto.innerHTML = `
            <img src="${photoUrl}" alt="{{ $user->name }}" class="h-100 w-100 object-cover" id="currentPhoto">
            <div class="profile-photo-overlay">
                <div class="text-white text-center">
                    <i class="fas fa-camera fs-4 mb-2"></i>
                    <div style="font-size: 12px;">Ubah Foto</div>
                </div>
            </div>
            @if($user->role === 'admin')
                <div class="admin-badge">★</div>
            @endif
        `;
    }
    
    // Update avatar components throughout the app
    document.querySelectorAll('.avatar-container img, .user-avatar img').forEach(img => {
        if (img.alt === '{{ $user->name }}') {
            img.src = photoUrl;
        }
    });
}

function removePhoto() {
    if (!confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        return;
    }
    
    fetch('{{ route("profile.updatePhoto") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ remove_photo: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Revert to placeholder
            const profilePhoto = document.querySelector('.profile-photo');
            profilePhoto.innerHTML = `
                <div class="profile-photo-placeholder {{ $user->role === 'admin' ? 'admin-avatar' : 'user-avatar' }}" id="photoPlaceholder">
                    @if($user->role === 'admin')
                        <svg class="avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 16L3 7l5.5 5L12 4l3.5 8L21 7l-2 9H5zm2.7-2h8.6l.9-4.4-2.1 1.8L12 8.5l-3.1 2.9-2.1-1.8L7.7 14z"/>
                            <circle cx="7" cy="7" r="1"/>
                            <circle cx="12" cy="3" r="1"/>
                            <circle cx="17" cy="7" r="1"/>
                        </svg>
                    @else
                        <svg class="avatar-icon" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2M21 9V7L12 9L3 7V9C3 10.1 3.9 11 5 11V12.5L6.5 18H17.5L19 12.5V11C20.1 11 21 10.1 21 9Z"/>
                        </svg>
                    @endif
                    <div class="avatar-shine"></div>
                </div>
                <div class="profile-photo-overlay">
                    <div class="text-white text-center">
                        <i class="fas fa-camera fs-4 mb-2"></i>
                        <div style="font-size: 12px;">Tambah Foto</div>
                    </div>
                </div>
                @if($user->role === 'admin')
                    <div class="admin-badge">★</div>
                @endif
            `;
            
            // Update button text
            const uploadBtn = document.querySelector('.btn-instagram');
            uploadBtn.innerHTML = '<i class="fas fa-camera me-2"></i>Upload Foto Profil';
            
            // Hide remove button
            const removeBtn = document.querySelector('.btn-outline-danger');
            if (removeBtn) removeBtn.style.display = 'none';
            
            showAlert('success', 'Foto profil berhasil dihapus.');
        } else {
            showAlert('error', data.message || 'Gagal menghapus foto.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Terjadi kesalahan saat menghapus foto.');
    });
}

function showAlert(type, message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of main content
    const mainContent = document.querySelector('.row');
    mainContent.parentNode.insertBefore(alertDiv, mainContent);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Close modal on outside click
document.getElementById('photoUploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePhotoUpload();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoUpload();
    }
});
</script>
@endpush

@endsection

@push('scripts')
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Preview image before upload
        document.getElementById('photo').addEventListener('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('currentPhoto').classList.add('d-none');
                    document.getElementById('photoPlaceholder').classList.add('d-none');
                    var preview = document.getElementById('photoPreview');
                    preview.classList.remove('d-none');
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
