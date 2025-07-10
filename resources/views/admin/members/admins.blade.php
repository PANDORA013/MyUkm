@extends('layouts.admin')

@section('title', 'Daftar Admin Grup UKM')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Daftar Admin Grup UKM</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <!-- <th>Email</th> -->
                            <th>UKM</th>
                            <th>Grup</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adminGroupUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->nim }}</td>
                                <!-- <td>{{ $user->email }}</td> -->
                                <td>{{ $user->ukm ? $user->ukm->name : '-' }}</td>
                                <td>
                                    @foreach($user->groups as $group)
                                        <span class="badge bg-primary">{{ $group->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada admin grup UKM aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
