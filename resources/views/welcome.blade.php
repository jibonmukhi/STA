<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STA - Professional Admin Dashboard</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        html, body {
            overflow-x: hidden !important;
            scroll-behavior: smooth;
            width: 100%;
            max-width: 100%;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Prevent horizontal overflow */
        .hero-content, .max-w-7xl, .max-w-6xl, .max-w-4xl {
            max-width: 100%;
            width: 100%;
        }
        
        /* Hide scrollbar completely */
        ::-webkit-scrollbar {
            display: none;
        }
        
        html {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }
        
        body {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
            margin: 0;
            padding: 0;
            position: relative;
        }
        
        body::-webkit-scrollbar {
            display: none;
        }
        
        .hero-background {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .hero-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .sta-logo-hero {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto 2rem;
            transition: transform 0.3s ease;
        }
        
        .sta-logo-hero:hover {
            transform: translateY(-5px) scale(1.05);
        }
        
        .sta-logo-hero-text {
            font-size: 3rem;
            font-weight: 900;
            color: white;
            letter-spacing: 3px;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            color: white;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }
        
        .cta-button {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
            border: none;
            cursor: pointer;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }
        
        .floating-shapes::before,
        .floating-shapes::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            animation: float 8s ease-in-out infinite;
        }
        
        .floating-shapes::before {
            width: 300px;
            height: 300px;
            top: -50px;
            right: -50px;
            animation-delay: 0s;
        }
        
        .floating-shapes::after {
            width: 200px;
            height: 200px;
            bottom: -25px;
            left: -25px;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .stat-item {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
    </style>
</head>
<body class="hero-background">
    <div class="floating-shapes"></div>
    
    <!-- Navigation -->
    <nav class="hero-content fixed top-0 left-0 right-0 z-50 p-6 bg-transparent backdrop-blur-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <div style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 0.5rem;">
                    <span style="color: white; font-weight: 800; font-size: 1rem;">STA</span>
                </div>
                <!-- <span style="color: white; font-weight: 600; font-size: 1.2rem;">STA</span> -->
            </div>
            
            @if (Route::has('login'))
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="nav-link">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>
    
    <!-- Hero Section -->
    <div class="hero-content flex items-center justify-center px-6" style="min-height: 80vh; padding-top: 100px; padding-bottom: 50px;">
        <div class="max-w-4xl mx-auto text-center">
            <div class="sta-logo-hero">
                <span class="sta-logo-hero-text">STA</span>
            </div>
            
            <!-- <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                Professional Admin<br>
                <span style="background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Dashboard</span>
            </h1> -->
            
            <p class="text-xl text-white opacity-90 mb-8 max-w-2xl mx-auto leading-relaxed">
                Streamline your administrative tasks with our modern, intuitive dashboard. 
                Manage users, roles, and permissions with ease.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                @auth
                    <a href="{{ url('/dashboard') }}" class="cta-button">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="cta-button">
                        Get Started
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" style="background: rgba(255, 255, 255, 0.1); color: white; padding: 1rem 2.5rem; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; display: inline-block; border: 2px solid rgba(255, 255, 255, 0.3);" 
                           onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.textDecoration='none';" 
                           onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.textDecoration='none';">
                            Register Now
                        </a>
                    @endif
                @endauth
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Secure</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Available</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">Fast</span>
                    <span class="stat-label">Performance</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">Modern</span>
                    <span class="stat-label">Interface</span>
                </div>
            </div>
            
            <!-- Scroll indicator -->
            <div class="text-center mt-12">
                <div class="animate-bounce">
                    <svg class="w-6 h-6 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
                <p class="text-white text-sm mt-2 opacity-75">Scroll for more</p>
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="hero-content py-20 px-6">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-white text-center mb-12">
                Key Features
            </h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="feature-card">
                    <div class="feature-icon">
                        👥
                    </div>
                    <h3 class="text-xl font-bold mb-3">User Management</h3>
                    <p class="opacity-90">
                        Comprehensive user management with role-based access control and permissions.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        🛡️
                    </div>
                    <h3 class="text-xl font-bold mb-3">Security First</h3>
                    <p class="opacity-90">
                        Built with security best practices and modern authentication systems.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        📱
                    </div>
                    <h3 class="text-xl font-bold mb-3">Responsive Design</h3>
                    <p class="opacity-90">
                        Perfect experience across all devices with our mobile-first approach.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="hero-content py-8 px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <p class="text-white opacity-70">
                © {{ date('Y') }} STA. Professional Admin Dashboard Solution.
            </p>
        </div>
    </footer>
    
</body>
</html>