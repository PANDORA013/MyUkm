@extends('layouts.app')

@section('title', 'Anggota UKM')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-6">
    <div class="max-w-5xl mx-auto space-y-8">
        {{-- Flash Messages --}}
        @foreach (['success', 'error', 'info'] as $msg)
            @if(session($msg))
                <div class="px-4 py-3 rounded-lg shadow-sm border {{ $msg==='success' ? 'bg-green-100 border-green-200 text-green-700' : ($msg==='error' ? 'bg-red-100 border-red-200 text-red-700' : 'bg-blue-100 border-blue-200 text-blue-700') }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold text-gray-800">Daftar Anggota</h2>
            <a href="{{ url('/admin/dashboard') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali ke Dashboard</a>
        </div>

        <section class="bg-white border border-gray-200 shadow-sm rounded-xl p-6">
            @if($anggota->isEmpty())
                <p class="text-gray-500">Belum ada anggota pada UKM ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Nama</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">NIM</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Role</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Terakhir Login</th>
                                {{-- Kolom Aksi Dihapus --}}
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($anggota as $user)
                                <tr>
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2 font-mono">{{ $user->nim }}</td>
                                    <td class="px-4 py-2 capitalize">{{ str_replace('_', ' ', $user->role) }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $isOnline = $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5));
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isOnline ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $isOnline ? 'Online' : 'Offline' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2">
                                        @if($user->last_seen_at)
                                            {{ $user->last_seen_at->format('d M Y, H:i') }}
                                            <span class="text-xs text-gray-400 block">{{ $user->last_seen_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-gray-400">Belum pernah online</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 space-x-3 whitespace-nowrap">
                                        @if(auth()->user()->role === 'admin_website' && $user->role !== 'admin_website')
                                            @if($user->role !== 'admin_grup')
                                                <form id="make-admin-{{ $user->id }}" action="{{ route('admin.users.make-admin', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="button" 
                                                        onclick="if(confirm('Jadikan {{ $user->name }} sebagai Admin Grup?')) { this.form.submit(); }"
                                                        class="text-green-600 hover:text-green-900 text-sm font-medium">
                                                        <i class="fas fa-user-shield mr-1"></i> Jadikan Admin Grup
                                                    </button>
                                                </form>
                                            @else
                                                <form id="remove-admin-{{ $user->id }}" action="{{ route('admin.users.remove-admin', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('POST')
                                                    <button type="button" 
                                                        onclick="if(confirm('Hapus hak akses Admin Grup dari {{ $user->name }}?')) { this.form.submit(); }"
                                                        class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                                        <i class="fas fa-user-minus mr-1"></i> Hapus Admin Grup
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Tidak ada aksi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
