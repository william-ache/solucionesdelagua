<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Soluciones del Agua - ERP</title>
    <!-- Dark Mode Anti-Flash Script -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <!-- Favicon Gota de Agua -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2322b0ea'%3E%3Cpath d='M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z'/%3E%3C/svg%3E">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Smooth scrolling globally */
        html, body, .overflow-y-auto, .overflow-auto, main, aside, nav {
            scroll-behavior: smooth !important;
        }

        /* Beautiful Slim & Brand-Colored Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }

        ::-webkit-scrollbar-thumb {
            background: #0d47a1; /* Brand Blue */
            border-radius: 9999px;
            border: 1px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #00b0ff; /* Brand Light Blue */
        }

        /* Firefox Support */
        * {
            scrollbar-width: thin;
            scrollbar-color: #0d47a1 #f1f5f9;
        }

        /* Dynamic Dark Mode overrides */
        .dark body {
            background-color: #0f172a !important; /* slate-900 */
            color: #f8fafc !important; /* slate-50 */
        }
        .dark header {
            background-color: #1e293b !important; /* slate-800 */
            border-bottom: 1px solid #334155 !important; /* slate-700 */
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2) !important;
        }
        .dark header i, .dark header span, .dark header button {
            color: #e2e8f0 !important;
        }
        .dark header input {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }
        .dark header input::placeholder {
            color: #94a3b8 !important;
        }
        .dark .bg-white {
            background-color: #1e293b !important; /* slate-800 */
            color: #f8fafc !important;
        }
        .dark .border-gray-150, .dark .border-gray-100, .dark .border-gray-200 {
            border-color: #334155 !important; /* slate-700 */
        }
        .dark .border-x, .dark .border-y, .dark .border-t, .dark .border-b {
            border-color: #334155 !important;
        }
        .dark select, .dark input, .dark textarea {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #475569 !important;
        }
        .dark select option {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }
        .dark text-gray-800, .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600 {
            color: #e2e8f0 !important; /* slate-200 */
        }
        .dark .text-gray-500, .dark .text-gray-400 {
            color: #94a3b8 !important; /* slate-400 */
        }
        .dark .bg-gray-50, .dark .bg-gray-50\/50, .dark .bg-gray-50\/30 {
            background-color: #0f172a !important; /* slate-900 */
        }
        .dark .hover\:bg-gray-50:hover, .dark .hover\:bg-gray-100:hover, .dark .hover\:bg-gray-50\/50:hover {
            background-color: #334155 !important;
        }
        .dark .divide-y > :not([hidden]) ~ :not([hidden]) {
            border-color: #334155 !important;
        }
        /* Keep buttons text white */
        .dark .text-white {
            color: #ffffff !important;
        }
        .dark .bg-green-50, .dark .bg-green-100 {
            background-color: #064e3b !important;
            color: #34d399 !important;
            border-color: #047857 !important;
        }
        .dark .text-green-700, .dark .text-green-600 {
            color: #34d399 !important;
        }
        .dark .bg-red-50, .dark .bg-red-100 {
            background-color: #7f1d1d !important;
            color: #fca5a5 !important;
            border-color: #b91c1c !important;
        }
        .dark .text-red-700, .dark .text-red-650 {
            color: #fca5a5 !important;
        }
        .dark td {
            color: #cbd5e1 !important; /* slate-350 */
        }
        .dark th {
            color: #ffffff !important;
        }
        .dark .bg-brand-blue {
            background-color: #00457c !important; 
        }
        .dark .shadow-2xl, .dark .shadow-xl {
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5) !important;
        }
        .dark ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #1e40af;
            border: 1px solid #1e293b;
        }
        .dark * {
            scrollbar-color: #1e40af #1e293b;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: false, 
          currentTime: '', 
          bcvRate: 'Cargando...',
          bcvDate: '',
          theme: localStorage.getItem('theme') || 'light',
          toggleTheme() {
              this.theme = this.theme === 'light' ? 'dark' : 'light';
              localStorage.setItem('theme', this.theme);
              if (this.theme === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          },
          // Global Detail Modal State and Actions
          detailModalOpen: false,
          detailLoading: false,
          detailData: null,
          openDetailModal(model, id) {
              this.detailLoading = true;
              this.detailModalOpen = true;
              this.detailData = null;
              
              fetch('/global-detail?model=' + encodeURIComponent(model) + '&id=' + encodeURIComponent(id))
                  .then(res => res.json())
                  .then(data => {
                      this.detailData = data;
                      this.detailLoading = false;
                  })
                  .catch(err => {
                      console.error(err);
                      this.detailLoading = false;
                      this.detailModalOpen = false;
                      window.Toast.fire({ icon: 'error', title: 'Error al cargar detalles.' });
                  });
          },
          updateTime() {
              const now = new Date();
              this.currentTime = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          },
          fetchBCV() {
              fetch('https://ve.dolarapi.com/v1/dolares/oficial')
                  .then(res => res.json())
                  .then(data => {
                      this.bcvRate = data.promedio ? parseFloat(data.promedio).toFixed(2) : 'N/D';
                      if (data.fechaActualizacion) {
                          const dateObj = new Date(data.fechaActualizacion);
                          const dateStr = dateObj.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit' });
                          const timeStr = dateObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', hour12: true });
                          this.bcvDate = `${dateStr} - ${timeStr}`;
                      }
                  })
                  .catch(err => {
                      this.bcvRate = 'Error';
                      console.error('Error fetching BCV rate:', err);
                  });
          },
          init() {
              this.updateTime();
              setInterval(() => this.updateTime(), 1000);
              this.fetchBCV();
              // Update rate every 15 minutes
              setInterval(() => this.fetchBCV(), 900000);

              // Custom global listener to catch Ver details requests
              window.addEventListener('open-global-detail', (e) => {
                  if (e.detail && e.detail.model && e.detail.id) {
                      this.openDetailModal(e.detail.model, e.detail.id);
                  }
              });
          }
      }">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="flex-shrink-0 bg-brand-blue text-white flex flex-col transition-all duration-300 md:translate-x-0 absolute md:relative z-20 h-full shadow-xl"
               :class="{
                   'translate-x-0': sidebarOpen, 
                   '-translate-x-full': !sidebarOpen,
                   'w-64 md:w-64': !sidebarCollapsed,
                   'w-64 md:w-20': sidebarCollapsed
               }">
            <div class="flex items-center bg-brand-blue shadow-md h-16 shrink-0 border-b border-blue-800 transition-all duration-300"
                 :class="sidebarCollapsed ? 'md:p-3 md:justify-center p-4 justify-between' : 'p-4 justify-between'">
                <span class="text-lg font-bold flex items-center gap-2" :class="sidebarCollapsed ? 'md:justify-center' : ''">
                    <div class="w-10 h-10 rounded-full bg-white overflow-hidden p-0.5 flex-shrink-0 flex items-center justify-center border border-blue-900/40 shadow-inner">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-cover rounded-full scale-[1.10]" id="sidebar-logo" onerror="this.style.display='none'; document.getElementById('sidebar-water-fallback').classList.remove('hidden'); document.getElementById('sidebar-water-fallback').classList.add('flex');">
                        <span id="sidebar-water-fallback" class="hidden items-center justify-center text-brand-light text-sm font-bold"><i class="fa-solid fa-water"></i></span>
                    </div>
                    <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans transition-all duration-300">Soluciones</span>
                </span>
                <button @click="sidebarOpen = false" class="md:hidden text-white hover:text-brand-light">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4 transition-all duration-300">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('dashboard') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Dashboard">
                            <i class="fa-solid fa-chart-line w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'md:text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clients.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('clients.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Clientes">
                            <i class="fa-solid fa-users w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Clientes</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('sales.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('sales.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Ventas">
                            <i class="fa-solid fa-cart-shopping w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Ventas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('credits.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('credits.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Créditos">
                            <i class="fa-solid fa-hand-holding-dollar w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Créditos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tax-payments.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('tax-payments.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Impuestos">
                            <i class="fa-solid fa-percent w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Impuestos</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('employees.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Nómina">
                            <i class="fa-solid fa-user-tie w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Nómina</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expenses.index') }}" 
                           class="flex items-center gap-3 py-3 transition-all duration-200 {{ Route::is('expenses.*') ? 'bg-white/10 border-l-4 border-brand-light' : 'text-gray-200 border-l-4 border-transparent hover:bg-white/5 hover:border-brand-light' }}"
                           :class="sidebarCollapsed ? 'md:px-0 md:justify-center px-6' : 'px-6'"
                           title="Gastos Operativos">
                            <i class="fa-solid fa-wallet w-5 text-center flex-shrink-0" :class="sidebarCollapsed ? 'text-lg' : ''"></i>
                            <span :class="sidebarCollapsed ? 'md:hidden' : 'inline'" class="font-sans text-sm font-medium">Gastos Operativos</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Sidebar Bottom Info Panel -->
            <div :class="sidebarCollapsed ? 'md:hidden' : 'block'" class="p-4 border-t border-blue-800 bg-blue-950/20 flex flex-col gap-2.5 shrink-0 select-none">
                <!-- BCV Exchange Rate Card -->
                <div class="bg-blue-900/40 rounded-lg p-2.5 border border-blue-700/30 flex flex-col gap-1.5 select-none">
                    <div class="flex items-center justify-between text-xs text-blue-200">
                        <span class="font-bold flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Dólar BCV
                        </span>
                    </div>
                    <div class="flex items-end justify-between mt-0.5 border-b border-blue-800/30 pb-1.5">
                        <span class="text-xs text-gray-400">Tasa Oficial</span>
                        <div class="flex items-center gap-1.5 shadow-sm">
                            <span class="text-sm font-black text-brand-light font-mono leading-none animate-fade-in" x-text="'Bs. ' + bcvRate"></span>
                            <button @click="navigator.clipboard.writeText(bcvRate); window.Toast.fire({ icon: 'success', title: 'Tasa copiada: Bs. ' + bcvRate })" 
                                    class="text-[11px] text-blue-300 hover:text-brand-light hover:scale-105 active:scale-95 transition-all outline-none" 
                                    title="Copiar Tasa">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[9px] text-gray-400 border-b border-blue-800/20 pb-1.5">
                        <span>Actualización</span>
                        <span class="font-medium opacity-80" x-text="bcvDate"></span>
                    </div>
                    <!-- Dynamic Live Hora Local (Placed Below Rate) -->
                    <div class="flex items-center justify-between text-[10px] text-blue-200">
                        <span class="font-semibold uppercase tracking-wider text-blue-300/80"><i class="fa-solid fa-clock mr-1"></i> Hora Local</span>
                        <span class="font-bold font-mono text-white text-xs" x-text="currentTime"></span>
                    </div>
                    <!-- Version (Placed Below Hora Local) -->
                    <div class="flex items-center justify-between text-[10px] text-blue-200/60 border-t border-blue-900/30 pt-1.5 mt-1.5">
                        <span class="font-semibold uppercase tracking-wider text-blue-300/60"><i class="fa-solid fa-code-branch mr-1"></i> Versión</span>
                        <span class="font-bold font-mono text-blue-300 text-[10px]">v{{ config('app.version', '1.0.0') }}</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col pt-0 relative overflow-hidden">
            <!-- Navbar -->
            <header class="bg-white shadow flex items-center justify-between p-4 h-16 z-30 w-full shrink-0">
                <div class="flex items-center gap-4">
                    <!-- Mobile Button -->
                    <button @click="sidebarOpen = true" class="md:hidden text-gray-500 hover:text-brand-blue">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <!-- Desktop Sidebar Collapsible Toggle Button -->
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:flex text-gray-500 hover:text-brand-blue bg-gray-50 hover:bg-gray-100 p-2 rounded-lg transition-colors items-center justify-center border border-gray-200 shadow-sm" title="Alternar menú">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                </div>
                
                <!-- Global Omni-Search Bar -->
                <div class="flex-1 max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg mx-4 relative hidden sm:block" x-data="{
                    searchQuery: '',
                    results: [],
                    loading: false,
                    showDropdown: false,
                    search() {
                        if (this.searchQuery.trim().length < 1) {
                            this.results = [];
                            this.showDropdown = false;
                            return;
                        }
                        this.loading = true;
                        this.showDropdown = true;
                        fetch('/global-search?q=' + encodeURIComponent(this.searchQuery))
                            .then(res => res.json())
                            .then(data => {
                                this.results = data;
                                this.loading = false;
                            })
                            .catch(err => {
                                console.error(err);
                                this.loading = false;
                            });
                    }
                }">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text"
                                x-model="searchQuery"
                                @input.debounce.300ms="search()"
                                @focus="showDropdown = true"
                                @click.away="showDropdown = false"
                                @keydown.escape.window="showDropdown = false"
                                placeholder="Buscar clientes, ventas, créditos, nómina..."
                                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-light focus:border-transparent bg-gray-50/50 hover:bg-gray-50 transition-all font-sans text-gray-700">
                        
                        <!-- Dropdown de Resultados -->
                        <div x-show="showDropdown && (results.length > 0 || loading || (searchQuery.trim().length >= 1 && !loading))"
                             x-transition
                             class="absolute left-0 mt-2 w-full bg-white rounded-lg shadow-xl border border-gray-150 py-1.5 z-50 max-h-96 overflow-y-auto"
                             x-cloak>
                            
                            <!-- Indicador de Carga -->
                            <div x-show="loading" class="px-4 py-3 text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-spinner animate-spin text-brand-blue"></i> Buscando coincidencias...
                            </div>
                            
                            <!-- Sin Resultados -->
                            <div x-show="!loading && results.length === 0 && searchQuery.trim().length >= 1" class="px-4 py-3.5 text-xs text-gray-400 flex items-center gap-2">
                                <i class="fa-solid fa-face-frown text-sm"></i> No se encontraron resultados para "<span class="font-semibold text-gray-600" x-text="searchQuery"></span>"
                            </div>
                            
                            <!-- Lista de Resultados -->
                            <div x-show="!loading && results.length > 0">
                                <template x-for="item in results">
                                    <a href="#" @click.prevent="openDetailModal(item.model, item.id); showDropdown = false" class="flex items-start gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100/80 text-gray-600 flex items-center justify-center mt-0.5 text-xs">
                                            <i :class="item.icon"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs font-bold text-gray-800 truncate" x-text="item.title"></span>
                                                <span class="text-[8px] font-black uppercase px-2 py-0.5 rounded-full tracking-wider flex-shrink-0 border"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border-emerald-200/50': item.type === 'Cliente',
                                                          'bg-red-50 text-red-700 border-red-200/50': item.type === 'Crédito / Cuentas por Cobrar',
                                                          'bg-sky-50 text-sky-700 border-sky-200/50': item.type === 'Venta / Facturación',
                                                          'bg-indigo-50 text-indigo-700 border-indigo-200/50': item.type === 'Colaborador / Nómina',
                                                          'bg-orange-50 text-orange-700 border-orange-200/50': item.type === 'Gasto Operativo',
                                                          'bg-amber-50 text-amber-700 border-amber-200/50': item.type === 'Impuestos / Fiscos'
                                                      }"
                                                      x-text="item.type"></span>
                                            </div>
                                            <span class="text-[10px] text-gray-500 truncate block mt-0.5" x-text="item.subtitle"></span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ml-auto flex items-center gap-3 relative">
                    <!-- Modern Minimalist Theme Toggle Switcher -->
                    <button @click="toggleTheme()"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full bg-gray-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none select-none shadow-inner border border-gray-300 dark:border-slate-600 p-0.5"
                            title="Alternar Modo Claro / Oscuro">
                        <span class="sr-only">Tema</span>
                        <!-- Sun Icon (Left Back) -->
                        <span class="absolute left-1.5 flex items-center justify-center text-[10px] text-amber-400 pointer-events-none transition-opacity duration-300"
                              :class="theme === 'light' ? 'opacity-100' : 'opacity-30'">
                            <i class="fa-solid fa-sun"></i>
                        </span>
                        <!-- Moon Icon (Right Back) -->
                        <span class="absolute right-1.5 flex items-center justify-center text-[10px] text-indigo-300 pointer-events-none transition-opacity duration-300"
                              :class="theme === 'dark' ? 'opacity-100' : 'opacity-30'">
                            <i class="fa-solid fa-moon"></i>
                        </span>
                        <!-- Sliding Indicator Circle -->
                        <span class="pointer-events-none block h-5 w-5 rounded-full bg-white dark:bg-brand-light shadow-md ring-0 transform transition-transform duration-300 ease-out"
                              :class="theme === 'dark' ? 'translate-x-5' : 'translate-x-0'">
                        </span>
                    </button>

                    <div x-data="{ profileDropdownOpen: false }" class="relative">
                        <div @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800 p-1.5 rounded-lg transition-colors select-none">
                            <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold font-sans">
                                A
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden md:inline-block">Admin</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-200" :class="{'rotate-180': profileDropdownOpen}"></i>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="profileDropdownOpen" 
                             @click.away="profileDropdownOpen = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-lg border border-gray-150 py-1 z-50"
                             x-cloak>
                            
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Mi Cuenta</p>
                                <p class="text-xs font-bold text-gray-800">Administrador</p>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors font-bold">
                                    <i class="fa-solid fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50 relative">
                <!-- Background Decorative Water Droplets (Subtle Watermarks) -->
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none z-0">
                    <!-- Top Perimeter -->
                    <i class="fa-solid fa-droplet text-brand-blue/[0.045] text-[5rem] absolute top-4 left-6"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.04] text-[4rem] absolute top-8 left-1/3"></i>
                    <i class="fa-solid fa-droplet text-brand-blue/[0.04] text-[4.5rem] absolute top-12 right-1/3"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.05] text-[6rem] absolute top-4 right-8"></i>
                    
                    <!-- Middle Perimeter -->
                    <i class="fa-solid fa-droplet text-brand-blue/[0.045] text-[5.5rem] absolute top-1/3 left-4"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.05] text-[6rem] absolute top-1/3 right-4"></i>
                    
                    <!-- Bottom Perimeter -->
                    <i class="fa-solid fa-droplet text-brand-blue/[0.04] text-[5rem] absolute bottom-1/3 left-1/4"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.04] text-[5.5rem] absolute bottom-1/3 right-1/4"></i>
                    <i class="fa-solid fa-droplet text-brand-blue/[0.045] text-[6rem] absolute bottom-6 left-8"></i>
                    <i class="fa-solid fa-droplet text-brand-light/[0.05] text-[7rem] absolute bottom-6 right-8"></i>
                </div>
                
                <div class="relative z-10">
                    @yield('content')
                </div>
            </main>
        </div>
        
    </div>

    <!-- Global Details Modal (Breathtaking Design) -->
    <div x-show="detailModalOpen" 
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[90] flex items-center justify-center p-4 transition-all duration-300"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all border border-gray-150 dark:border-gray-700" 
             @click.away="detailModalOpen = false"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between border-b border-blue-900/10">
                <div class="flex items-center gap-2">
                    <span class="text-xs uppercase font-extrabold px-2 py-0.5 rounded bg-white/20 text-white tracking-widest" x-text="detailData ? detailData.type : 'Cargando'"></span>
                    <h3 class="text-base font-bold truncate max-w-[240px]" x-text="detailData ? detailData.title : 'Consultando Registro'"></h3>
                </div>
                <button @click="detailModalOpen = false" class="text-white/80 hover:text-white text-lg transition-colors focus:outline-none"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Loading Animation -->
                <div x-show="detailLoading" class="flex flex-col items-center justify-center py-12 gap-3">
                    <i class="fa-solid fa-spinner animate-spin text-4xl text-brand-blue dark:text-brand-light"></i>
                    <p class="text-xs text-gray-500 font-sans">Recuperando información confidencial...</p>
                </div>
                
                <!-- Dynamic Ledger Data -->
                <div x-show="!detailLoading && detailData">
                    <div class="rounded-xl bg-gray-50/50 dark:bg-gray-900 border border-gray-100 dark:border-gray-700 divide-y divide-gray-100/60 dark:divide-gray-700/60 overflow-hidden font-sans">
                        <template x-for="(value, key) in (detailData ? detailData.details : {})">
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider" x-text="key"></span>
                                <span class="text-sm font-semibold text-gray-850 dark:text-gray-200" x-text="value"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 dark:bg-gray-950 px-6 py-4 flex justify-end border-t border-gray-100 dark:border-gray-700/60">
                <button @click="detailModalOpen = false" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold text-xs px-5 py-2.5 rounded-lg transition-all shadow focus:outline-none">Cerrar Detalle</button>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
