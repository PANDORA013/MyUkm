@extends('layouts.admin')

@section('title', 'Edit Anggota')

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
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
        }
        .form-control:focus {
            border-color: #5a67d8;
            box-shadow: 0 0 0 0.2rem rgba(90, 103, 216, 0.25);
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Anggota</h1>
        <div>
            <a href="{{ route('admin.member.show', $member->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
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
        {{-- Profile Preview Card --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Preview Profil</h6>
                </div>
                <div class="card-body text-center">
                    <div class="user-avatar mx-auto mb-3">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <h6 class="font-weight-bold">{{ $member->name }}</h6>
                    <p class="text-muted small">{{ $member->email }}</p>
                    <small class="text-muted">ID: {{ $member->id }}</small>
                </div>
            </div>
        </div>

        {{-- Edit Form Card --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Anggota</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.member.update', $member->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label font-weight-bold">Nama Lengkap</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name"
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $member->name) }}"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label font-weight-bold">Email</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email"
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email', $member->email) }}"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NIM --}}
                        <div class="mb-3">
                            <label for="nim" class="form-label font-weight-bold">NIM</label>
                            <input 
                                type="text" 
                                name="nim" 
                                id="nim"
                                class="form-control @error('nim') is-invalid @enderror" 
                                value="{{ old('nim', $member->nim) }}"
                                placeholder="Nomor Induk Mahasiswa (opsional)"
                            >
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="mb-3">
                            <label for="role" class="form-label font-weight-bold">Role</label>
                            <select 
                                name="role" 
                                id="role" 
                                class="form-control @error('role') is-invalid @enderror"
                                required
                            >
                                <option value="">Pilih Role</option>
                                <option value="member" {{ old('role', $member->role) == 'member' ? 'selected' : '' }}>
                                    Member
                                </option>
                                <option value="admin_grup" {{ old('role', $member->role) == 'admin_grup' ? 'selected' : '' }}>
                                    Admin Grup
                                </option>
                                @if(auth()->user()->role === 'admin_website')
                                    <option value="admin_website" {{ old('role', $member->role) == 'admin_website' ? 'selected' : '' }}>
                                        Admin Website
                                    </option>
                                @endif
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.member.show', $member->id) }}" class="btn btn-secondary me-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- UKM Membership Info Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keanggotaan UKM</h6>
                </div>
                <div class="card-body">
                    @if($member->groups->isNotEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi:</strong> Untuk mengubah keanggotaan UKM, anggota harus keluar dari UKM terlebih dahulu melalui halaman UKM.
                        </div>
                        <div class="row">
                            @foreach($member->groups as $group)
                                <div class="col-md-6 mb-2">
                                    <div class="border rounded p-3">
                                        <h6 class="font-weight-bold mb-1">{{ $group->name }}</h6>
                                        <small class="text-muted">
                                            Kode: <code class="bg-light px-1 rounded">{{ $group->referral_code }}</code>
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-users fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">Anggota ini belum bergabung dengan UKM manapun</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
