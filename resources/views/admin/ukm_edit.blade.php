@extends('layouts.app')

@section('title', 'Edit UKM')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-6">
    <div class="max-w-xl mx-auto">
        {{-- Flash Messages --}}
        @foreach (['success', 'error', 'info'] as $msg)
            @if(session($msg))
                <div class="mb-6 px-4 py-3 rounded-lg shadow-sm border {{ $msg==='success' ? 'bg-green-100 border-green-200 text-green-700' : ($msg==='error' ? 'bg-red-100 border-red-200 text-red-700' : 'bg-blue-100 border-blue-200 text-blue-700') }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Edit UKM</h2>
            <form action="{{ url('/admin/ukm/update/'.$ukm->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama UKM</label>
                    <input type="text" name="nama" value="{{ old('nama', $ukm->nama) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nama')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode UKM</label>
                    <input type="text" name="kode" maxlength="5" value="{{ old('kode', $ukm->kode) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('kode')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="{{ url('/admin/dashboard') }}" class="text-gray-600 hover:underline">Batal</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
