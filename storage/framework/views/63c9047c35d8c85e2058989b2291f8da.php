<?php $__env->startSection('title', 'Detail Anggota - ' . $user->name); ?>


<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
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
        
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                Detail Anggota
            </h1>
            <a href="<?php echo e(url()->previous()); ?>" class="text-blue-500 hover:text-blue-700 flex items-center">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        
        <div class="p-6">
            
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Informasi Akun</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <h3 class="text-base font-medium text-black mb-2">Nama</h3>
                            <div class="flex items-center">
                                <?php if($user->photo): ?>
                                    <img src="<?php echo e($user->photo_url); ?>" alt="<?php echo e($user->name); ?>" class="w-12 h-12 rounded-full object-cover mr-4">
                                <?php else: ?>
                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-4">
                                        <i class="fas fa-user text-xl"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="text-sm text-gray-600"><?php echo e($user->name); ?></span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="mb-4">
                                <h3 class="text-base font-medium text-black mb-2">Nomor Induk Mahasiswa</h3>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-600"><?php echo e($user->nim ?? 'Tidak tersedia'); ?></span>
                                </div>
                            </div>
                            <?php if($user->email): ?>
                            <div class="mb-4">
                                <h3 class="text-base font-medium text-black mb-2">Email</h3>
                                <div class="flex items-center">
                                    <div class="w-12 h-12 flex items-center justify-center mr-4">
                                        <i class="fas fa-envelope text-gray-400 text-xl"></i>
                                    </div>
                                    <span class="text-sm text-gray-600"><?php echo e($user->email); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            

                        </div>
                    </div>
                    <div>
                        <h3 class="text-base font-medium text-black mb-2">Status</h3>
                        <div class="flex items-center mb-4">
                            <?php
                                $isOnline = $user->last_seen_at && $user->last_seen_at->gt(now()->subMinutes(5));
                            ?>
                            <span class="h-2.5 w-2.5 rounded-full mr-2 <?php echo e($isOnline ? 'bg-green-500' : 'bg-gray-400'); ?>"></span>
                            <span class="text-sm text-gray-600">
                                <?php if($isOnline): ?>
                                    Sedang Online
                                <?php else: ?>
                                    Terakhir aktif <?php echo e($user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'tidak diketahui'); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        <h3 class="text-base font-medium text-black mb-2">Bergabung</h3>
                        <p class="text-sm text-gray-600"><?php echo e($user->created_at->translatedFormat('d F Y')); ?></p>
                    </div>
                </div>
            </div>

            
            <?php if($ukms->count() > 0): ?>
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Keanggotaan UKM</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Total: <?php echo e($ukms->count()); ?> UKM
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama UKM</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $ukms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ukm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($ukm->nama); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo e($ukm->kode); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($ukm->pivot->created_at->translatedFormat('d F Y')); ?>

                                        <div class="text-xs text-gray-400">
                                            <?php echo e($ukm->pivot->created_at->diffForHumans()); ?>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($user->role === 'admin_grup'): ?>
                                            <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-user-shield mr-1"></i> Admin Grup
                                            </span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-user mr-1"></i> Anggota
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="mt-8 border-t border-red-200 pt-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Hapus Akun</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Menghapus akun ini akan menghapus semua data yang terkait dengan pengguna ini. Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                            <div class="mt-4">
                                <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan!')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-trash-alt mr-2"></i> Hapus Akun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
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

<?php $__env->startPush('scripts'); ?>
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
            const memberName = '<?php echo e($user->name); ?>';
            
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
<?php $__env->stopPush(); ?>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views\admin\member_ukms.blade.php ENDPATH**/ ?>