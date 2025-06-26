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
    
    .btn-loading .btn-text {
        display: inline-block;
    }
</style>
</head>
<body>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                Detail Anggota
            </h1>
            <a href="{{ url()->previous() }}" class="text-blue-500 hover:text-blue-700 flex items-center">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- User Info --}}
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Akun</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <h3 class="text-base font-medium text-black mb-2">Nama</h3>
                            <div class="flex items-center">
                                @if($user->photo)
                                    <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-4">
                                        <i class="fas fa-user text-xl"></i>
                                    </div>
                                @endif
                                <span class="text-sm text-gray-600">{{ $user->name }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="mb-4">
                                <h3 class="text-base font-medium text-black mb-2">Nomor Induk Mahasiswa</h3>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 flex items-center justify-center mr-4">
                                        <i class="fas fa-id-card text-gray-400 text-xl"></i>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $user->nim ?? 'Tidak tersedia' }}</span>
                                </div>
                            </div>
                            @if($user->email)
                            <div class="mb-4">
                                <h3 class="text-base font-medium text-black mb-2">Email</h3>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 flex items-center justify-center mr-4">
                                        <i class="fas fa-envelope text-gray-400 text-xl"></i>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $user->email }}</span>
                                </div>
                            </div>
                            @endif
                            
                            @if(isset($user->is_admin) && $user->is_admin)
                            <div class="mb-4">
                                <h3 class="text-base font-medium text-black mb-2">Password</h3>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 flex items-center justify-center mr-4">
                                        <i class="fas fa-lock text-gray-400 text-xl"></i>
                                    </div>
                                    <div class="relative flex-1">
                                        <div class="flex items-center">
                                            <div class="relative flex-1">
                                                <input type="password" 
                                                       value="{{ $user->password_visible ?? 'Tidak dapat menampilkan password' }}" 
                                                       id="password-field" 
                                                       class="text-sm text-gray-600 bg-transparent border-0 p-0 w-full" 
                                                       readonly>
                                            </div>
                                            <button type="button" 
                                                    class="ml-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
                                                    onclick="togglePassword('password-field')">
                                                Tampilkan
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Hanya terlihat oleh Admin Website</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-base font-medium text-black mb-2">Status</h3>
                        <div class="flex items-center mb-4">
                            @php
                                $isOnline = $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5));
                            @endphp
                            <span class="h-2.5 w-2.5 rounded-full mr-2 {{ $isOnline ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            <span class="text-sm text-gray-600">
                                @if($isOnline)
                                    Sedang Online
                                @else
                                    Terakhir aktif {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'tidak diketahui' }}
                                @endif
                            </span>
                        </div>
                        <h3 class="text-base font-medium text-black mb-2">Bergabung</h3>
                        <p class="text-sm text-gray-600">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- UKM Memberships --}}
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Keanggotaan UKM</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Total: {{ $ukms->count() }} UKM
                    </span>
                </div>

                @if($ukms->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UKM</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Password</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($ukms as $ukm)
                                    @php
                                        $isOnline = $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5));
                                        $password = $ukm->pivot->password ?? 'Belum diatur';
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $ukm->nama }}</div>
                                            <div class="text-xs text-gray-500">{{ $ukm->kode }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isOnline ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $isOnline ? 'Online' : 'Offline' }}
                                                @if($isOnline)
                                                    <span class="ml-1 w-2 h-2 bg-green-500 rounded-full"></span>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ukm->pivot->role === 'Admin' ? 'bg-blue-100 text-blue-800' : 'bg-indigo-100 text-indigo-800' }}">
                                                {{ $ukm->pivot->role }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <span class="password-display">••••••••</span>
                                                <button onclick="togglePassword(this, '{{ $password }}')" class="ml-2 text-blue-600 hover:text-blue-800 text-xs">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <input type="hidden" value="{{ $password }}" class="password-value">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $ukm->pivot->created_at->translatedFormat('d F Y') }}
                                            <div class="text-xs text-gray-400">
                                                {{ $ukm->pivot->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('admin.ukm.members.remove', ['ukm' => $ukm->id, 'user' => $user->id]) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus anggota ini dari UKM?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
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
</div>

<!-- Modal Edit Role -->
<div id="editRoleModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="editRoleForm" method="POST" action="">
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
    function resetPassword(userId) {
        if (confirm('Apakah Anda yakin ingin mereset password pengguna ini? Password baru akan dikirim ke email pengguna.')) {
            // Kirim permintaan reset password ke server
            fetch(`/admin/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password berhasil direset. Password baru telah dikirim ke email pengguna.');
                } else {
                    alert('Gagal mereset password: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mereset password');
            });
        }
    }

    function openModal() {
        document.getElementById('editRoleModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        document.getElementById('editRoleModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.nextElementSibling;
        
        if (field.type === 'password') {
            field.type = 'text';
            button.textContent = 'Sembunyikan';
        } else {
            field.type = 'password';
            button.textContent = 'Tampilkan';
        }
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
        
        // Get the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Create form data
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('role', form.querySelector('select[name="role"]').value);
        
        // Submit form
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
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

    // Toggle password visibility
    function togglePassword(button, password) {
        const passwordDisplay = button.previousElementSibling;
        const icon = button.querySelector('i');
        
        if (passwordDisplay.textContent === '••••••••') {
            passwordDisplay.textContent = password;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            
            // Auto hide after 3 seconds
            setTimeout(() => {
                if (passwordDisplay.textContent !== '••••••••') {
                    passwordDisplay.textContent = '••••••••';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }, 3000);
        } else {
            passwordDisplay.textContent = '••••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Add copy to clipboard functionality
    document.querySelectorAll('.copy-password').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const password = this.getAttribute('data-password');
            if (password && password !== 'Belum diatur') {
                navigator.clipboard.writeText(password).then(() => {
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Disalin!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            }
        });
    });
</script>

<style>
    /* Add smooth transitions for password display */
    .password-display {
        transition: all 0.3s ease;
    }
    
    /* Loading spinner for buttons */
    .loading-spinner {
        display: none;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 0.5rem;
    }
    
    .btn-loading .loading-spinner {
        display: inline-block;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Status indicator */
    .status-indicator {
        display: inline-block;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        margin-right: 0.25rem;
    }
    
    .status-online { background-color: #10B981; }
    .status-offline { background-color: #9CA3AF; }
</style>
@endpush


@endsection
