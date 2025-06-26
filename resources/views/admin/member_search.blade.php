@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Hasil Pencarian: "{{ $query }}"</h1>
    
    <form action="{{ route('admin.search-member') }}" method="GET" class="mb-6">
        <div class="flex">
            <input type="text" name="q" value="{{ $query }}" 
                   class="flex-grow px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Cari berdasarkan nama atau NIM">
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                Cari
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-left text-gray-600 text-sm uppercase font-semibold">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">NIM</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Terakhir Akses</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 even:bg-gray-50/50 transition-colors duration-150">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $user->nim }}</td>
                        <td class="px-4 py-3 whitespace-nowrap capitalize">{{ $user->role }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('admin.member-ukms', $user->id) }}"
                               class="inline-block px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded hover:bg-blue-600 transition-colors duration-150">
                                Informasi Lengkap
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                            Tidak ada hasil ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links('pagination::tailwind') }}
    </div>
</div>
@endsection
