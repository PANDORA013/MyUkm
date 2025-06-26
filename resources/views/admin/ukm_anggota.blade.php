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
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Aksi</th>
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
                                        <form action="{{ url('/admin/ukm/'.$ukm->id.'/keluarkan/'.$user->id) }}" method="POST" class="inline" onsubmit="return confirm('Keluarkan anggota ini dari UKM?');">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:underline">Keluarkan</button>
                                        </form>
                                        @if($user->role === 'anggota')
                                            <form action="{{ url('/admin/user/jadikan-admin') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="text-blue-600 hover:underline" onclick="return confirm('Jadikan user ini sebagai Admin Grup?');">Jadikan Admin</button>
                                            </form>
                                        @elseif($user->role === 'admin_grup')
                                            <form action="{{ url('/admin/user/hapus-admin') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Hapus status Admin Grup dari user ini?');">Hapus Admin</button>
                                            </form>
                                        @else
                                            <span class="text-gray-500">—</span>
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
