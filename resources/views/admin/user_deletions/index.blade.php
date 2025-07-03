@extends('layouts.admin')

@section('title', 'Riwayat Penghapusan Pengguna')

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
        .deleted-user {
            background-color: #fff5f5;
        }
        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Penghapusan Pengguna</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Riwayat Penghapusan</li>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-2"></i>Daftar Akun yang Dihapus
            </h6>
            <div class="text-muted small">
                Total: {{ $deletions->total() }} akun
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dihapus Oleh</th>
                            <th>Tanggal Hapus</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deletions as $deletion)
                            <tr>
                                <td><code>{{ $deletion->user_id }}</code></td>
                                <td>
                                    <div class="fw-bold">{{ $deletion->name }}</div>
                                </td>
                                <td>{{ $deletion->nim ?? '-' }}</td>
                                <td>{{ $deletion->email ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'admin_website' => 'bg-primary',
                                            'admin_grup' => 'bg-success',
                                            'anggota' => 'bg-secondary',
                                            'member' => 'bg-secondary'
                                        ][$deletion->role] ?? 'bg-secondary';
                                        
                                        $roleText = [
                                            'admin_website' => 'Admin Website',
                                            'admin_grup' => 'Admin Grup',
                                            'anggota' => 'Anggota',
                                            'member' => 'Member'
                                        ][$deletion->role] ?? ucfirst($deletion->role);
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $roleText }}
                                    </span>
                                </td>
                                <td>
                                    @if($deletion->deletedBy)
                                        <div class="fw-bold">{{ $deletion->deletedBy->name }}</div>
                                        <small class="text-muted">{{ $deletion->deletedBy->role }}</small>
                                    @else
                                        <span class="text-muted">Sistem</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $deletion->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $deletion->created_at->format('H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.user-deletions.show', $deletion->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Lihat Detail"
                                       data-bs-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i>Tidak ada data penghapusan pengguna
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($deletions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $deletions->links() }}
                </div>
            @endif
        </div>
    </div>

@push('scripts')
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
@endpush

@endsection
