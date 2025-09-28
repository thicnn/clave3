<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Clave Tres') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-inter">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ url('/') }}" class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">CT</span>
                            </div>
                            <span class="text-xl font-semibold text-gray-900">Clave Tres</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    @auth
                        <nav class="hidden md:flex space-x-8">
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('pedidos.index') }}" class="text-sm font-medium {{ request()->routeIs('pedidos.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Pedidos
                            </a>
                            <a href="{{ route('clientes.index') }}" class="text-sm font-medium {{ request()->routeIs('clientes.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Clientes
                            </a>
                            <a href="{{ route('insumos.index') }}" class="text-sm font-medium {{ request()->routeIs('insumos.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Insumos
                            </a>
                            <a href="{{ route('maquinas.index') }}" class="text-sm font-medium {{ request()->routeIs('maquinas.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Máquinas
                            </a>
                            <a href="{{ route('productos.index') }}" class="text-sm font-medium {{ request()->routeIs('productos.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Productos
                            </a>
                            <a href="{{ route('reports.dashboard') }}" class="text-sm font-medium {{ request()->routeIs('reports.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                                Reportes
                            </a>
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <div class="relative group">
                                        <button class="text-sm font-medium {{ request()->routeIs('gestion.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700' }} flex items-center">
                                            Gestión
                                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                                            <a href="{{ route('gestion.usuarios') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Usuarios</a>
                                            <a href="{{ route('gestion.pedidos-cancelados') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Pedidos Cancelados</a>
                                            <a href="{{ route('gestion.contadores') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Contadores</a>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </nav>
                    @endauth

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        @guest
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                    Iniciar Sesión
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-gray-900 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-800">
                                    Registrarse
                                </a>
                            @endif
                        @else
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</div>
                                </div>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="text-sm text-gray-500 hover:text-gray-700">
                                    Cerrar Sesión
                                </a>
                            </div>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @endguest

                        <!-- Mobile menu button -->
                        <button type="button" class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100" onclick="toggleMobileMenu()">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div id="mobile-menu" class="md:hidden hidden">
                    <div class="pt-2 pb-3 space-y-1 border-t border-gray-200">
                        @auth
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('pedidos.index') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('pedidos.*') ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                Pedidos
                            </a>
                            <a href="{{ route('clientes.index') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('clientes.*') ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                Clientes
                            </a>
                            <a href="{{ route('insumos.index') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('insumos.*') ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                Insumos
                            </a>
                            <a href="{{ route('productos.index') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('productos.*') ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                Productos
                            </a>
                            <a href="{{ route('reports.dashboard') }}" class="block px-3 py-2 text-base font-medium {{ request()->routeIs('reports.*') ? 'text-gray-900 bg-gray-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                                Reportes
                            </a>
                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-gray-200 pt-2 mt-2">
                                    <div class="px-3 py-2 text-sm font-medium text-gray-500">Gestión</div>
                                    <a href="{{ route('gestion.usuarios') }}" class="block px-6 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50">Usuarios</a>
                                    <a href="{{ route('gestion.pedidos-cancelados') }}" class="block px-6 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50">Pedidos Cancelados</a>
                                    <a href="{{ route('gestion.contadores') }}" class="block px-6 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50">Contadores</a>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="flex items-center space-x-3 mb-4 md:mb-0">
                        <div class="w-6 h-6 bg-gray-900 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">CT</span>
                        </div>
                        <span class="text-sm text-gray-600">© 2024 Clave Tres. Todos los derechos reservados.</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Sistema de Gestión Integral
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
