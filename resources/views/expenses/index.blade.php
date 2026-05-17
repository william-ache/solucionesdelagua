@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="expenseManager">
    
    <!-- Expenses Log (List) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 lg:col-span-2 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Gastos Operativos</h1>
                    <p class="text-xs text-gray-500">Historial general de pagos y compras logísticas</p>
                </div>
                <button @click="openExpenseModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-1.5 px-3 rounded-lg text-xs flex items-center gap-1.5 shadow transition-all">
                    <i class="fa-solid fa-plus font-bold"></i> Cargar Gasto
                </button>
            </div>

            @if(session('success') && !str_contains(session('success'), 'Categoría'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2.5 rounded-lg mb-4 flex items-center">
                    <span class="text-xs font-semibold"><i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}</span>
                </div>
            @endif

            <!-- Datatable Controls -->
            <div class="p-3 mb-3 bg-gray-50 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 text-gray-500">
                    <span>Mostrar</span>
                    <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-1 focus:ring-brand-light bg-white font-medium text-gray-700">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>registros</span>
                </div>
                <!-- Search & Actions -->
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                    <!-- Export Actions -->
                    <div class="flex items-center gap-1.5 justify-end w-full sm:w-auto">
                        <button @click="window.exportToExcel(filteredExpenses, ['expense_date', 'category.name', 'description', 'currency', 'amount'], ['Fecha', 'Categoría', 'Descripción', 'Moneda', 'Monto'], 'Gastos_SolucionesDelAgua')"
                                type="button"
                                class="flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-emerald-50 text-emerald-700 hover:text-emerald-800 border border-gray-300 hover:border-emerald-300 rounded-md text-[11px] font-bold transition-all shadow-sm select-none"
                                title="Exportar registros filtrados a Excel (CSV)">
                            <i class="fa-solid fa-file-excel text-emerald-600 text-xs"></i> Excel
                        </button>
                        <button @click="window.exportToPDF(filteredExpenses, ['expense_date', 'category.name', 'description', 'currency', 'amount'], ['Fecha', 'Categoría', 'Descripción', 'Moneda', 'Monto'], 'Reporte de Gastos Operativos')"
                                type="button"
                                class="flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-red-50 text-red-650 hover:text-red-700 border border-gray-300 hover:border-red-300 rounded-md text-[11px] font-bold transition-all shadow-sm select-none"
                                title="Generar Reporte PDF para imprimir">
                            <i class="fa-solid fa-file-pdf text-red-500 text-xs"></i> PDF
                        </button>
                    </div>

                    <!-- Search Box -->
                    <div class="relative w-full sm:w-56 md:w-60">
                        <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar..." class="w-full pl-8 pr-3 py-1 border border-gray-300 rounded-md text-xs focus:outline-none focus:ring-1 focus:ring-brand-light bg-white text-gray-700">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-150">
                    <thead class="bg-brand-blue text-white">
                        <tr>
                            <th @click="sort('expense_date')" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider cursor-pointer select-none hover:bg-blue-800 transition-colors">
                                <div class="flex items-center gap-1">
                                    Fecha
                                    <i class="fa-solid text-[9px]" :class="sortColumn === 'expense_date' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                                </div>
                            </th>
                            <th @click="sort('category_name')" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider cursor-pointer select-none hover:bg-blue-800 transition-colors">
                                <div class="flex items-center gap-1">
                                    Categoría
                                    <i class="fa-solid text-[9px]" :class="sortColumn === 'category_name' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                                </div>
                            </th>
                            <th @click="sort('description')" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider cursor-pointer select-none hover:bg-blue-800 transition-colors">
                                <div class="flex items-center gap-1">
                                    Descripción
                                    <i class="fa-solid text-[9px]" :class="sortColumn === 'description' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                                </div>
                            </th>
                            <th @click="sort('currency')" class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider cursor-pointer select-none hover:bg-blue-800 transition-colors">
                                <div class="flex items-center gap-1">
                                    Moneda
                                    <i class="fa-solid text-[9px]" :class="sortColumn === 'currency' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                                </div>
                            </th>
                            <th @click="sort('amount')" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider cursor-pointer select-none hover:bg-blue-800 transition-colors">
                                <div class="flex items-center justify-end gap-1">
                                    Monto
                                    <i class="fa-solid text-[9px]" :class="sortColumn === 'amount' ? (sortDirection === 'asc' ? 'fa-sort-up text-white' : 'fa-sort-down text-white') : 'fa-sort text-blue-250/30'"></i>
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <template x-for="expense in pagedExpenses" :key="expense.id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600" x-text="expense.expense_date ? new Date(expense.expense_date).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' }) : '-'"></td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-gray-700" x-text="expense.category ? expense.category.name : '-'"></td>
                                <td class="px-4 py-3 text-xs text-gray-550 max-w-xs truncate" x-text="expense.description || '-'"></td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-1.5 rounded-md text-[10px] font-bold border inline-flex items-center gap-1.5"
                                          :class="expense.currency === 'USD' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-sky-50 text-sky-700 border-sky-200/60'">
                                        <i class="fa-solid" :class="expense.currency === 'USD' ? 'fa-dollar-sign' : 'fa-coins'"></i>
                                        <span x-text="expense.currency"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right font-mono">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border inline-block min-w-[70px] text-center"
                                          :class="expense.currency === 'USD' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60' : 'bg-sky-50 text-sky-700 border-sky-200/60'"
                                          x-text="(expense.currency === 'USD' ? '$' : 'Bs.') + parseFloat(expense.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-center font-semibold">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="$dispatch('open-global-detail', { model: 'expense', id: expense.id })" class="text-indigo-600 hover:text-indigo-900 transition-colors bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg" title="Ver Detalles">
                                            <i class="fa-solid fa-eye text-[11px]"></i>
                                        </button>
                                        <button @click="editExpense(expense)" class="text-blue-600 hover:text-blue-900 transition-colors bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg" title="Editar">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                        </button>
                                        <button @click="deleteExpense(expense)" class="text-red-655 hover:text-red-900 transition-colors bg-red-50 hover:bg-red-100 p-1.5 rounded-lg" title="Eliminar">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredExpenses.length === 0">
                            <td colspan="6" class="px-4 py-10 text-center text-xs text-gray-400">
                                <i class="fa-solid fa-wallet text-3xl mb-2 block"></i> No se han registrado egresos contables.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Datatable Pagination Footer -->
        <div class="mt-4 pt-3 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
            <div>
                Mostrando <span class="font-bold text-gray-700" x-text="filteredExpenses.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredExpenses.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredExpenses.length"></span> registros
            </div>
            
            <div class="flex items-center gap-1" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-6 h-6 rounded text-xs font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-2 py-1 border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Categories Ledger List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 lg:col-span-1 h-fit">
        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Rubros / Categorías</h2>
            <button @click="openCategoryModal = true" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-1 px-2.5 rounded text-xs flex items-center gap-1 transition-all">
                <i class="fa-solid fa-folder-plus"></i> Nueva
            </button>
        </div>

        @if(session('success') && str_contains(session('success'), 'Categoría'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-4 flex items-center">
                <span class="text-[11px] font-semibold"><i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}</span>
            </div>
        @endif

        <ul class="space-y-2 max-h-96 overflow-y-auto pr-1">
            @forelse($categories as $category)
                <li class="flex items-center justify-between bg-gray-50 rounded px-3 py-2 text-xs border border-gray-100 text-gray-750 hover:bg-gray-100 transition-colors">
                    <span class="font-semibold">{{ $category->name }}</span>
                    <span class="bg-white px-2 py-0.5 text-[9px] font-bold text-gray-400 rounded-full border border-gray-150">
                        {{ $category->expenses->count() }} operaciones
                    </span>
                </li>
            @empty
                <li class="text-center text-xs text-gray-400 py-6">No hay rubros de egresos activos.</li>
            @endforelse
        </ul>
    </div>

    <!-- Create Expense modal (Alpine.js) -->
    <div x-show="openExpenseModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openExpenseModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-wallet mr-1"></i> Cargar Gasto Operativo</h3>
                <button @click="openExpenseModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('expenses.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Rubro / Categoría</label>
                        <select name="expense_category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-brand-light">
                            <option value="">Seleccione una categoría...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción corta</label>
                        <input type="text" name="description" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Pago de Internet de Mayo">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Monto del Egresado</label>
                            <input type="number" step="0.01" name="amount" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Moneda</label>
                            <select name="currency" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="USD">Dólares (USD)</option>
                                <option value="VES">Bolívares (VES)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de la Facturación</label>
                        <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openExpenseModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Category modal (Alpine.js) -->
    <div x-show="openCategoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all" @click.away="openCategoryModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-folder-plus mr-1"></i> Adicionar nueva categoría</h3>
                <button @click="openCategoryModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('expense-categories.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la Categoría</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Equipos informáticos, Alquiler">
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-end gap-3">
                    <button type="button" @click="openCategoryModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Añadir Categoría</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Expense Modal (Alpine.js) -->
    <div x-show="openEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openEditModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-pen-to-square mr-1"></i> Editar Gasto Operativo</h3>
                <button @click="openEditModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form @submit.prevent="submitEditForm" class="p-6">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Rubro / Categoría</label>
                        <select x-model="currentExpense.expense_category_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                            <option value="">Selecciona rubro...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción corta</label>
                        <input type="text" x-model="currentExpense.description" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Pagado</label>
                            <input type="number" step="0.01" x-model="currentExpense.amount" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Moneda del Gasto</label>
                            <select x-model="currentExpense.currency" required class="w-full border border-gray-305 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="USD">Dólares (USD)</option>
                                <option value="VES">Bolívares (VES)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de Facturación</label>
                        <input type="date" x-model="currentExpense.expense_date" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openEditModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Actualizar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('expenseManager', () => ({
        openExpenseModal: false,
        openCategoryModal: false,
        openEditModal: false,
        currentExpense: { id: '', expense_category_id: '', description: '', amount: '', currency: 'USD', expense_date: '' },
        expenses: @json($expenses),
        searchQuery: new URLSearchParams(window.location.search).get('search') || '',
        perPage: 10,
        currentPage: 1,
        sortColumn: 'expense_date',
        sortDirection: 'desc',
        
        get filteredExpenses() {
            let result = this.expenses || [];
            if (this.searchQuery.trim() !== '') {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(e => 
                    (e.description && e.description.toLowerCase().includes(query)) ||
                    (e.category && e.category.name && e.category.name.toLowerCase().includes(query)) ||
                    (e.currency && e.currency.toLowerCase().includes(query))
                );
            }
            
            // Sort
            result.sort((a, b) => {
                let valA = '';
                let valB = '';
                
                if (this.sortColumn === 'category_name') {
                    valA = a.category ? (a.category.name || '') : '';
                    valB = b.category ? (b.category.name || '') : '';
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
        
        get pagedExpenses() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredExpenses.slice(start, start + this.perPage);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredExpenses.length / this.perPage) || 1;
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

        editExpense(expense) {
            this.currentExpense = { ...expense };
            if (expense.expense_date) {
                this.currentExpense.expense_date = expense.expense_date.split('T')[0];
            }
            this.openEditModal = true;
        },

        submitEditForm() {
            fetch(`/expenses/${this.currentExpense.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(this.currentExpense)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Error al actualizar el gasto.');
                }
                const idx = this.expenses.findIndex(e => e.id === this.currentExpense.id);
                if (idx !== -1) {
                    this.expenses[idx] = data.expense;
                }
                this.openEditModal = false;
                window.Toast.fire({
                    icon: 'success',
                    title: 'Gasto actualizado con éxito'
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

        deleteExpense(expense) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar el gasto "${expense.description}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#005293',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/expenses/${expense.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar el gasto.');
                        }
                        this.expenses = this.expenses.filter(e => e.id !== expense.id);
                        window.Toast.fire({
                            icon: 'success',
                            title: 'Gasto eliminado con éxito'
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
