<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <!-- Local Fonts -->
    <link href="{{ asset('assets/css/fonts/inter/inter-fonts.css') }}" rel="stylesheet" />
    
    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/fontawesome/all.min.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: #667eea !important;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar p-3">
                <div class="text-center mb-4">
                    <h4 class="text-white fw-bold">{{ config('app.name', 'STA') }}</h4>
                    <p class="text-white-50 small">Admin Dashboard</p>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center text-white mb-2">
                        <i class="fas fa-user-circle fa-2x me-2"></i>
                        <div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <small class="text-white-50">{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-home me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- User Management Section -->
                    @can('view users')
                    <li class="nav-item">
                        <div class="nav-link text-white-50 small fw-bold text-uppercase mt-3 mb-1">
                            <i class="fas fa-users me-2"></i>User Management
                        </div>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="fas fa-list me-2"></i>
                            All Users
                        </a>
                    </li>
                    @can('create users')
                    <li class="nav-item ms-3">
                        <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" href="{{ route('users.create') }}">
                            <i class="fas fa-user-plus me-2"></i>
                            Add User
                        </a>
                    </li>
                    @endcan
                    @endcan
                    
                    <!-- Role Management Section -->
                    @can('view roles')
                    <li class="nav-item">
                        <div class="nav-link text-white-50 small fw-bold text-uppercase mt-3 mb-1">
                            <i class="fas fa-user-shield me-2"></i>Role Management
                        </div>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                            <i class="fas fa-list me-2"></i>
                            All Roles
                        </a>
                    </li>
                    @can('create roles')
                    <li class="nav-item ms-3">
                        <a class="nav-link {{ request()->routeIs('roles.create') ? 'active' : '' }}" href="{{ route('roles.create') }}">
                            <i class="fas fa-plus-circle me-2"></i>
                            Add Role
                        </a>
                    </li>
                    @endcan
                    @endcan

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-edit me-2"></i>
                            Profile
                        </a>
                    </li>
                    
                    <li class="nav-item mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-lg me-2"></i>
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid px-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>