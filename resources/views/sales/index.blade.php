@extends('layouts.app')

@section('content')
<div x-data="saleManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header (Title, description, and button) inside white card -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">Módulo de Ventas</h1>
                <p class="text-sm text-gray-500 mt-1">Historial de facturación de equipos, químicos y proyectos</p>
            </div>
            <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all self-start sm:self-auto flex-shrink-0">
                <i class="fa-solid fa-cart-plus"></i> Nueva Venta
            </button>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="mx-6 mt-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center justify-between">
                <span class="text-sm"><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
            </div>
        @endif

        <!-- Datatable Controls -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Entries Selector -->
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Mostrar</span>
                <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-brand-light bg-white font-medium text-gray-700">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>registros</span>
            </div>
            
            <!-- Search Box -->
            <div class="relative w-full md:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-light bg-white">
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-brand-blue text-white">
                    <tr>
                        <th @click="sort('date')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Fecha
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'date' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('client_name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Cliente
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'client_name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('currency')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Moneda
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'currency' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('total_amount')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Total Facturado
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'total_amount' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('status')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Condición
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'status' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="sale in pagedSales" :key="sale.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550" x-text="new Date(sale.date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' })"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800" x-text="sale.client_name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550" x-text="sale.currency"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800 font-mono" x-text="(sale.currency === 'USD' ? '$' : 'Bs.') + parseFloat(sale.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                      :class="sale.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                                      x-text="sale.status === 'paid' ? 'Contado (Pagado)' : 'Crédito Pendiente'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="$dispatch('open-global-detail', { model: 'sale', id: sale.id })" class="text-indigo-600 hover:text-indigo-900 transition-colors bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button @click="editSale(sale)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteSale(sale)" class="text-red-650 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredSales.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-cart-shopping text-4xl mb-3 block"></i> No se encontraron ventas registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredSales.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredSales.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredSales.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-310 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Create Modal (Alpine.js) -->
    <div x-show="openCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-cart-plus mr-1"></i> Registrar Nueva Venta</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('sales.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del Cliente</label>
                        <input type="text" name="client_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Piscinas del Centro">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Total Facturado</label>
                            <input type="number" step="0.01" name="total_amount" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Moneda</label>
                            <select name="currency" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="USD">Dólares (USD)</option>
                                <option value="VES">Bolívares (VES)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Condición de Pago</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="paid">Contado</option>
                                <option value="credit">A Crédito</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Registrar Venta</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal (Alpine.js) -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Venta</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del Cliente</label>
                        <input type="text" x-model="currentSale.client_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Total Facturado</label>
                            <input type="number" step="0.01" x-model="currentSale.total_amount" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Moneda</label>
                            <select x-model="currentSale.currency" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="USD">Dólares (USD)</option>
                                <option value="VES">Bolívares (VES)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Condición de Pago</label>
                            <select x-model="currentSale.status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="paid">Contado</option>
                                <option value="credit">A Crédito</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
                            <input type="date" x-model="currentSale.date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('saleManager', () => ({
        openCreateModal: false,
        openEditModal: false,
        currentSale: { id: '', client_name: '', total_amount: '', currency: 'USD', status: 'paid', date: '' },
        sales: @json($sales),
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'date',
        sortDirection: 'desc',
        
        get filteredSales() {
            let result = this.sales || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(s => 
                    (s.client_name && s.client_name.toLowerCase().includes(query)) ||
                    (s.currency && s.currency.toLowerCase().includes(query))
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
        
        get pagedSales() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredSales.slice(start, start + this.perPage);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredSales.length / this.perPage) || 1;
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

        editSale(sale) {
            this.currentSale = { ...sale };
            if (sale.date) {
                this.currentSale.date = sale.date.split('T')[0];
            }
            this.openEditModal = true;
        },

        submitEditForm() {
            fetch(`/sales/${this.currentSale.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentSale)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar la venta.');
                }
                const idx = this.sales.findIndex(s => s.id === this.currentSale.id);
                if (idx !== -1) {
                    this.sales[idx] = data.sale;
                }
                this.openEditModal = false;
                window.Toast.fire({
                    icon: 'success',
                    title: 'Venta actualizada con éxito'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar la venta.',
                    confirmButtonColor: '#005293'
                });
            });
        },

        deleteSale(sale) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar la venta #${sale.id} para "${sale.client_name}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005293',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/sales/${sale.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar la venta.');
                        }
                        this.sales = this.sales.filter(s => s.id !== sale.id);
                        window.Toast.fire({
                            icon: 'success',
                            title: 'Venta eliminada con éxito'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Ocurrió un error al eliminar la venta.',
                            confirmButtonColor: '#005293'
                        });
                    });
                }
            });
        }
    }));
});
</script>
@endpush
