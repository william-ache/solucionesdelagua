@extends('layouts.app')

@section('content')
<div x-data="{ openCreateModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 font-sans">Módulo de Ventas</h1>
            <p class="text-sm text-gray-500">Historial de facturación de equipos, químicos y proyectos</p>
        </div>
        <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all">
            <i class="fa-solid fa-cart-plus"></i> Nueva Venta
        </button>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center justify-between">
            <span class="text-sm"><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Moneda</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Facturado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Condición</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550">
                                {{ $sale->date ? $sale->date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $sale->client_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550">{{ $sale->currency }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                                {{ $sale->currency === 'USD' ? '$' : 'Bs.' }} {{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($sale->status === 'paid')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">
                                        Contado (Pagado)
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-700 rounded-full">
                                        Crédito Pendiente
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-cart-shopping text-4xl mb-3 block"></i> No se encontraron ventas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</div>
@endsection
