@extends('layouts.app')

@section('content')
<div x-data="{ openCreateModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 font-sans">Divisas y Tasas de Cambio</h1>
            <p class="text-sm text-gray-500">Historial diario del tipo de cambio oficial del Banco Central (BCV)</p>
        </div>
        <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all">
            <i class="fa-solid fa-plus"></i> Nueva Tasa
        </button>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-sm flex items-center justify-between">
            <span class="text-sm"><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
        </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden max-w-2xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de la Tasa</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tasa de Cambio Oficial</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($rates as $rate)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $rate->date ? $rate->date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-black text-brand-blue">
                                Bs. {{ number_format($rate->rate, 4) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-money-bill-transfer text-4xl mb-3 block"></i> No hay tasas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal (Alpine.js) -->
    <div x-show="openCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-money-bill-transfer mr-1"></i> Añadir Tipo de Cambio</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('exchange-rates.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto en BS por Dólar</label>
                        <input type="number" step="0.0001" name="rate" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: 36.5420">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha de la Tasa</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Guardar Tasa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
