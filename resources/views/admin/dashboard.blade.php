@extends('layouts.admin')

@section('title', 'Dashboard Admin')

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
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
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

    {{-- Statistik Cards --}}
    <div class="row mb-4">
        <!-- Total Anggota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card blue h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Anggota</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($totalMembers) }}</div>
                            <div class="text-xs mt-1 opacity-75">Seluruh UKM</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total UKM -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card green h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total UKM</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($totalUkms) }}</div>
                            <div class="text-xs mt-1 opacity-75">Terdaftar</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Penghapusan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card red h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Akun Dihapus</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalDeletedAccounts }}</div>
                            <div class="text-xs mt-1 opacity-75">Riwayat Penghapusan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x opacity-75"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.user-deletions.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-eye me-1"></i>Lihat Riwayat
                        </a>
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
                            <div class="h5 mb-0 font-weight-bold">{{ $totalAdmins }}</div>
                            <div class="text-xs mt-1 opacity-75">Aktif</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Baris Kedua Statistik --}}
    <div class="row mb-4">
        <!-- Pengguna Aktif -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card amber h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Pengguna Aktif</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($activeUsersThisMonth) }}</div>
                            <div class="text-xs mt-1 opacity-75">Bulan Ini</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengguna Baru -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card teal h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Pengguna Baru</div>
                            <div class="h5 mb-0 font-weight-bold">+{{ number_format($newUsersThisMonth) }}</div>
                            <div class="text-xs mt-1 opacity-75">Bulan Ini</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rata-rata Keanggotaan -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card indigo h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Rata-rata</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalUkms > 0 ? number_format($totalMembers / $totalUkms, 1) : 0 }}</div>
                            <div class="text-xs mt-1 opacity-75">Anggota per UKM</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
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
