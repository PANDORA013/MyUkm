@extends('layouts.app')

@section('title', 'Detail Anggota - ' . $user->name)


@section('content')
@push('styles')
<style>
    .loading-spinner {
        display: none;
        width: 1.5rem;
        height: 1.5rem;
        border: 0.2rem solid #f3f3f3;
        border-top: 0.2rem solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .btn-loading .loading-spinner {
        display: inline-block;
        margin-right: 0.5rem;
    }
</style>
@endpush
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                Detail Anggota - {{ $user->name }}
            </h1>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- User Info --}}
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Akun</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Nama Lengkap</p>
                            <p class="font-medium">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">NIM</p>
                            <p class="font-medium">{{ $user->nim ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Password (Hash)</p>
                            <div class="truncate max-w-xs" title="{{ $user->password }}">
                                <code class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($user->password, 30) }}</code>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Role</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $user->role ?? 'Anggota' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Terakhir Akses</p>
                            <p class="font-medium">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah login' }}
                                @if($user->last_login_at)
                                    <span class="text-gray-500 text-sm block">Terakhir aktif {{ $user->last_login_at->format('d M Y, H:i') }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Registrasi</p>
                            <p class="font-medium">
                                {{ $user->created_at->format('d M Y') }}
                                <span class="text-gray-500 text-sm block">{{ $user->created_at->diffForHumans() }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- UKM Membership --}}
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Keanggotaan UKM</h2>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 mr-4">
                            {{ $user->ukm->count() }} UKM
                        </span>
                        @if(auth()->user()->role === 'admin_website')
                            <button 
                                class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600"
                                data-bs-toggle="tooltip" 
                                title="Detail Keanggotaan"
                            >
                                <i class="fas fa-info-circle"></i>
                            </button>
                        @endif
                    </div>
                </div>

                @if($user->ukm->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UKM</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                                    @if(auth()->user()->role === 'admin_website')
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($user->ukm as $ukm)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $ukm->name }}</div>
                                            @if($ukm->category)
                                                <div class="text-sm text-gray-500">{{ $ukm->category }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ukm->pivot->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Menunggu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($ukm->pivot->role === 'Admin')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $ukm->pivot->role }}
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    {{ $ukm->pivot->role ?? 'Anggota' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($ukm->pivot->created_at)
                                                {{ $ukm->pivot->created_at->format('d M Y') }}
                                                <div class="text-xs text-gray-400">{{ $ukm->pivot->created_at->diffForHumans() }}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if(auth()->user()->role === 'admin_website')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button type="button" 
                                                    class="text-blue-600 hover:text-blue-900 mr-3 edit-role-btn" 
                                                    title="Edit Role"
                                                    data-user-id="{{ $user->id }}"
                                                    data-ukm-id="{{ $ukm->id }}"
                                                    data-current-role="{{ $ukm->pivot->role }}">
                                                    <i class="fas fa-user-edit"></i>
                                                </button>
                                                <form action="{{ url('/admin/ukm/' . $ukm->id . '/members/' . $user->id) }}" 
                                                    method="POST" 
                                                    class="inline"
                                                    onsubmit="return confirm('Anda yakin ingin menghapus anggota ini dari UKM?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-users-slash text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-500">Belum terdaftar di UKM manapun</p>
                    </div>
                @endif
            </div>
            </div>
</div>

<!-- Modal Edit Role -->
<div id="editRoleModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-user-edit text-blue-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Ubah Peran Anggota
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Pilih peran untuk <span id="memberName" class="font-medium"></span> di UKM <span id="ukmName" class="font-medium"></span>
                                </p>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                                    <select name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="Anggota">Anggota</option>
                                        <option value="Admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm btn-save-role">
                        <span class="loading-spinner"></span>
                        <span class="btn-text">Simpan Perubahan</span>
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modal functions
    function openModal() {
        document.getElementById('editRoleModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        document.getElementById('editRoleModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Handle edit role button click
    document.querySelectorAll('.edit-role-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const ukmId = this.getAttribute('data-ukm-id');
            const currentRole = this.getAttribute('data-current-role');
            const ukmName = this.closest('tr').querySelector('.text-gray-900').textContent.trim();
            const memberName = '{{ $user->name }}';
            
            // Set form action
            const form = document.getElementById('editRoleForm');
            form.action = `/admin/ukm/${ukmId}/members/${userId}/role`;
            
            // Set current role
            form.querySelector('select[name="role"]').value = currentRole;
            
            // Set names
            document.getElementById('memberName').textContent = memberName;
            document.getElementById('ukmName').textContent = ukmName;
            
            // Show modal
            openModal();
        });
    });

    // Handle form submission
    document.getElementById('editRoleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitButton = form.querySelector('.btn-save-role');
        const buttonText = submitButton.querySelector('.btn-text');
        const spinner = submitButton.querySelector('.loading-spinner');
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.classList.add('btn-loading');
        buttonText.textContent = 'Menyimpan...';
        
        // Submit form
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                _method: 'PUT',
                role: form.querySelector('select[name="role"]').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                alert('Peran berhasil diperbarui');
                // Reload page to see changes
                window.location.reload();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memperbarui peran: ' + error.message);
            // Reset button state
            submitButton.disabled = false;
            submitButton.classList.remove('btn-loading');
            buttonText.textContent = 'Simpan Perubahan';
        });
    });

    // Close modal when clicking outside
    document.getElementById('editRoleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush

<div class="mt-8 flex justify-end">
    <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Halaman Sebelumnya
    </a>
</div>

@endsection
