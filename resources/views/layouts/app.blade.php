<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'API Gateway Testing') - Perbandingan REST dan GraphQL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .active-nav-link {
            color: #4f46e5;
            border-bottom: 2px solid #4f46e5;
        }
        .nav-link:hover {
            color: #4f46e5;
        }
    </style>
    @yield('head')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-code-branch text-primary-600 text-2xl"></i>
                <a href="/" class="text-xl font-bold text-gray-800">API Gateway Testing</a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <a href="https://github.com" target="_blank" class="text-gray-500 hover:text-primary-600">
                    <i class="fab fa-github text-xl"></i>
                </a>
                <a href="https://laravel.com" target="_blank" class="text-gray-500 hover:text-primary-600">
                    <i class="fab fa-laravel text-xl"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex overflow-x-auto whitespace-nowrap py-3 -mb-px">
                <a href="/" class="nav-link px-4 py-2 text-gray-700 font-medium {{ request()->is('/') ? 'active-nav-link' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="/repositories" class="nav-link px-4 py-2 text-gray-700 font-medium {{ request()->is('repositories') ? 'active-nav-link' : '' }}">
                    <i class="fas fa-code-branch mr-2"></i>Repository
                </a>
                <a href="/performance" class="nav-link px-4 py-2 text-gray-700 font-medium {{ request()->is('performance') ? 'active-nav-link' : '' }}">
                    <i class="fas fa-chart-line mr-2"></i>Metrik
                </a>
                <a href="/logs" class="nav-link px-4 py-2 text-gray-700 font-medium {{ request()->is('logs') ? 'active-nav-link' : '' }}">
                    <i class="fas fa-list-alt mr-2"></i>Log
                </a>
                <a href="/documentation" class="nav-link px-4 py-2 text-gray-700 font-medium {{ request()->is('documentation') ? 'active-nav-link' : '' }}">
                    <i class="fas fa-book mr-2"></i>Dokumentasi
                </a>
                <a href="{{ route('reports.summary') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('reports.summary') ? 'bg-gray-900 text-white' : '' }}">
                    <i class="fas fa-chart-line mr-2"></i>
                    Laporan Kesimpulan
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="text-gray-600">&copy; {{ date('Y') }} API Gateway Testing. Hak Cipta Dilindungi.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="/documentation" class="text-gray-600 hover:text-primary-600">
                        <i class="fas fa-book mr-1"></i>Dokumentasi
                    </a>
                    <a href="https://github.com" target="_blank" class="text-gray-600 hover:text-primary-600">
                        <i class="fab fa-github mr-1"></i>GitHub
                    </a>
                    <a href="https://laravel.com" target="_blank" class="text-gray-600 hover:text-primary-600">
                        <i class="fab fa-laravel mr-1"></i>Laravel
                    </a>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html> 