@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-6 px-4">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-blue-700">MyUKM</h1>
        <div class="flex items-center gap-2">
            <span class="text-gray-700">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-red-500 hover:underline">Logout</button>
            </form>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            {{ session('info') }}
        </div>
    @endif

    {{-- Judul --}}
    <div class="bg-gray-100 p-4 rounded mb-6">
        <h2 class="text-xl font-semibold text-center">UKM Management</h2>
    </div>

    {{-- UKM Tersedia --}}
    <div class="mb-6">
        <h3 class="font-semibold mb-3">Gabung UKM</h3>
        <p class="mb-2">UKM yang Tersedia:</p>
        
        @foreach ($groupDefaults as $code => $group)
            <div class="bg-white shadow-sm p-4 rounded-lg mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-bold">{{ $group['name'] }}</h4>
                        <p class="text-sm text-gray-600">Kode: {{ $code }}</p>
                    </div>
                    @if($joinedGroups->contains('referral_code', $code))
                        <span class="text-green-600 font-semibold">Sudah Bergabung</span>
                    @else
                        <form action="{{ route('ukm.join') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group_code" value="{{ $code }}">
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded">
                                Gabung
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gabung dengan kode --}}
    <div class="mb-6">
        <h3 class="font-semibold mb-3">Gabung dengan Kode Referral:</h3>
        <form action="{{ route('ukm.join') }}" method="POST" class="flex gap-2">
            @csrf
            <input name="group_code" type="text" placeholder="Masukkan kode referral"
                class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                maxlength="4" pattern=".{4,4}" required />
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Gabung
            </button>
        </form>
        @error('group_code')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- UKM Diikuti --}}
    <div class="mb-6">
        <h3 class="font-semibold mb-3">UKM yang Diikuti</h3>
        @if ($joinedGroups->isEmpty())
            <p class="text-gray-500">Belum mengikuti UKM manapun.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($joinedGroups as $group)
                    <div class="bg-white shadow-sm p-4 rounded-lg">
                        <h4 class="text-lg font-bold mb-2">{{ $group->name }}</h4>
                        <p class="text-sm text-gray-600 mb-2">Kode: {{ $group->referral_code }}</p>
                        <p class="text-sm text-gray-600 mb-4">{{ $group->users->count() }} Anggota</p>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('ukm.chat', $group->referral_code) }}" 
                               class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Masuk Chat
                            </a>
                            <form action="{{ route('ukm.leave', $group->referral_code) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin keluar dari UKM ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Keluar</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection