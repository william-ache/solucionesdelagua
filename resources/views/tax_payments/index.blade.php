@extends('layouts.app')

@section('content')
<div x-data="taxPaymentManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header (Title, description, and button) inside white card -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">Impuestos y Contribuciones Fiscales</h1>
                <p class="text-sm text-gray-500 mt-1">Historial de registros de pagos del IVA, ISLR y tasas municipales (SENIAT)</p>
            </div>
            <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all self-start sm:self-auto flex-shrink-0">
                <i class="fa-solid fa-plus"></i> Registrar Carga Fiscal
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
                        <th @click="sort('payment_date')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Fecha de Pago
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'payment_date' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('tax_name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Concepto de Alícuota
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'tax_name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('reference_number')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Número Referencia
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'reference_number' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('currency')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Moneda
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'currency' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('amount')" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center justify-end gap-1.5">
                                Monto Abonado
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'amount' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="p in pagedPayments" :key="p.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="p.payment_date ? new Date(p.payment_date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' }) : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800" x-text="p.tax_name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 font-mono" x-text="p.reference_number"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500" x-text="p.currency"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-500 font-mono" x-text="(p.currency === 'USD' ? '$' : 'Bs.') + parseFloat(p.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="$dispatch('open-global-detail', { model: 'tax_payment', id: p.id })" class="text-indigo-600 hover:text-indigo-900 transition-colors bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button @click="editPayment(p)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deletePayment(p)" class="text-red-650 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredPayments.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-percent text-4xl mb-3 block"></i> No se han registrado pagos fiscales.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredPayments.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredPayments.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredPayments.length"></span> registros
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
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-percent mr-1"></i> Registrar Carga Fiscal</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('tax-payments.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre / Concepto del Impuesto</label>
                        <input type="text" name="tax_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: IVA quincena Mayo">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Pagado</label>
                            <input type="number" step="0.01" name="amount" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Moneda del Gasto</label>
                            <select name="currency" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="USD">Dólares (USD)</option>
                                <option value="VES">Bolívares (VES)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Número de Referencia / Bancario</label>
                        <input type="text" name="reference_number" required class="w-full border border-gray-350 rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: SENIAT-100293">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Conciliación</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Registrar Impuesto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal (Alpine.js) -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Carga Fiscal</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre / Concepto del Impuesto</label>
                        <input type="text" x-model="currentPayment.tax_name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Pagado</label>
                            <input type="number" step="0.01" x-model="currentPayment.amount" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Moneda del Gasto</label>
                            <select x-model="currentPayment.currency" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="USD">Dólares (USD)</option>
                                <option value="VES">Bolívares (VES)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Número de Referencia / Bancario</label>
                        <input type="text" x-model="currentPayment.reference_number" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Conciliación</label>
                        <input type="date" x-model="currentPayment.payment_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar Impuesto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('taxPaymentManager', () => ({
        openCreateModal: false,
        openEditModal: false,
        currentPayment: { id: '', tax_name: '', amount: '', currency: 'USD', reference_number: '', payment_date: '' },
        payments: @json($payments),
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'payment_date',
        sortDirection: 'desc',
        
        get filteredPayments() {
            let result = this.payments || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(p => 
                    (p.tax_name && p.tax_name.toLowerCase().includes(query)) ||
                    (p.reference_number && p.reference_number.toLowerCase().includes(query)) ||
                    (p.currency && p.currency.toLowerCase().includes(query))
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
        
        get pagedPayments() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredPayments.slice(start, start + this.perPage);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredPayments.length / this.perPage) || 1;
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

        editPayment(p) {
            this.currentPayment = { ...p };
            if (p.payment_date) {
                this.currentPayment.payment_date = p.payment_date.split('T')[0];
            }
            this.openEditModal = true;
        },

        submitEditForm() {
            fetch(`/tax-payments/${this.currentPayment.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentPayment)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar el pago.');
                }
                const idx = this.payments.findIndex(item => item.id === this.currentPayment.id);
                if (idx !== -1) {
                    this.payments[idx] = data.payment;
                }
                this.openEditModal = false;
                window.Toast.fire({
                    icon: 'success',
                    title: 'Impuesto actualizado con éxito'
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

        deletePayment(p) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar el impuesto "${p.tax_name}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005293',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/tax-payments/${p.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar el impuesto.');
                        }
                        this.payments = this.payments.filter(item => item.id !== p.id);
                        window.Toast.fire({
                            icon: 'success',
                            title: 'Impuesto eliminado con éxito'
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
