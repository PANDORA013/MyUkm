@extends('layouts.app')

@section('title', 'Dashboard Admin Grup')

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

        <a href="{{ url('/home') }}" class="inline-flex items-center text-sm text-blue-600 hover:underline mb-4">
            &larr; Kembali
        </a>

        <h2 class="text-2xl font-semibold text-gray-800">Anggota UKM Anda</h2>

        <section class="bg-white border border-gray-200 shadow-sm rounded-xl p-6">
            @if($anggota->isEmpty())
                <p class="text-gray-500">Belum ada anggota di UKM ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Nama</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">NIM</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($anggota as $user)
                                <tr>
                                    @php($isAdmin = $user->role === 'admin_grup')
                                    <td class="px-4 py-2">{{ $user->name }}</td>
                                    <td class="px-4 py-2">{{ $isAdmin ? 'Admin' : 'Anggota' }}</td>
                                    <td class="px-4 py-2 font-mono">{{ $isAdmin ? '-' : $user->nim }}</td>
                                    <td class="px-4 py-2 space-x-3 whitespace-nowrap">
                                        @if(!$isAdmin)
                                            <form action="{{ url('/grup/keluarkan/'.$user->id) }}" method="POST" class="inline" onsubmit="return confirm('Keluarkan anggota ini?');">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:underline">Keluarkan</button>
                                            </form>
                                            @php($isMuted = (bool) optional($user->pivot)->is_muted)
                                            <form action="{{ url('/grup/mute/'.$user->id) }}" method="POST" class="inline" onsubmit="return confirm($isMuted ? 'Unmute anggota ini?' : 'Mute anggota ini?');">
                                                @csrf
                                                <button type="submit" class="{{ $isMuted ? 'text-green-600' : 'text-yellow-600' }} hover:underline">
                                                    {{ $isMuted ? 'Unmute' : 'Mute' }}
                                                </button>
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
