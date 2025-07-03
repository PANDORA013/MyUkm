@extends('layouts.admin')

@section('title', 'Detail Anggota')

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
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 2rem;
        }
        .info-row {
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Anggota</h1>
        <div>
            <a href="{{ route('admin.members') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            @if($member->role !== 'admin_website')
                <a href="{{ route('admin.member.edit', $member->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <button type="button" 
                        class="btn btn-danger" 
                        onclick="confirmDeleteMember({{ $member->id }}, '{{ $member->name }}')">
                    <i class="fas fa-trash me-2"></i>Hapus
                </button>
            @endif
        </div>
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
        {{-- Profile Card --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profil Anggota</h6>
                </div>
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <h5 class="font-weight-bold">{{ $member->name }}</h5>
                    <p class="text-muted">{{ $member->email }}</p>
                    
                    @php
                        $badgeClass = [
                            'admin_website' => 'bg-primary',
                            'admin_grup' => 'bg-success',
                            'member' => 'bg-secondary'
                        ][$member->role] ?? 'bg-secondary';
                    @endphp
                    <span class="badge {{ $badgeClass }} mb-3">
                        {{ ucfirst(str_replace('_', ' ', $member->role)) }}
                    </span>

                    @if($member->nim)
                        <div class="mt-3">
                            <small class="text-muted">NIM</small>
                            <div class="font-weight-bold">{{ $member->nim }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Details Card --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Detail</h6>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>ID Anggota</strong>
                            </div>
                            <div class="col-sm-9">
                                <code class="bg-light px-2 py-1 rounded">{{ $member->id }}</code>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>Nama Lengkap</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $member->name }}
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>Email</strong>
                            </div>
                            <div class="col-sm-9">
                                <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                            </div>
                        </div>
                    </div>

                    @if($member->nim)
                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>NIM</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $member->nim }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>Role</strong>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $member->role)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>Bergabung</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $member->created_at ? $member->created_at->format('d F Y, H:i') : 'Tidak diketahui' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="row">
                            <div class="col-sm-3">
                                <strong>Terakhir Aktif</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ $member->last_seen_at ? $member->last_seen_at->format('d F Y, H:i') : 'Tidak pernah login' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- UKM Membership Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keanggotaan UKM</h6>
                </div>
                <div class="card-body">
                    @if($member->groups->isNotEmpty())
                        <div class="row">
                            @foreach($member->groups as $group)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <h6 class="font-weight-bold mb-2">{{ $group->name }}</h6>
                                        <p class="text-muted mb-1">
                                            <strong>Kode:</strong> 
                                            <code class="bg-light px-2 py-1 rounded">{{ $group->referral_code }}</code>
                                        </p>
                                        <small class="text-muted">
                                            Bergabung: {{ $group->pivot->created_at ? $group->pivot->created_at->format('d/m/Y') : 'Tidak diketahui' }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                            <p>Anggota ini belum bergabung dengan UKM manapun</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
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
