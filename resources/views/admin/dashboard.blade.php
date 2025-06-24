@extends('layouts.app')

@section('title', 'Dashboard Admin')

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

        {{-- Admin Navigation --}}
        <div class="flex space-x-4 mb-6">
            <a href="{{ route('admin.search-member') }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Cari Anggota
            </a>
        </div>

        {{-- Statistik --}}
        <section class="bg-white border border-gray-200 shadow-sm rounded-xl p-6 flex items-center justify-between">
            <div class="text-lg font-medium text-gray-700">Total Anggota Seluruh UKM</div>
            <div class="text-3xl font-bold text-blue-600">{{ $totalMembers }}</div>
        </section>

        <section class="bg-white border border-gray-200 shadow-sm rounded-xl p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800">Kelola UKM</h2>

            {{-- Form Tambah UKM --}}
            <form action="{{ url('/admin/ukm/tambah') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama UKM</label>
                        <input type="text" name="nama" required maxlength="255" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode (4 huruf/angka)</label>
                        <input type="text" name="kode" required pattern=".{4,4}" maxlength="4" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium w-full">Tambah</button>
                    </div>
                </div>
            </form>

            {{-- Daftar UKM --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Kode</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Anggota</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ukms as $ukm)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $ukm->nama }}</td>
                                <td class="px-4 py-2 whitespace-nowrap font-mono">{{ $ukm->kode }}</td>
                                <td class="px-4 py-2 text-center">{{ $ukm->members_count }}</td>
                                <td class="px-4 py-2 text-center space-x-3">
                                    <a href="{{ url('/admin/ukm/'.$ukm->id.'/anggota') }}" class="text-blue-600 hover:underline">Anggota</a>
                                    <a href="{{ url('/admin/ukm/edit/'.$ukm->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                                    <form action="{{ url('/admin/ukm/hapus/'.$ukm->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus UKM?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada UKM terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
