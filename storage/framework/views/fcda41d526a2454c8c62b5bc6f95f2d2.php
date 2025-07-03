<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?> - MyUkm</title>
    
    <!-- Favicons -->
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    
    <style>
        :root {
            --primary-color: #4338ca;
            --primary-hover: #3730a3;
            --sidebar-width: 240px;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.3s;
        }
        
        .sidebar-heading {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin 0.3s;
            min-height: 100vh;
            padding: 1rem;
        }
        
        .list-group-item {
            border: none;
            padding: 0.6rem 1rem;
            transition: all 0.2s;
        }
        
        .list-group-item.active {
            background-color: #f0f2ff;
            color: var(--primary-color);
            font-weight: 500;
            border-left: 3px solid var(--primary-color);
        }
        
        .list-group-item:hover {
            background-color: #f8f9fc;
            color: var(--primary-color);
        }
        
        .list-group-item i {
            width: 1.25rem;
            margin-right: 0.5rem;
            text-align: center;
        }
        
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .top-navbar {
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 0.5rem 1rem;
            margin-left: var(--sidebar-width);
            position: sticky;
            top: 0;
            z-index: 99;
            transition: margin 0.3s;
        }
        
        .avatar-container {
            display: flex;
            align-items: center;
        }
        
        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 8px;
            object-fit: cover;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content,
            .top-navbar {
                margin-left: 0;
            }
            
            body.sidebar-open .sidebar {
                margin-left: 0;
            }
            
            body.sidebar-open::after {
                content: '';
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 99;
                display: block;
            }
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="d-flex align-items-center justify-content-center p-3 mb-2">
            <h4 class="mb-0 text-primary">
                <i class="fas fa-graduation-cap me-2"></i>MyUkm
            </h4>
        </div>
        
        <div class="sidebar-heading border-bottom">
            Menu Navigasi
        </div>
        <div class="list-group list-group-flush">
            <a href="<?php echo e(route('ukm.index')); ?>" class="list-group-item list-group-item-action <?php echo e(request()->routeIs('ukm.index') ? 'active' : ''); ?>">
                <i class="fas fa-university"></i> Daftar UKM
            </a>
            <a href="<?php echo e(route('profile.show')); ?>" class="list-group-item list-group-item-action <?php echo e(request()->routeIs('profile.show') ? 'active' : ''); ?>">
                <i class="fas fa-user-circle"></i> Profil Saya
            </a>
        </div>
        
        <!-- Chat menu akan muncul jika user sudah bergabung dengan UKM -->
        <?php if(Auth::user()->groups->count() > 0): ?>
            <div class="sidebar-heading border-bottom mt-2 mb-0">
                UKM Saya
            </div>
            <?php $__currentLoopData = Auth::user()->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="list-group-item list-group-item-action <?php echo e(request()->is('ukm/'.$group->referral_code.'/chat') ? 'active' : ''); ?>">
                <i class="fas fa-comments"></i> <?php echo e($group->name); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
    
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-link text-dark p-0 d-md-none" id="sidebar-toggle">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar">
                            <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

                        </div>
                        <span class="d-none d-sm-inline"><?php echo e(Auth::user()->name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
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
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Toggle sidebar for mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            
            if (window.innerWidth < 768 && 
                sidebar.classList.contains('show') && 
                !sidebar.contains(event.target) && 
                event.target !== toggleBtn) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        });
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/layouts/user.blade.php ENDPATH**/ ?>