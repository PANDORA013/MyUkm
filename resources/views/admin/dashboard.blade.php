@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="bg-gray-50 min-h-screen py-10 px-6">
    <div class="max-w-5xl mx-auto space-y-8">
        {{-- Flash Messages --}}
        @foreach (['success', 'error', 'info'] as $msg)
            @if(session($msg))
                <x-alert :type="$msg">
                    {{ session($msg) }}
                </x-alert>
            @endif
        @endforeach

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-stat-card 
                title="Total Anggota"
                :value="number_format($totalMembers)"
                icon="fa-users"
                description="Seluruh UKM"
                color="blue"
                :action="[
                    'url' => route('admin.member.search'),
                    'icon' => 'fa-search',
                    'label' => 'Cari Anggota'
                ]"
            />
            
            <x-stat-card 
                title="Total UKM"
                :value="number_format($totalUkms)"
                icon="fa-building"
                description="Terdaftar"
                color="green"
            />
            
            <x-stat-card 
                title="Riwayat Penghapusan"
                :value="$totalDeletedAccounts"
                icon="fa-history"
                description="Akun Dihapus"
                color="red"
                :action="[
                    'url' => route('admin.user-deletions.index'),
                    'label' => 'Lihat Riwayat'
                ]"
            />

            <x-stat-card 
                title="Admin Grup"
                :value="$totalAdmins"
                icon="fa-user-shield"
                description="Aktif"
                color="purple"
            />

            <x-stat-card 
                title="Pengguna Aktif Bulan Ini"
                :value="number_format($activeUsersThisMonth)"
                icon="fa-user-clock"
                description="Pengguna"
                color="amber"
            />
        </div>

        <!-- Baris Kedua Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            <x-stat-card 
                title="Pengguna Baru"
                :value="'+' . number_format($newUsersThisMonth)"
                icon="fa-user-plus"
                description="Bulan Ini"
                color="teal"
            />

            <x-stat-card 
                title="Rata-rata Keanggotaan"
                :value="$totalUkms > 0 ? number_format($totalMembers / $totalUkms, 1) : 0"
                icon="fa-chart-bar"
                description="Anggota per UKM"
                color="indigo"
            />

            <x-stat-card 
                title="Pertumbuhan"
                :value="($newUsersThisMonth > 0 ? round(($newUsersThisMonth / $totalMembers) * 100, 1) : 0) . '%'"
                icon="fa-chart-line"
                description="Pertumbuhan Pengguna"
                color="pink"
            />
        </div>

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
