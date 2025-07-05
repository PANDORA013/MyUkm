@extends('layouts.admin')

@section('title', 'Riwayat Penghapusan User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Riwayat Penghapusan User
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($deletions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="deletionsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama User</th>
                                        <th>NIM</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Alasan Penghapusan</th>
                                        <th>Dihapus Oleh</th>
                                        <th>Waktu Penghapusan</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deletions as $index => $deletion)
                                        <tr>
                                            <td>{{ $deletions->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $deletion->deleted_user_name }}</strong>
                                                <small class="text-muted d-block">ID: {{ $deletion->deleted_user_id }}</small>
                                            </td>
                                            <td>{{ $deletion->deleted_user_nim }}</td>
                                            <td>{{ $deletion->deleted_user_email ?? '-' }}</td>
                                            <td>
                                                @if($deletion->deleted_user_role === 'admin_website')
                                                    <span class="badge bg-danger">Admin Website</span>
                                                @elseif($deletion->deleted_user_role === 'admin_grup')
                                                    <span class="badge bg-warning">Admin Grup</span>
                                                @elseif($deletion->deleted_user_role === 'member')
                                                    <span class="badge bg-info">Member</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $deletion->deleted_user_role }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $deletion->deletion_reason }}</span>
                                            </td>
                                            <td>
                                                @if($deletion->deletedBy)
                                                    <strong>{{ $deletion->deletedBy->name }}</strong>
                                                    <small class="text-muted d-block">{{ $deletion->deletedBy->nim }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $deletion->created_at->format('d/m/Y H:i') }}</strong>
                                                <small class="text-muted d-block">{{ $deletion->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                {{ $deletion->deletion_notes ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $deletions->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-info-circle fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Belum ada riwayat penghapusan user</h5>
                            <p class="text-muted">Riwayat penghapusan akan muncul di sini ketika admin website menghapus user.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for better UX
    if ($('#deletionsTable').length > 0) {
        $('#deletionsTable').DataTable({
            "pageLength": 25,
            "responsive": true,
            "order": [[ 7, "desc" ]], // Sort by deletion date (newest first)
            "columnDefs": [
                {
                    "targets": [0, 7, 8], // No, Waktu Penghapusan, Catatan
                    "orderable": false
                }
            ],
            "language": {
                "lengthMenu": "Tampilkan _MENU_ entri per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada entri yang tersedia",
                "infoFiltered": "(disaring dari _MAX_ total entri)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    }
});
</script>
@endpush
@endsection
