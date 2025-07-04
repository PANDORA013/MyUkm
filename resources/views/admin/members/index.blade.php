@extends('layouts.admin')

@section('title', 'Daftar Anggota')

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
        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Anggota</h1>
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

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Anggota</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.members') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Anggota</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        class="form-control" 
                        placeholder="Nama, NIM, atau Email"
                        value="{{ request('search') }}"
                    >
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">Semua Role</option>
                        <option value="admin_website" {{ request('role') == 'admin_website' ? 'selected' : '' }}>Admin Website</option>
                        <option value="admin_grup" {{ request('role') == 'admin_grup' ? 'selected' : '' }}>Admin Grup</option>
                        <option value="member" {{ request('role') == 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ukm" class="form-label">UKM</label>
                    <select name="ukm" id="ukm" class="form-control">
                        <option value="">Semua UKM</option>
                        @foreach($ukms as $ukm)
                            <option value="{{ $ukm->referral_code }}" {{ request('ukm') == $ukm->referral_code ? 'selected' : '' }}>
                                {{ $ukm->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Members List Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Anggota</h6>
            <div class="text-muted small">
                Total: {{ $members->total() }} anggota
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Anggota</th>
                            <th>NIM</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>UKM</th>
                            <th>Bergabung</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $member->name }}</div>
                                            <div class="text-muted small">ID: {{ $member->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $member->nim ?? '-' }}</code>
                                </td>
                                <td>{{ $member->email }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'admin_website' => 'bg-primary',
                                            'admin_grup' => 'bg-success',
                                            'member' => 'bg-secondary'
                                        ][$member->role] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $member->role)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($member->groups->isNotEmpty())
                                        @foreach($member->groups as $group)
                                            <span class="badge bg-info me-1 mb-1">{{ $group->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $member->created_at ? $member->created_at->format('d/m/Y') : '-' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.member.show', $member->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Lihat Detail"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($member->role !== 'admin_website')
                                            <a href="{{ route('admin.member.edit', $member->id) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Edit Anggota"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Hapus Anggota"
                                                    data-bs-toggle="tooltip"
                                                    onclick="deleteUser({{ $member->id }}, '{{ addslashes($member->name) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users me-2"></i>
                                    @if(request()->hasAny(['search', 'role', 'ukm']))
                                        Tidak ada anggota yang sesuai dengan filter
                                    @else
                                        Belum ada anggota terdaftar
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($members->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $members->appends(request()->query())->links() }}
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
        
        // Function to confirm member deletion
        function confirmDeleteMember(memberId, memberName) {
            if (confirm('Apakah Anda yakin ingin menghapus anggota "' + memberName + '"?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait anggota ini.')) {
                // Create form and submit
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/member/' + memberId;
                
                // Add CSRF token
                let csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                // Add method spoofing for DELETE
                let methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush

@endsection
