<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - MyUkm</title>
    
    <!-- Favicons -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/js/app.js'])
    
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
        
        /* Admin badge styling */
        .admin-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
            display: inline-flex;
            align-items: center;
        }
        
        .admin-menu-item {
            background-color: #fef3c7 !important;
            border-left: 3px solid #f59e0b !important;
            color: #92400e !important;
            font-weight: 500 !important;
        }
        
        .admin-menu-item:hover {
            background-color: #fde68a !important;
            color: #78350f !important;
        }
        
        .admin-menu-item.active {
            background-color: #f59e0b !important;
            color: white !important;
        }
        
        .admin-section-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
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
    
    @stack('styles')
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
            <a href="{{ route('ukm.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('ukm.index') ? 'active' : '' }}">
                <i class="fas fa-university"></i> Daftar UKM
            </a>
            <a href="{{ route('profile.show') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i> Profil Saya
            </a>
        </div>
        
        <!-- Admin Groups Management Section -->
        @if(Auth::user()->role === 'admin_grup')
            @php
                $managedGroups = Auth::user()->adminGroups;
            @endphp
            @if($managedGroups->count() > 0)
                <div class="admin-section-header">
                    <i class="fas fa-crown me-1"></i> Admin UKM
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('grup.dashboard') }}" class="list-group-item list-group-item-action admin-menu-item {{ request()->routeIs('grup.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                    </a>
                    <a href="{{ route('grup.anggota') }}" class="list-group-item list-group-item-action admin-menu-item {{ request()->routeIs('grup.anggota') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i> Kelola Anggota
                    </a>
                    @foreach($managedGroups as $group)
                        <a href="{{ route('ukm.chat', $group->referral_code) }}" class="list-group-item list-group-item-action admin-menu-item {{ request()->is('ukm/'.$group->referral_code.'/chat') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Chat {{ $group->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
        
        <!-- Regular User Groups Section -->
        @php
            $regularGroups = Auth::user()->groups;
            if(Auth::user()->role === 'admin_grup') {
                $managedGroupIds = Auth::user()->adminGroups->pluck('id')->toArray();
                $regularGroups = $regularGroups->filter(function($group) use ($managedGroupIds) {
                    return !in_array($group->id, $managedGroupIds);
                });
            }
        @endphp
        @if($regularGroups->count() > 0)
            <div class="sidebar-heading border-bottom mt-2 mb-0">
                UKM Saya
            </div>
            @foreach($regularGroups as $group)
            <a href="{{ route('ukm.chat', $group->referral_code) }}" class="list-group-item list-group-item-action {{ request()->is('ukm/'.$group->referral_code.'/chat') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> {{ $group->name }}
            </a>
            @endforeach
        @endif
    </div>
    
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-link text-dark p-0 d-md-none" id="sidebar-toggle" aria-label="Toggle sidebar" title="Buka/tutup menu samping">
                    <i class="fas fa-bars fa-lg" aria-hidden="true"></i>
                    <span class="visually-hidden">Menu</span>
                </button>
                
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="d-none d-sm-inline">
                            {{ Auth::user()->name }}
                            @if(Auth::user()->role === 'admin_grup')
                                <span class="admin-badge">
                                    <i class="fas fa-crown me-1"></i>Admin UKM
                                </span>
                            @endif
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
                        @if(Auth::user()->role === 'admin_grup')
                            @php
                                $managedGroups = Auth::user()->adminGroups;
                            @endphp
                            @if($managedGroups->count() > 0)
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header"><i class="fas fa-crown me-2" style="color: #f59e0b;"></i>Menu Admin UKM</li>
                                <li><a class="dropdown-item" href="{{ route('grup.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</a></li>
                                <li><a class="dropdown-item" href="{{ route('grup.anggota') }}"><i class="fas fa-users-cog me-2"></i>Kelola Anggota</a></li>
                                @foreach($managedGroups as $group)
                                    <li><a class="dropdown-item" href="{{ route('ukm.chat', $group->referral_code) }}"><i class="fas fa-comments me-2"></i>Chat {{ $group->name }}</a></li>
                                @endforeach
                            @endif
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
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
        @yield('content')
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Wait for DOM to be ready
        $(document).ready(function() {
            // CSRF token is already handled in app.js, just log confirmation
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            if (csrfToken) {
                console.log('Layout: CSRF token confirmed available');
            }
        });
        
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
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                alert('Kode berhasil disalin!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
