<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soluciones del Agua - ERP</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#005293',
                            light: '#22B0EA'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="flex-shrink-0 w-64 bg-brand-blue text-white flex flex-col transition-transform transform md:translate-x-0 absolute md:relative z-20 h-full shadow-xl"
               :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="p-4 flex items-center justify-between bg-brand-blue shadow-md h-16 shrink-0 border-b border-blue-800">
                <span class="text-lg font-bold flex items-center gap-2">
                    <div class="w-10 h-10 rounded-full bg-white overflow-hidden p-0.5 flex-shrink-0 flex items-center justify-center border border-blue-900/40 shadow-inner">
                        <img src="/logo.jpg" alt="Logo" class="w-full h-full object-cover rounded-full scale-[1.10]" id="sidebar-logo" onerror="this.style.display='none'; document.getElementById('sidebar-water-fallback').classList.remove('hidden'); document.getElementById('sidebar-water-fallback').classList.add('flex');">
                        <span id="sidebar-water-fallback" class="hidden items-center justify-center text-brand-light text-sm font-bold"><i class="fa-solid fa-water"></i></span>
                    </div>
                    Soluciones
                </span>
                <button @click="sidebarOpen = false" class="md:hidden text-white hover:text-brand-light">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('dashboard') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-chart-line w-5"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('clients.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-users w-5"></i> Clientes
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('sales.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-cart-shopping w-5"></i> Ventas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('credits.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('credits.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-hand-holding-dollar w-5"></i> Créditos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('exchange-rates.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('exchange-rates.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-money-bill-transfer w-5"></i> Divisas y Tasas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tax-payments.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('tax-payments.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-percent w-5"></i> Impuestos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('employees.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-user-tie w-5"></i> Nómina
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expenses.index') }}" class="flex items-center gap-3 px-6 py-3 {{ Route::is('expenses.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }} transition-colors">
                            <i class="fa-solid fa-wallet w-5"></i> Gastos Operativos
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="p-4 border-t border-blue-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 hover:bg-white/10 transition-colors text-gray-300 rounded">
                        <i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col pt-0 relative overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white shadow flex items-center justify-between p-4 h-16 z-10 w-full shrink-0">
                <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-brand-blue">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                
                <div class="ml-auto flex items-center gap-4">
                    <div class="flex items-center gap-2 cursor-pointer">
                        <div class="w-8 h-8 rounded-full bg-brand-light text-white flex items-center justify-center font-bold">
                            A
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden md:inline-block">Admin</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
                @yield('content')
            </main>
        </div>
        
    </div>

    @stack('scripts')
</body>
</html>
