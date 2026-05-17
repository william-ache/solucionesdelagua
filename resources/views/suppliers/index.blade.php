@extends('layouts.app')

@section('content')
<div x-data="supplierManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">Gestión de Proveedores</h1>
                <p class="text-sm text-gray-500 mt-1">Expedientes de proveedores y saldo de cuenta por pagar</p>
            </div>
            <button @click="resetForm(); openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all self-start sm:self-auto flex-shrink-0">
                <i class="fa-solid fa-truck-fast"></i> Nuevo Proveedor
            </button>
        </div>

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
                        <th @click="sort('name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Empresa / Razón Social
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('document_id')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                RIF / Identificación
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'document_id' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Teléfono</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Correo Electrónico</th>
                        <th @click="sort('balance')" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center justify-end gap-1.5">
                                Saldo Deudor
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'balance' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="supplier in pagedSuppliers" :key="supplier.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800" x-text="supplier.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 font-mono" x-text="supplier.document_id"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550" x-text="supplier.phone || '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550" x-text="supplier.email || '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-right">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border inline-block min-w-[70px] text-center"
                                      :class="parseFloat(supplier.balance) > 0 ? 'bg-red-50 text-red-700 border-red-200/60' : 'bg-green-50 text-green-700 border-green-200/60'"
                                      x-text="'$' + parseFloat(supplier.balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openCurrentAccount(supplier)" class="text-orange-600 hover:text-orange-900 transition-colors bg-orange-50 hover:bg-orange-100 p-1.5 rounded-lg" title="Ver Cuenta Corriente">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </button>
                                    <button @click="editSupplier(supplier)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteSupplier(supplier)" class="text-red-650 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredSuppliers.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-truck-fast text-4xl mb-3 block"></i> No se encontraron proveedores registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredSuppliers.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredSuppliers.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredSuppliers.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-305 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Create Modal (Alpine.js) -->
    <div x-show="openCreateModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[80] flex items-start justify-center p-4 pt-24 pb-6 overflow-y-auto" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all my-2" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-truck-fast mr-1"></i> Registrar Proveedor</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitCreateForm" class="p-6">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre o Razón Social</label>
                        <input type="text" x-model="currentSupplier.name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.name ? errors.name[0] : ''" x-show="errors.name"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Identificación (RIF / Cédula)</label>
                        <input type="text" x-model="currentSupplier.document_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: J-12345678-9">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.document_id ? errors.document_id[0] : ''" x-show="errors.document_id"></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono de Contacto</label>
                            <input type="text" x-model="currentSupplier.phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                            <span class="text-red-550 text-xs mt-1" x-text="errors.phone ? errors.phone[0] : ''" x-show="errors.phone"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" x-model="currentSupplier.email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                            <span class="text-red-550 text-xs mt-1" x-text="errors.email ? errors.email[0] : ''" x-show="errors.email"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal (Alpine.js) -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[80] flex items-start justify-center p-4 pt-24 pb-6 overflow-y-auto" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all my-2" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-lg font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Proveedor</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre o Razón Social</label>
                        <input type="text" x-model="currentSupplier.name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Identificación (RIF / Cédula)</label>
                        <input type="text" x-model="currentSupplier.document_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono de Contacto</label>
                            <input type="text" x-model="currentSupplier.phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" x-model="currentSupplier.email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cuenta Corriente Modal -->
    <div x-show="openAccountModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[80] flex items-start justify-center p-4 pt-24 pb-6 overflow-y-auto" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all my-2" @click.away="openAccountModal = false">
            <div class="bg-orange-600 p-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold"><i class="fa-solid fa-file-invoice-dollar mr-1"></i> Cuenta por Pagar al Proveedor</h3>
                    <p class="text-xs text-orange-100 mt-0.5" x-text="currentSupplier.name + ' - RIF: ' + currentSupplier.document_id"></p>
                </div>
                <button @click="openAccountModal = false" class="text-white hover:text-orange-100 text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-700 uppercase tracking-wider text-xs"><i class="fa-solid fa-list mr-1"></i> Historial de Movimientos</h4>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 font-semibold uppercase block">Monto Adeudado</span>
                            <span class="text-xl font-extrabold font-mono"
                                  :class="parseFloat(currentSupplier.balance) > 0 ? 'text-red-500' : 'text-emerald-600'"
                                  x-text="'$' + parseFloat(currentSupplier.balance || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                            </span>
                        </div>
                    </div>

                    <div class="border border-gray-150 rounded-lg overflow-hidden bg-gray-50 flex-1">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-150 text-left">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="px-4 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-4 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Detalle</th>
                                        <th class="px-4 py-2.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-4 py-2.5 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="t in transactions" :key="t.id">
                                        <tr class="bg-white hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-2.5 whitespace-nowrap text-xs font-semibold text-gray-500" x-text="new Date(t.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })"></td>
                                            <td class="px-4 py-2.5 text-xs text-gray-700" x-text="t.description"></td>
                                            <td class="px-4 py-2.5 whitespace-nowrap">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
                                                      :class="{
                                                          'bg-red-50 text-red-700': t.type === 'invoice',
                                                          'bg-blue-50 text-blue-600': t.type === 'credit_note',
                                                          'bg-emerald-50 text-emerald-700': t.type === 'payment'
                                                      }"
                                                      x-text="t.type === 'invoice' ? 'Factura/Deuda' : (t.type === 'credit_note' ? 'N. Crédito' : 'Pago')">
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 whitespace-nowrap text-xs text-right font-bold font-mono"
                                                :class="t.type === 'invoice' ? 'text-red-500' : 'text-emerald-600'"
                                                x-text="(t.type === 'invoice' ? '+' : '-') + '$' + parseFloat(t.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="transactions.length === 0">
                                        <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-400">
                                            No hay movimientos de cuenta por pagar.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-150 rounded-xl p-5 flex flex-col gap-4 self-start">
                    <h4 class="font-bold text-gray-700 uppercase tracking-wider text-xs border-b border-gray-150 pb-2"><i class="fa-solid fa-money-bill-transfer text-orange-600"></i> Nuevo Movimiento</h4>
                    <form @submit.prevent="submitTransactionForm" class="flex flex-col gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
                            <select x-model="newTransaction.type" required class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs bg-white">
                                <option value="invoice">Factura de Compra (Sube Deuda)</option>
                                <option value="payment">Abono / Pago (Baja Deuda)</option>
                                <option value="credit_note">Nota de Crédito (Baja Deuda)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Monto ($ USD)</label>
                            <input type="number" step="0.01" min="0" x-model="newTransaction.amount" required class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs font-mono" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción</label>
                            <textarea x-model="newTransaction.description" required rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-xs"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white text-xs px-4 py-2.5 rounded-lg font-bold shadow-sm transition-all flex items-center justify-center gap-1.5 mt-2">
                            <i class="fa-solid fa-plus-circle"></i> Aplicar Movimiento
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('supplierManager', () => ({
        suppliers: @json($suppliers),
        openCreateModal: false,
        openEditModal: false,
        openAccountModal: false,
        transactions: [],
        newTransaction: { type: 'invoice', amount: '', description: '' },
        currentSupplier: { id: '', name: '', document_id: '', phone: '', email: '', balance: 0 },
        errors: {},
        searchQuery: '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'name',
        sortDirection: 'asc',

        get filteredSuppliers() {
            let result = this.suppliers || [];
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase().trim();
                result = result.filter(s => 
                    (s.name && s.name.toLowerCase().includes(q)) || 
                    (s.document_id && s.document_id.toLowerCase().includes(q))
                );
            }
            result.sort((a, b) => {
                let valA = a[this.sortColumn] || '';
                let valB = b[this.sortColumn] || '';
                if(typeof valA === 'string') valA = valA.toLowerCase();
                if(typeof valB === 'string') valB = valB.toLowerCase();
                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
            return result;
        },
        get pagedSuppliers() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredSuppliers.slice(start, start + this.perPage);
        },
        get totalPages() { return Math.ceil(this.filteredSuppliers.length / this.perPage) || 1; },
        sort(col) {
            if (this.sortColumn === col) this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            else { this.sortColumn = col; this.sortDirection = 'asc'; }
            this.currentPage = 1;
        },
        resetForm() {
            this.currentSupplier = { id: '', name: '', document_id: '', phone: '', email: '', balance: 0 };
            this.errors = {};
        },
        async submitCreateForm() {
            try {
                const res = await fetch('{{ route('suppliers.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.currentSupplier)
                });
                const data = await res.json();
                if (res.ok) {
                    this.suppliers.push(data.supplier);
                    this.openCreateModal = false;
                    Swal.fire({icon: 'success', title: 'Proveedor creado', showConfirmButton: false, timer: 1500});
                } else if (res.status === 422) { this.errors = data.errors; }
            } catch (e) {
                Swal.fire('Error', 'Fallo de red', 'error');
            }
        },
        editSupplier(s) {
            this.errors = {};
            this.currentSupplier = { ...s };
            this.openEditModal = true;
        },
        async submitEditForm() {
            try {
                const res = await fetch(`/suppliers/${this.currentSupplier.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.currentSupplier)
                });
                const data = await res.json();
                if (res.ok) {
                    const idx = this.suppliers.findIndex(s => s.id === this.currentSupplier.id);
                    if (idx !== -1) this.suppliers[idx] = data.supplier;
                    this.openEditModal = false;
                    Swal.fire({icon: 'success', title: 'Actualizado', showConfirmButton: false, timer: 1500});
                }
            } catch (e) {}
        },
        deleteSupplier(s) {
            Swal.fire({
                title: '¿Seguro?', text: 'Se eliminará a ' + s.name, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Sí, eliminar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const res = await fetch(`/suppliers/${s.id}`, {
                        method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    if (res.ok) {
                        this.suppliers = this.suppliers.filter(i => i.id !== s.id);
                        Swal.fire('Eliminado', '', 'success');
                    } else Swal.fire('Error', 'No se ha podido eliminar, verifique si tiene historiales.', 'error');
                }
            });
        },
        async openCurrentAccount(s) {
            this.currentSupplier = { ...s };
            this.transactions = [];
            this.openAccountModal = true;
            const res = await fetch(`/suppliers/${s.id}/transactions`);
            const data = await res.json();
            if (res.ok) this.transactions = data.transactions;
        },
        async submitTransactionForm() {
            try {
                const res = await fetch(`/suppliers/${this.currentSupplier.id}/transactions`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.newTransaction)
                });
                const data = await res.json();
                if (res.ok) {
                    this.transactions.unshift(data.transaction);
                    this.currentSupplier.balance = data.new_balance;
                    const idx = this.suppliers.findIndex(c => c.id === this.currentSupplier.id);
                    if (idx !== -1) this.suppliers[idx].balance = data.new_balance;
                    this.newTransaction = { type: 'invoice', amount: '', description: '' };
                    Swal.fire({icon: 'success', title: 'Movimiento Registrado', showConfirmButton: false, timer: 1500});
                }
            } catch (e) {
                Swal.fire('Error', 'Hubo un error registrando el movimiento', 'error');
            }
        }
    }));
});
</script>
@endpush
