@extends('layouts.admin')

@section('title', 'Daftar Pengguna Aktif Bulan Ini')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Pengguna Aktif Bulan Ini</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Role</th>
                            <th>Terakhir Aktif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->nim }}</td>
                                <td>{{ $user->role }}</td>
                                <td>{{ $user->last_seen_at ? $user->last_seen_at->format('d-m-Y H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada pengguna aktif bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
