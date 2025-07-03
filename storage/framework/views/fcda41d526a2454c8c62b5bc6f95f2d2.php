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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #4338ca;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .sidebar-heading {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .nav-item .nav-link {
            padding: 0.75rem 1rem;
            color: #5a5c69;
        }
        
        .nav-item .nav-link:hover {
            color: #4338ca;
            background-color: #f8f9fc;
        }
        
        .nav-item .nav-link.active {
            color: #4338ca;
            font-weight: 500;
        }
        
        .nav-item .nav-link i {
            width: 1.25rem;
            margin-right: 0.5rem;
            text-align: center;
        }
        
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            border: none;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        
        .btn-primary:hover {
            background-color: #3730a3;
            border-color: #3730a3;
        }
        
        .topbar .dropdown-list .dropdown-header {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        
        @media (min-width: 768px) {
            .sidebar {
                width: 14rem !important;
            }
            .content {
                margin-left: 14rem;
            }
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="sidebar border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom">
                Menu Navigasi
            </div>
            <div class="list-group list-group-flush">
                <a href="<?php echo e(route('ukm.index')); ?>" class="list-group-item list-group-item-action <?php echo e(request()->routeIs('ukm.index') ? 'active' : ''); ?>">
                    <i class="fas fa-university"></i> Daftar UKM
                </a>
                <a href="<?php echo e(route('profile.show')); ?>" class="list-group-item list-group-item-action <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
                <!-- Chat menu akan muncul jika user sudah bergabung dengan UKM -->
                <?php if(Auth::user()->groups->count() > 0): ?>
                    <div class="sidebar-heading border-bottom mt-3">
                        UKM Saya
                    </div>
                    <?php $__currentLoopData = Auth::user()->groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('ukm.chat', $group->referral_code)); ?>" class="list-group-item list-group-item-action <?php echo e(request()->is('ukm/'.$group->referral_code.'/chat') ? 'active' : ''); ?>">
                        <i class="fas fa-comments"></i> <?php echo e($group->name); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper" class="content flex-grow-1">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                <div class="container-fluid">
                    <button class="btn btn-sm" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a class="navbar-brand mx-3" href="<?php echo e(route('ukm.index')); ?>">
                        <i class="fas fa-graduation-cap me-2"></i>MyUkm
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name)); ?>&background=4338ca&color=fff" alt="Profile" class="rounded-circle" width="32">
                                    <span class="ms-2"><?php echo e(Auth::user()->name); ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
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
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Main Content -->
            <div class="container-fluid">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Toggle sidebar
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $("#sidebar-wrapper").toggleClass("d-none d-md-block");
        });
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\MyUkm-main\resources\views/layouts/user.blade.php ENDPATH**/ ?>