@extends('layouts.admin')

@section('title', 'Anggota UKM - ' . ($ukm->nama ?? $ukm->name))

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
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card.blue { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .stat-card.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .stat-card.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .stat-card.amber { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.teal { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }
        .stat-card.indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .stat-card.pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .status-online {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .status-offline {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users me-2"></i>Anggota UKM: {{ $ukm->nama ?? $ukm->name }}
        </h1>
        <a href="{{ route('admin.ukms') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar UKM
        </a>
    </div>

    @if($ukm->description)
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-2"></i>Deskripsi UKM
            </h6>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $ukm->description }}</p>
        </div>
    </div>
    @endif

    {{-- Flash Messages --}}
    @foreach (['success', 'error', 'info'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show" role="alert">
                <i class="fas fa-{{ $msg === 'success' ? 'check-circle' : ($msg === 'error' ? 'exclamation-circle' : 'info-circle') }} me-2"></i>
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    {{-- UKM Info Card --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi UKM
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Nama UKM:</strong><br>
                            <span class="text-primary">{{ $ukm->nama ?? $ukm->name }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Kode UKM:</strong><br>
                            <code class="bg-light px-2 py-1 rounded">{{ $ukm->kode ?? $ukm->referral_code }}</code>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Anggota:</strong><br>
                            <span class="badge bg-info fs-6">{{ $anggota->count() }} anggota</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Dibuat:</strong><br>
                            <small class="text-muted">{{ $ukm->created_at ? $ukm->created_at->format('d/m/Y H:i') : '-' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row mb-4">
        <!-- Total Anggota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card blue h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Anggota</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $anggota->count() }}</div>
                            <div class="text-xs mt-1 opacity-75">Terdaftar</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Grup -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card purple h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Admin Grup</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $anggota->where('role', 'admin_grup')->count() }}</div>
                            <div class="text-xs mt-1 opacity-75">Aktif</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anggota Online -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card green h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Anggota Online</div>
                            @php 
                                $onlineCount = $anggota->filter(function($user) { 
                                    return $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5)); 
                                })->count();
                            @endphp
                            <div class="h5 mb-0 font-weight-bold">{{ $onlineCount }}</div>
                            <div class="text-xs mt-1 opacity-75">Aktif sekarang</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anggota Biasa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card teal h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Anggota Biasa</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $anggota->where('role', 'anggota')->count() }}</div>
                            <div class="text-xs mt-1 opacity-75">Member</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Anggota --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Anggota
                    </h5>
                    <div class="d-flex gap-2">
                        <!-- Search Form -->
                        <form method="GET" class="d-flex">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari anggota..." value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.ukm.anggota', $ukm->id) }}" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if($anggota->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                @if(request('search'))
                                    Tidak ada anggota yang ditemukan untuk pencarian "{{ request('search') }}"
                                @else
                                    Belum ada anggota pada UKM ini
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if(request('search'))
                                    Coba ubah kata kunci pencarian Anda
                                @else
                                    Anggota akan muncul di sini setelah bergabung dengan UKM
                                @endif
                            </p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>NIM</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Terakhir Login</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anggota as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-2">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px;">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $user->name }}</strong>
                                                        @if($user->email)
                                                            <br><small class="text-muted">{{ $user->email }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td><code>{{ $user->nim ?? '-' }}</code></td>
                                            <td>
                                                @php
                                                    $roleLabels = [
                                                        'admin_website' => ['Admin Website', 'danger'],
                                                        'admin_grup' => ['Admin Grup', 'warning'],
                                                        'anggota' => ['Anggota', 'primary']
                                                    ];
                                                    $roleData = $roleLabels[$user->role] ?? ['Unknown', 'secondary'];
                                                @endphp
                                                <span class="badge bg-{{ $roleData[1] }}">{{ $roleData[0] }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $isOnline = $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5));
                                                @endphp
                                                <span class="badge bg-{{ $isOnline ? 'success' : 'secondary' }}">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.6em;"></i>
                                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->last_seen_at)
                                                    <small>{{ $user->last_seen_at->format('d/m/Y H:i') }}</small><br>
                                                    <small class="text-muted">{{ $user->last_seen_at->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">Belum pernah online</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.member.show', $user->id) }}" 
                                                       class="btn btn-outline-info" 
                                                       title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->role === 'admin_website' && $user->role !== 'admin_website')
                                                        @if($user->role !== 'admin_grup')
                                                            <button type="button" 
                                                                    class="btn btn-outline-success" 
                                                                    title="Jadikan Admin Grup"
                                                                    onclick="confirmMakeAdmin({{ $user->id }}, '{{ $user->name }}')">
                                                                <i class="fas fa-user-shield"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" 
                                                                    class="btn btn-outline-warning" 
                                                                    title="Hapus Admin Grup"
                                                                    onclick="confirmRemoveAdmin({{ $user->id }}, '{{ $user->name }}')">
                                                                <i class="fas fa-user-minus"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" 
                                                                class="btn btn-outline-danger" 
                                                                title="Keluarkan dari UKM"
                                                                onclick="confirmRemoveMember({{ $user->id }}, '{{ $user->name }}')">
                                                            <i class="fas fa-user-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Function to confirm making user admin
    function confirmMakeAdmin(userId, userName) {
        if (confirm('Apakah Anda yakin ingin menjadikan "' + userName + '" sebagai Admin Grup?\n\nAdmin Grup dapat mengelola anggota UKM.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/users/' + userId + '/make-admin';
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Function to confirm removing admin
    function confirmRemoveAdmin(userId, userName) {
        if (confirm('Apakah Anda yakin ingin menghapus hak akses Admin Grup dari "' + userName + '"?\n\nUser akan kembali menjadi anggota biasa.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/users/' + userId + '/remove-admin';
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Function to confirm removing member from UKM
    function confirmRemoveMember(userId, userName) {
        if (confirm('Apakah Anda yakin ingin mengeluarkan "' + userName + '" dari UKM ini?\n\nAnggota akan kehilangan akses ke UKM.')) {
            // Create form and submit
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/ukm/{{ $ukm->id }}/keluarkan/' + userId;
            
            // Add CSRF token
            let csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush
