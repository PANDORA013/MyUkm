@extends('layouts.admin')

@section('title', 'Riwayat Penghapusan Pengguna')

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .deleted-user {
            background-color: #fff5f5;
        }
        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
        .table th {
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
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
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Riwayat Penghapusan Pengguna</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akun yang Dihapus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="deletionsTable" width="100%" cellspacing="0">
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
                                <td>{{ $deletion->user_id }}</td>
                                <td>{{ $deletion->name }}</td>
                                <td>{{ $deletion->nim ?? '-' }}</td>
                                <td>{{ $deletion->email ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'admin_website' => 'bg-primary',
                                            'admin_ukm' => 'bg-success',
                                            'member' => 'bg-secondary'
                                        ][$deletion->role] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $deletion->role)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $deletion->deletedBy ? $deletion->deletedBy->name : 'Sistem' }}
                                </td>
                                <td>{{ $deletion->created_at->format('d/m/Y H:i') }}</td>
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
                                <td colspan="8" class="text-center">Tidak ada data penghapusan pengguna</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#deletionsTable').DataTable({
                order: [[6, 'desc']], // Sort by created_at desc by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: [7] } // Disable sorting on action column
                ]
            });
        });
    </script>
@endpush
