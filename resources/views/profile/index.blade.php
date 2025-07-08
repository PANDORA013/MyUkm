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
            height: 120px;
            width: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .profile-photo-placeholder {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e2e8f0;
            font-size: 2rem;
            font-weight: bold;
            color: #4e73df;
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
                        <div class="profile-photo">
                            @if($user->photo)
                                <img src="{{ Storage::url($user->photo) }}" 
                                     alt="{{ $user->name }}" 
                                     class="h-100 w-100 object-cover"
                                     id="currentPhoto">
                            @else
                                <div class="profile-photo-placeholder"
                                     id="photoPlaceholder">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <img id="photoPreview" class="h-100 w-100 object-cover d-none" alt="Preview">
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
