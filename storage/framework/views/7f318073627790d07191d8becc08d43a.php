<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin - MyUkm'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <!-- Additional Admin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #2c3e50;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #34495e;
            color: #3498db;
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        .navbar-brand {
            font-weight: 600;
            color: #fff !important;
        }
        .content {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="font-[Inter]">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('admin.dashboard')); ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                Admin Panel
            </a>
            
            <!-- Navbar -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo e(Auth::user()->name); ?>

                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>"><i class="fas fa-user me-2"></i>Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('admin.dashboard')); ?>">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.members') ? 'active' : ''); ?>" href="<?php echo e(route('admin.members')); ?>">
                                <i class="fas fa-fw fa-users"></i>
                                Anggota
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.ukms') ? 'active' : ''); ?>" href="<?php echo e(route('admin.ukms')); ?>">
                                <i class="fas fa-fw fa-university"></i>
                                UKM
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('admin.riwayat-penghapusan') ? 'active' : ''); ?>" href="<?php echo e(route('admin.riwayat-penghapusan')); ?>">
                                <i class="fas fa-fw fa-trash-alt"></i>
                                Riwayat Penghapusan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="content">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($errors) && $errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Enable popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Global functions for admin actions
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }

        function showAlert(type, message) {
            // Remove existing alerts to prevent accumulation
            $('.alert').remove();
            
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('.content').prepend(alertHtml);
            
            // Auto-dismiss alert after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }

        // AJAX helper function with better error handling
        function makeAjaxRequest(url, method, data, successCallback, errorCallback) {
            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        if (successCallback) successCallback(response);
                    } else {
                        showAlert('danger', response.error || 'Terjadi kesalahan');
                        if (errorCallback) errorCallback(response);
                    }
                },
                error: function(xhr) {
                    // Only show error alerts for non-404 errors to prevent spam
                    if (xhr.status !== 404) {
                        const errorMessage = xhr.responseJSON?.error || 'Terjadi kesalahan pada server';
                        showAlert('danger', errorMessage);
                    }
                    if (errorCallback) errorCallback(xhr.responseJSON);
                }
            });
        }

        // User management functions
        function makeAdminGrup(userId, userName) {
            confirmAction(
                `Apakah Anda yakin ingin menjadikan ${userName} sebagai admin grup?`,
                function() {
                    makeAjaxRequest(
                        `/admin/users/${userId}/make-admin`,
                        'POST',
                        {},
                        function() { location.reload(); }
                    );
                }
            );
        }

        function removeAdminGrup(userId, userName) {
            confirmAction(
                `Apakah Anda yakin ingin menghapus status admin grup dari ${userName}?`,
                function() {
                    makeAjaxRequest(
                        `/admin/users/${userId}/remove-admin`,
                        'POST',
                        {},
                        function() { location.reload(); }
                    );
                }
            );
        }

        function deleteUser(userId, userName) {
            confirmAction(
                `PERINGATAN: Apakah Anda yakin ingin menghapus akun ${userName} secara permanen?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait user ini.`,
                function() {
                    makeAjaxRequest(
                        `/admin/users/${userId}`,
                        'DELETE',
                        {},
                        function() { location.reload(); }
                    );
                }
            );
        }

        // UKM management functions
        function deleteUKM(ukmId, ukmName) {
            confirmAction(
                `PERINGATAN: Apakah Anda yakin ingin menghapus UKM "${ukmName}" beserta semua anggotanya?\n\nTindakan ini tidak dapat dibatalkan.`,
                function() {
                    makeAjaxRequest(
                        `/admin/ukm/${ukmId}`,
                        'DELETE',
                        {},
                        function() { location.reload(); }
                    );
                }
            );
        }

        function removeFromUKM(ukmId, userId, userName) {
            confirmAction(
                `Apakah Anda yakin ingin mengeluarkan ${userName} dari UKM ini?`,
                function() {
                    makeAjaxRequest(
                        `/admin/ukm/${ukmId}/keluarkan/${userId}`,
                        'POST',
                        {},
                        function() { location.reload(); }
                    );
                }
            );
        }

        function promoteToAdmin(userId, ukmId, userName) {
            confirmAction(
                `Apakah Anda yakin ingin menjadikan ${userName} sebagai admin di grup ini?`,
                function() {
                    makeAjaxRequest(
                        `/admin/users/${userId}/promote-in-group`,
                        'POST',
                        { ukm_id: ukmId },
                        function() { location.reload(); }
                    );
                }
            );
        }

        function demoteFromAdmin(userId, ukmId, userName) {
            confirmAction(
                `Apakah Anda yakin ingin menurunkan ${userName} dari admin di grup ini?`,
                function() {
                    makeAjaxRequest(
                        `/admin/users/${userId}/demote-from-group`,
                        'POST',
                        { ukm_id: ukmId },
                        function() { location.reload(); }
                    );
                }
            );
        }

        // Auto-refresh functionality disabled to prevent error spam
        // TODO: Implement proper statistics endpoint if needed
        /*
        function refreshStats() {
            if (window.location.pathname === '/admin/dashboard') {
                makeAjaxRequest(
                    '/admin/statistics',
                    'GET',
                    {},
                    function(response) {
                        // Update stat cards if they exist
                        if (response.total_members !== undefined) {
                            $('.stat-total-members').text(response.total_members.toLocaleString());
                        }
                        // Add more stat updates as needed
                    },
                    function(error) {
                        // Silently handle errors for auto-refresh
                        console.log('Stats refresh failed:', error);
                    }
                );
            }
        }

        // Refresh stats every 30 seconds on dashboard
        if (window.location.pathname === '/admin/dashboard') {
            setInterval(refreshStats, 30000);
        }
        */
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\NganTeen\MyUkm\resources\views/layouts/admin.blade.php ENDPATH**/ ?>