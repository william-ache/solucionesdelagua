@extends('layouts.app')

@section('content')
<div x-data="creditManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header (Title and description) inside white card -->
        <div class="p-6 border-b border-gray-150">
            <h1 class="text-2xl font-bold text-gray-800 font-sans">Créditos y Cuentas por Cobrar</h1>
            <p class="text-sm text-gray-500 mt-1">Listado de saldos pendientes de ventas a crédito</p>
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
            
            <!-- Search & Actions -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Export Buttons -->
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end sm:justify-start">
                    <button @click="window.exportToExcel(filteredCredits, ['sale_id', 'sale.client_name', 'due_date', 'total_debt', 'balance_due', 'status'], ['Venta ID', 'Cliente', 'Vence / Límite', 'Monto Deuda', 'Balance Pendiente', 'Estatus'], 'Creditos_SolucionesDelAgua')"
                            type="button"
                            class="flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-emerald-50 text-emerald-700 hover:text-emerald-800 border border-gray-300 hover:border-emerald-300 rounded-lg text-xs font-bold transition-all shadow-sm select-none"
                            title="Exportar registros filtrados a Excel (CSV)">
                        <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                    </button>
                    <button @click="window.exportToPDF(filteredCredits, ['sale_id', 'sale.client_name', 'due_date', 'total_debt', 'balance_due', 'status'], ['Venta ID', 'Cliente', 'Vence / Límite', 'Monto Deuda', 'Balance Pendiente', 'Estatus'], 'Reporte de Créditos y Cuentas por Cobrar')"
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
                    <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-light bg-white text-gray-750">
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-brand-blue text-white">
                    <tr>
                        <th @click="sort('sale_id')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Venta ID
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'sale_id' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('client_name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Cliente
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'client_name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('due_date')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Límite / Vence
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'due_date' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('total_debt')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Monto Deuda
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'total_debt' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('balance_due')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Balance Pendiente
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'balance_due' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('status')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Estatus
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'status' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="credit in pagedCredits" :key="credit.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700 font-mono" x-text="'#' + credit.sale_id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800" x-text="credit.sale ? credit.sale.client_name : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550" x-text="credit.due_date ? new Date(credit.due_date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' }) : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border inline-block min-w-[70px] text-center bg-gray-50 text-gray-700 border-gray-200"
                                      x-text="'$' + parseFloat(credit.total_debt).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border inline-block min-w-[70px] text-center"
                                      :class="parseFloat(credit.balance_due) > 0 ? 'bg-red-50 text-red-700 border-red-200/60' : 'bg-green-50 text-green-700 border-green-200/60'"
                                      x-text="'$' + parseFloat(credit.balance_due).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                      :class="credit.status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                      x-text="credit.status === 'paid' ? 'Solvente' : 'Pendiente'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="$dispatch('open-global-detail', { model: 'credit', id: credit.id })" class="text-indigo-600 hover:text-indigo-900 transition-colors bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a :href="'/credits/' + credit.id" class="text-emerald-600 hover:text-emerald-900 transition-colors bg-emerald-50 hover:bg-emerald-100 p-1.5 rounded-lg" title="Ver Detalle/Abonar">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </a>
                                    <button @click="editCredit(credit)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteCredit(credit)" class="text-red-650 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredCredits.length === 0">
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-hand-holding-dollar text-4xl mb-3 block"></i> No se encontraron créditos registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredCredits.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredCredits.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredCredits.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-320 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Edit Modal (Alpine.js) -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Crédito</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cliente</label>
                        <input type="text" :value="currentCredit.sale ? currentCredit.sale.client_name : '-'" disabled class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-500 font-bold focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Deuda</label>
                            <input type="number" step="0.01" x-model="currentCredit.total_debt" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Saldo Restante</label>
                            <input type="number" step="0.01" x-model="currentCredit.balance_due" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Límite</label>
                        <input type="date" x-model="currentCredit.due_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado de Pago</label>
                        <select x-model="currentCredit.status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                            <option value="pending">Pendiente</option>
                            <option value="paid">Solvente</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar Crédito</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('creditManager', () => ({
        openEditModal: false,
        currentCredit: { id: '', sale_id: '', due_date: '', total_debt: '', balance_due: '', status: 'pending', sale: null },
        credits: @json($credits),
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'due_date',
        sortDirection: 'asc',
        
        get filteredCredits() {
            let result = this.credits || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(c => 
                    (c.sale_id && String(c.sale_id).includes(query)) ||
                    (c.sale && c.sale.client_name && c.sale.client_name.toLowerCase().includes(query)) ||
                    (c.status && c.status.toLowerCase().includes(query))
                );
            }
            
            // Sort
            result.sort((a, b) => {
                let valA = '';
                let valB = '';
                
                if (this.sortColumn === 'client_name') {
                    valA = a.sale ? (a.sale.client_name || '') : '';
                    valB = b.sale ? (b.sale.client_name || '') : '';
                } else {
                    valA = a[this.sortColumn] || '';
                    valB = b[this.sortColumn] || '';
                }
                
                if (typeof valA === 'string') valA = valA.toLowerCase();
                if (typeof valB === 'string') valB = valB.toLowerCase();
                
                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
            return result;
        },
        
        get pagedCredits() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredCredits.slice(start, start + this.perPage);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredCredits.length / this.perPage) || 1;
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

        editCredit(credit) {
            this.currentCredit = { ...credit };
            if (credit.due_date) {
                this.currentCredit.due_date = credit.due_date.split('T')[0];
            }
            this.openEditModal = true;
        },

        submitEditForm() {
            fetch(`/credits/${this.currentCredit.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentCredit)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar el crédito.');
                }
                const idx = this.credits.findIndex(c => c.id === this.currentCredit.id);
                if (idx !== -1) {
                    this.credits[idx] = data.credit;
                }
                this.openEditModal = false;
                window.Toast.fire({
                    icon: 'success',
                    title: 'Crédito actualizado con éxito'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar.',
                    confirmButtonColor: '#005293'
                });
            });
        },

        deleteCredit(credit) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar el crédito #${credit.id} del cliente "${credit.sale ? credit.sale.client_name : ''}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005293',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/credits/${credit.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar el crédito.');
                        }
                        this.credits = this.credits.filter(c => c.id !== credit.id);
                        window.Toast.fire({
                            icon: 'success',
                            title: 'Crédito eliminado con éxito'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Ocurrió un error al eliminar.',
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
