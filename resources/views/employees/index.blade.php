@extends('layouts.app')

@section('content')
<div x-data="employeeManager">
    <!-- Unified White Card Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header (Title, description, and button) inside white card -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">Nómina y Gestión de Personal</h1>
                <p class="text-sm text-gray-500 mt-1">Expedientes de nómina de colaboradores y registro de salarios</p>
            </div>
            <button @click="resetForm(); openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all self-start sm:self-auto flex-shrink-0">
                <i class="fa-solid fa-user-plus"></i> Registrar Colaborador
            </button>
        </div>

        <!-- Datatable Controls -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Entries Selector -->
            <div class="flex items-center gap-2 text-sm text-gray-550">
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
                        <th @click="sort('name')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Nombre del Colaborador
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('identification_number')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Cédula / Documento
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'identification_number' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('base_salary_usd')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Salario Base (USD)
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'base_salary_usd' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th @click="sort('status')" class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer select-none hover:bg-blue-800 transition-colors">
                            <div class="flex items-center gap-1.5">
                                Estado
                                <i class="fa-solid text-[10px]" :class="sortColumn === 'status' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Abonos Históricos</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="employee in pagedEmployees" :key="employee.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800" x-text="employee.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 font-mono" x-text="employee.identification_number"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium"
                                x-text="'$' + parseFloat(employee.base_salary_usd || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                      :class="employee.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                      x-text="employee.status === 'active' ? 'Activo' : 'Inactivo'">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550">
                                <span class="bg-gray-100 text-gray-650 px-2 py-0.5 rounded text-xs font-bold font-mono"
                                      x-text="(employee.payroll_payments ? employee.payroll_payments.length : 0) + ' pagos'">
                                </span>
                                <span class="text-xs text-gray-400 font-semibold block mt-0.5"
                                      x-text="'Total: $' + parseFloat(employee.payroll_payments ? employee.payroll_payments.reduce((sum, p) => sum + parseFloat(p.amount_paid), 0) : 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="$dispatch('open-global-detail', { model: 'employee', id: employee.id })" class="text-indigo-600 hover:text-indigo-900 transition-colors bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg" title="Ver Detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <template x-if="employee.status === 'active'">
                                        <button @click="openPaySlip(employee)" class="inline-flex items-center gap-1 bg-brand-light hover:bg-brand-blue text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all" title="Pagar Nómina">
                                            <i class="fa-solid fa-wallet"></i> Pagar
                                        </button>
                                    </template>
                                    <template x-if="employee.status !== 'active'">
                                        <span class="text-xs text-gray-400 font-bold italic mr-2">Desincorporado</span>
                                    </template>
                                    <button @click="editEmployee(employee)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar Ficha">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button @click="deleteEmployee(employee)" class="text-red-650 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar Colaborador">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredEmployees.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-user-tie text-4xl mb-3 block"></i> No se encontraron colaboradores registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredEmployees.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredEmployees.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredEmployees.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-330 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Create Employee Modal -->
    <div x-show="openCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-user-plus mr-1"></i> Registrar Colaborador</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitCreateForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" x-model="currentEmployee.name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.name ? errors.name[0] : ''" x-show="errors.name"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cédula de Identidad</label>
                        <input type="text" x-model="currentEmployee.identification_number" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: V-12345678">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.identification_number ? errors.identification_number[0] : ''" x-show="errors.identification_number"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Salario Base (USD)</label>
                            <input type="number" step="0.01" x-model="currentEmployee.base_salary_usd" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: 500.00">
                            <span class="text-red-550 text-xs mt-1" x-text="errors.base_salary_usd ? errors.base_salary_usd[0] : ''" x-show="errors.base_salary_usd"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                            <select x-model="currentEmployee.status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                            <span class="text-red-555 text-xs mt-1" x-text="errors.status ? errors.status[0] : ''" x-show="errors.status"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Guardar Ficha</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Ficha de Colaborador</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" x-model="currentEmployee.name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.name ? errors.name[0] : ''" x-show="errors.name"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cédula de Identidad</label>
                        <input type="text" x-model="currentEmployee.identification_number" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: V-12345678">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.identification_number ? errors.identification_number[0] : ''" x-show="errors.identification_number"></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Salario Base (USD)</label>
                            <input type="number" step="0.01" x-model="currentEmployee.base_salary_usd" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: 500.00">
                            <span class="text-red-550 text-xs mt-1" x-text="errors.base_salary_usd ? errors.base_salary_usd[0] : ''" x-show="errors.base_salary_usd"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                            <select x-model="currentEmployee.status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                            <span class="text-red-555 text-xs mt-1" x-text="errors.status ? errors.status[0] : ''" x-show="errors.status"></span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar Ficha</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reg Pago de Nómina Modal -->
    <div x-show="openPayModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all" @click.away="openPayModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-cash-register mr-1"></i> Desembolsar Nómina</h3>
                <button @click="openPayModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitPayForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Colaborador Destinatario</p>
                        <p class="text-sm font-bold text-gray-800" x-text="selectedEmployeeName"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Concepto del Pago</label>
                        <input type="text" x-model="paymentData.concept" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Primera Quincena Mayo">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.concept ? errors.concept[0] : ''" x-show="errors.concept"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Pagado (USD)</label>
                        <input type="number" step="0.01" x-model="paymentData.amount_paid" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.amount_paid ? errors.amount_paid[0] : ''" x-show="errors.amount_paid"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Emisión</label>
                        <input type="date" x-model="paymentData.payment_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-red-550 text-xs mt-1" x-text="errors.payment_date ? errors.payment_date[0] : ''" x-show="errors.payment_date"></span>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openPayModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Emitir Transacción</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('employeeManager', () => ({
        employees: @json($employees),
        openCreateModal: false,
        openEditModal: false,
        openPayModal: false,
        selectedEmployeeId: null,
        selectedEmployeeName: '',
        errors: {},
        currentEmployee: { id: '', name: '', identification_number: '', base_salary_usd: '', status: 'active' },
        paymentData: { employee_id: '', concept: '', amount_paid: '', payment_date: '{{ date('Y-m-d') }}' },
        
        // Datatable states
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'name',
        sortDirection: 'asc',

        get filteredEmployees() {
            let result = this.employees || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(e => 
                    (e.name && e.name.toLowerCase().includes(query)) ||
                    (e.identification_number && e.identification_number.toLowerCase().includes(query)) ||
                    (e.status && e.status.toLowerCase().includes(query))
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

        get pagedEmployees() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredEmployees.slice(start, start + this.perPage);
        },

        get totalPages() {
            return Math.ceil(this.filteredEmployees.length / this.perPage) || 1;
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
            this.currentEmployee = { id: '', name: '', identification_number: '', base_salary_usd: '', status: 'active' };
            this.errors = {};
        },
        resetPaymentForm() {
            this.paymentData = { employee_id: '', concept: '', amount_paid: '', payment_date: '{{ date('Y-m-d') }}' };
            this.errors = {};
        },
        submitCreateForm() {
            this.errors = {};
            fetch('{{ route('employees.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentEmployee)
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
                    this.employees.push(data.employee);
                    this.employees.sort((a, b) => a.name.localeCompare(b.name));
                    this.openCreateModal = false;
                    this.resetForm();
                    window.Toast.fire({
                        icon: 'success',
                        title: 'Colaborador guardado con éxito'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al guardar el colaborador.',
                    confirmButtonColor: '#005293'
                });
            });
        },
        editEmployee(employee) {
            this.errors = {};
            this.currentEmployee = { ...employee };
            this.openEditModal = true;
        },
        submitEditForm() {
            this.errors = {};
            fetch(`/employees/${this.currentEmployee.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentEmployee)
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
                    const idx = this.employees.findIndex(e => e.id === this.currentEmployee.id);
                    if (idx !== -1) {
                        this.employees[idx] = data.employee;
                    }
                    this.employees.sort((a, b) => a.name.localeCompare(b.name));
                    this.openEditModal = false;
                    this.resetForm();
                    window.Toast.fire({
                        icon: 'success',
                        title: 'Ficha actualizada con éxito'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al actualizar la ficha.',
                    confirmButtonColor: '#005293'
                });
            });
        },
        deleteEmployee(employee) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a retirar y eliminar la ficha de "${employee.name}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005293',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/employees/${employee.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar el colaborador.');
                        }
                        this.employees = this.employees.filter(e => e.id !== employee.id);
                        window.Toast.fire({
                            icon: 'success',
                            title: 'Ficha eliminada con éxito'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Ocurrió un error al eliminar el colaborador.',
                            confirmButtonColor: '#005293'
                        });
                    });
                }
            });
        },
        openPaySlip(employee) {
            this.resetPaymentForm();
            this.selectedEmployeeId = employee.id;
            this.selectedEmployeeName = employee.name;
            this.paymentData.employee_id = employee.id;
            this.paymentData.amount_paid = employee.base_salary_usd;
            this.openPayModal = true;
        },
        submitPayForm() {
            this.errors = {};
            fetch('{{ route('payroll-payments.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.paymentData)
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
                    const idx = this.employees.findIndex(e => e.id == this.selectedEmployeeId);
                    if (idx !== -1) {
                        if (!this.employees[idx].payroll_payments) {
                            this.employees[idx].payroll_payments = [];
                        }
                        this.employees[idx].payroll_payments.push(data.payment);
                    }
                    this.openPayModal = false;
                    this.resetPaymentForm();
                    window.Toast.fire({
                        icon: 'success',
                        title: 'Pago de nómina realizado con éxito'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Ocurrió un error al registrar el pago.',
                    confirmButtonColor: '#005293'
                });
            });
        }
    }));
});
</script>
@endpush
