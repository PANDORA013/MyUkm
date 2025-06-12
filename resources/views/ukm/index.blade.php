@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen py-12 px-6">
    <div class="max-w-4xl mx-auto space-y-10">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="bg-blue-100 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg shadow-sm">
                {{ session('info') }}
            </div>
        @endif

        {{-- Referral Form --}}
        <section class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Gabung dengan Kode Referral</h2>
            <form action="{{ route('ukm.join') }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-center justify-center">
                @csrf
                <input name="group_code" type="text" 
                    class="w-full sm:w-2/3 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    placeholder="Masukkan kode referral"
                    maxlength="4" pattern=".{4,4}" required />
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    Gabung
                </button>
            </form>
            @error('group_code')
                <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
            @enderror
        </section>

        {{-- Available UKMs --}}
        <section class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Daftar UKM</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                @foreach ($groupDefaults as $code => $group)
                    <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3 hover:shadow-sm transition">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">{{ $group['name'] }}</h3>
                        </div>
                        
                        <div>
                            @if($joinedGroups->contains('referral_code', $code))
                                <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Sudah Bergabung
                                </span>
                            @else
                                <span class="text-sm italic text-gray-500">Butuh kode referral</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Joined UKMs --}}
        <section class="bg-white shadow-sm rounded-xl p-6 border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">UKM yang Kamu Ikuti</h2>
            @if ($joinedGroups->isEmpty())
                <p class="text-gray-500 text-center">Kamu belum bergabung dengan UKM manapun.</p>
            @else
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach ($joinedGroups as $group)
                        <div class="border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $group->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $group->users->count() }} anggota</p>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <a href="{{ route('ukm.chat', $group->referral_code) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                    Masuk Chat
                                </a>
                                <form action="{{ route('ukm.leave', $group->referral_code) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        onclick="return confirm('Yakin ingin keluar dari UKM ini?')"
                                        class="text-red-500 hover:text-red-600 text-sm font-semibold">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>
@endsection