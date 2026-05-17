@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ openExpenseModal: false, openCategoryModal: false }">
    
    <!-- Expenses Log (List) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 lg:col-span-2">
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

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Moneda</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-600">
                                {{ $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs font-bold text-gray-700">
                                {{ $expense->category->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-gray-550 max-w-xs truncate">{{ $expense->description }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs font-medium text-gray-550">{{ $expense->currency }}</td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs text-right font-black text-red-500">
                                {{ $expense->currency === 'USD' ? '$' : 'Bs.' }} {{ number_format($expense->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-xs text-gray-400">
                                <i class="fa-solid fa-wallet text-3xl mb-2 block"></i> No se han registrado egresos contables.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</div>
@endsection
