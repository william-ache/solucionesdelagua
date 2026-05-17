@extends('layouts.app')

@section('content')
<div x-data="exchangeRateManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6 max-w-3xl">
        <!-- Header (Title, description, and button) inside white card -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">Divisas y Tasas de Cambio</h1>
                <p class="text-sm text-gray-500 mt-1">Historial diario del tipo de cambio oficial del Banco Central (BCV)</p>
            </div>
            <button @click="resetForm(); openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all self-start sm:self-auto flex-shrink-0">
                <i class="fa-solid fa-plus"></i> Nueva Tasa
            </button>
        </div>

        <!-- Datatable Controls -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Entries Selector -->
            <div class="flex items-center gap-2 text-sm text-gray-555">
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
            <div class="relative w-full md:w-64">
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
                        <th @click="sort('date')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-850 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Fecha de la Tasa
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'date' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('rate')" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-850 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center justify-end gap-1.5">
                                Tasa de Cambio Oficial
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'rate' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider border-b border-blue-850">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="rate in pagedRates" :key="rate.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-semibold" x-text="formatDate(rate.date)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-black text-brand-blue font-mono"
                                x-text="'Bs. ' + parseFloat(rate.rate || 0).toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 })">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="editRate(rate)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteRate(rate)" class="text-red-650 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredRates.length === 0">
                        <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-money-bill-transfer text-4xl mb-3 block"></i> No hay tasas registradas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredRates.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredRates.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredRates.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Create Modal (Alpine.js) -->
    <div x-show="openCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-money-bill-transfer mr-1"></i> Añadir Tipo de Cambio</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitCreateForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto en BS por Dólar</label>
                        <input type="number" step="0.0001" x-model="currentRate.rate" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: 36.5420">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.rate ? errors.rate[0] : ''" x-show="errors.rate"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de la Tasa</label>
                        <input type="date" x-model="currentRate.date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.date ? errors.date[0] : ''" x-show="errors.date"></span>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Guardar Tasa</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal (Alpine.js) -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Tipo de Cambio</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto en BS por Dólar</label>
                        <input type="number" step="0.0001" x-model="currentRate.rate" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: 36.5420">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.rate ? errors.rate[0] : ''" x-show="errors.rate"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de la Tasa</label>
                        <input type="date" x-model="currentRate.date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.date ? errors.date[0] : ''" x-show="errors.date"></span>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar Tasa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('exchangeRateManager', () => ({
        rates: @json($rates),
        openCreateModal: false,
        openEditModal: false,
        currentRate: { id: '', rate: '', date: '{{ date('Y-m-d') }}' },
        errors: {},
        
        // Datatable states
        searchQuery: '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'date',
        sortDirection: 'desc',

        get filteredRates() {
            let result = this.rates || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(r => 
                    (r.date && r.date.toLowerCase().includes(query)) ||
                    (r.rate && String(r.rate).toLowerCase().includes(query))
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

        get pagedRates() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredRates.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredRates.length / this.perPage) || 1;
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
        
        resetForm() {
            this.currentRate = { id: '', rate: '', date: '{{ date('Y-m-d') }}' };
            this.errors = {};
        },
        formatDate(dateStr) {
            if (!dateStr) return '-';
            const parts = dateStr.substr(0, 10).split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
            return dateStr;
        },
        submitCreateForm() {
            this.errors = {};
            fetch('{{ route('exchange-rates.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentRate)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                    } else {
                        throw new Error(data.message || 'Error desconocido.');
                    }
                } else {
                    this.rates.push(data.rate);
                    this.rates.sort((a, b) => b.date.localeCompare(a.date));
                    this.openCreateModal = false;
                    this.resetForm();
                    window.Toast.fire({
                        icon: 'success',
                        title: 'Tasa de cambio guardada'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar la tasa.',
                    confirmButtonColor: '#005293'
                });
            });
        },
        editRate(rate) {
            this.errors = {};
            let rateDate = rate.date;
            if (rateDate && rateDate.includes('T')) {
                rateDate = rateDate.split('T')[0];
            } else if (rateDate && rateDate.includes(' ')) {
                rateDate = rateDate.split(' ')[0];
            }
            this.currentRate = { id: rate.id, rate: rate.rate, date: rateDate };
            this.openEditModal = true;
        },
        submitEditForm() {
            this.errors = {};
            fetch(`/exchange-rates/${this.currentRate.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentRate)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                    } else {
                        throw new Error(data.message || 'Error desconocido.');
                    }
                } else {
                    const idx = this.rates.findIndex(r => r.id === this.currentRate.id);
                    if (idx !== -1) {
                        this.rates[idx] = data.rate;
                    }
                    this.rates.sort((a, b) => b.date.localeCompare(a.date));
                    this.openEditModal = false;
                    this.resetForm();
                    window.Toast.fire({
                        icon: 'success',
                        title: 'Tasa actualizada con éxito'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar la tasa.',
                    confirmButtonColor: '#005293'
                });
            });
        },
        deleteRate(rate) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar la tasa de cambio del ${this.formatDate(rate.date)}. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005293',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/exchange-rates/${rate.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar la tasa.');
                        }
                        this.rates = this.rates.filter(r => r.id !== rate.id);
                        window.Toast.fire({
                            icon: 'success',
                            title: 'Tasa eliminada con éxito'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Ocurrió un error al eliminar la tasa.',
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
