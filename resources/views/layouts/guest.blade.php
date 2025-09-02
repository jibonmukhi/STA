<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>STA - Admin Dashboard</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * {
                font-family: 'Inter', sans-serif;
            }

            .sta-logo-container {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
                border: 2px solid rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
            }

            .sta-logo-text {
                font-size: 2rem;
                font-weight: 900;
                color: white;
                letter-spacing: 2px;
                text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }

            .auth-background {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                background-attachment: fixed;
                position: relative;
                overflow: hidden;
            }

            .auth-background::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
                opacity: 0.3;
            }

            .auth-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
                border-radius: 20px;
                transition: all 0.3s ease;
            }

            .auth-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
            }

            .floating-shapes {
                position: absolute;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: 1;
            }

            .floating-shapes::before,
            .floating-shapes::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                animation: float 6s ease-in-out infinite;
            }

            .floating-shapes::before {
                width: 200px;
                height: 200px;
                top: -50px;
                right: -50px;
                animation-delay: 0s;
            }

            .floating-shapes::after {
                width: 150px;
                height: 150px;
                bottom: -30px;
                left: -30px;
                animation-delay: 3s;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }

            .content-wrapper {
                position: relative;
                z-index: 2;
            }

            .brand-title {
                color: white;
                font-size: 1.5rem;
                font-weight: 700;
                margin-top: 1rem;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
                letter-spacing: 1px;
            }

            .brand-subtitle {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.9rem;
                font-weight: 500;
                margin-top: 0.5rem;
                letter-spacing: 0.5px;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen auth-background flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="floating-shapes"></div>
            
            <div class="content-wrapper text-center">
                <a href="/">
                    <x-application-logo />
                </a>
                <!-- <h1 class="brand-title">STA</h1>
                <p class="brand-subtitle">Professional Admin Dashboard</p> -->
            </div>

            <div class="w-full sm:max-w-md mt-8 px-8 py-8 auth-card content-wrapper">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
