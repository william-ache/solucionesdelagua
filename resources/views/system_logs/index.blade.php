@extends('layouts.app')

@section('content')
<div x-data="logManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header Page -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans"><i class="fa-solid fa-clipboard-list text-brand-light mr-2"></i> Bitácora del Sistema</h1>
                <p class="text-sm text-gray-500 mt-1">Registro de actividad y acciones inmutables de los usuarios en el sistema.</p>
            </div>
        </div>
        <!-- Datatable Controls -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Entries Selector -->
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Mostrar</span>
                <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-brand-light bg-white font-medium text-gray-700">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>registros</span>
            </div>
            
            <!-- Search & Actions -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Export Actions -->
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end sm:justify-start">
                    <button @click="window.exportToExcel(filteredLogs, ['formatted_date', 'user_name', 'module', 'action', 'description'], ['Fecha y Hora', 'Usuario', 'Módulo', 'Acción', 'Descripción'], 'Bitacora_SolucionesDelAgua')"
                            type="button"
                            class="flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-emerald-50 text-emerald-700 hover:text-emerald-800 border border-gray-300 hover:border-emerald-300 rounded-lg text-xs font-bold transition-all shadow-sm select-none"
                            title="Exportar registros filtrados a Excel">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                    </button>
                    <button @click="window.exportToPDF(filteredLogs, ['formatted_date', 'user_name', 'module', 'action', 'description'], ['Fecha y Hora', 'Usuario', 'Módulo', 'Acción', 'Descripción'], 'Reporte de Bitácora del Sistema')"
                            type="button"
                            class="flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-red-50 text-red-650 hover:text-red-700 border border-gray-300 hover:border-red-300 rounded-lg text-xs font-bold transition-all shadow-sm select-none"
                            title="Generar Reporte PDF para imprimir">
                        <i class="fa-solid fa-file-pdf text-red-500"></i> PDF
                    </button>
                </div>

                <!-- Search Box -->
                <div class="relative w-full sm:w-64 md:w-72">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar registros..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-light bg-white text-gray-750">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-brand-blue text-white">
                    <tr>
                        <th @click="sort('created_at')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors w-40">
                            <div class="flex items-center gap-1.5">
                                Fecha y Hora
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'created_at' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('user_name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Usuario
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'user_name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('module')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Módulo
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'module' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('action')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Acción
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'action' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('description')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Descripción Corta
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'description' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider border-b border-blue-800 w-24">
                            Ver Detalle
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-sm text-gray-700">
                    <template x-for="log in pagedLogs" :key="log.id">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500" x-text="log.formatted_date"></td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900" x-text="log.user_name"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-indigo-50 text-indigo-700 font-bold px-2.5 py-1 rounded-md text-[10px]" x-text="log.module"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold" 
                                      :class="{
                                          'bg-green-100 text-green-700': log.action.toLowerCase().includes('crear'),
                                          'bg-amber-100 text-amber-700': log.action.toLowerCase().includes('editar') || log.action.toLowerCase().includes('actualizar'),
                                          'bg-red-100 text-red-700': log.action.toLowerCase().includes('eliminar'),
                                          'bg-gray-100 text-gray-700': !log.action.toLowerCase().includes('crear') && !log.action.toLowerCase().includes('editar') && !log.action.toLowerCase().includes('eliminar') && !log.action.toLowerCase().includes('actualizar')
                                      }" x-text="log.action"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate text-xs text-gray-600" x-text="log.description" :title="log.description"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button @click="openModal(log)" 
                                        class="w-7 h-7 rounded-md bg-brand-light/10 text-brand-blue hover:bg-brand-blue hover:text-white transition-all flex items-center justify-center mx-auto" title="Ver detalles completos">
                                    <i class="fa-solid fa-eye text-[11px]"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredLogs.length === 0">
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p>No se encontraron registros en la bitácora que coincidan con la búsqueda.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredLogs.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredLogs.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredLogs.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in visiblePages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- View Modal (Alpine.js) -->
    <div x-show="openViewModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[80] flex items-start justify-center p-4 pt-24 pb-6 overflow-y-auto" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all my-2" @click.away="openViewModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold"><i class="fa-solid fa-list-check mr-1"></i> Detalles del Registro</h3>
                    <p class="text-[10px] text-blue-100 mt-1 uppercase tracking-wider" x-text="'ID Transacción: ' + currentLog.id"></p>
                </div>
                <button @click="openViewModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-5 pb-5 border-b border-gray-100">
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Fecha y Hora</span>
                        <div class="text-sm font-semibold text-gray-800" x-text="currentLog.formatted_date"></div>
                    </div>
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Usuario</span>
                        <div class="text-sm font-semibold text-gray-800" x-text="currentLog.user_name"></div>
                    </div>
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Módulo Afectado</span>
                        <div class="text-sm font-semibold text-gray-800" x-text="currentLog.module"></div>
                    </div>
                    <div>
                        <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Tipo de Acción</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold" 
                              :class="{
                                  'bg-green-100 text-green-700': currentLog.action && currentLog.action.toLowerCase().includes('crear'),
                                  'bg-amber-100 text-amber-700': currentLog.action && (currentLog.action.toLowerCase().includes('editar') || currentLog.action.toLowerCase().includes('actualizar')),
                                  'bg-red-100 text-red-700': currentLog.action && currentLog.action.toLowerCase().includes('eliminar'),
                                  'bg-gray-100 text-gray-700': currentLog.action && !currentLog.action.toLowerCase().includes('crear') && !currentLog.action.toLowerCase().includes('editar') && !currentLog.action.toLowerCase().includes('eliminar') && !currentLog.action.toLowerCase().includes('actualizar')
                              }" x-text="currentLog.action"></span>
                    </div>
                </div>

                <div>
                    <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-2">Descripción Detallada</span>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed" x-text="currentLog.description"></div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <button type="button" @click="openViewModal = false" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-5 py-2 rounded-lg font-bold transition-all text-sm">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    // Parsing logs from Laravel and adding a formatted_date
    const rawLogs = @json($logs);
    const parsedLogs = rawLogs.map(l => {
        const d = new Date(l.created_at);
        return {
            ...l,
            formatted_date: d.toLocaleDateString('es-ES', { year: 'numeric', month: '2-digit', day: '2-digit' }) + ' ' + d.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })
        };
    });

    Alpine.data('logManager', () => ({
        openViewModal: false,
        logs: parsedLogs,
        currentLog: {},
        
        // Datatable states
        searchQuery: '',
        perPage: 25,
        currentPage: 1,
        sortColumn: 'created_at',
        sortDirection: 'desc',

        get filteredLogs() {
            let result = this.logs || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(l => 
                    (l.user_name && l.user_name.toLowerCase().includes(query)) ||
                    (l.module && l.module.toLowerCase().includes(query)) ||
                    (l.action && l.action.toLowerCase().includes(query)) ||
                    (l.description && l.description.toLowerCase().includes(query)) ||
                    (l.formatted_date && l.formatted_date.toLowerCase().includes(query))
                );
            }
            
            // Sort
            result.sort((a, b) => {
                let valA = a[this.sortColumn] || '';
                let valB = b[this.sortColumn] || '';
                
                if (typeof valA === 'string') valA = valA.toLowerCase();
                if (typeof valB === 'string') valB = valB.toLowerCase();
                
                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
            return result;
        },

        get pagedLogs() {
            const start = (this.currentPage - 1) * parseInt(this.perPage);
            return this.filteredLogs.slice(start, start + parseInt(this.perPage));
        },

        get totalPages() {
            return Math.ceil(this.filteredLogs.length / parseInt(this.perPage)) || 1;
        },

        get visiblePages() {
            // Very simple pagination display max 5 pages
            let pages = [];
            let start = Math.max(1, this.currentPage - 2);
            let end = Math.min(this.totalPages, start + 4);
            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },

        sort(col) {
            if (this.sortColumn === col) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = col;
                this.sortDirection = 'asc';
            }
            this.currentPage = 1;
        },

        openModal(log) {
            this.currentLog = {...log};
            this.openViewModal = true;
        }
    }));
});
</script>
@endpush
