@extends('layouts.app')

@section('content')
<div x-data="{ openCreateModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 font-sans">Impuestos y Contribuciones Fiscales</h1>
            <p class="text-sm text-gray-500">Historial de registros de pagos del IVA, ISLR y tasas municipales (SENIAT)</p>
        </div>
        <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all">
            <i class="fa-solid fa-plus"></i> Registrar Carga Fiscal
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de Pago</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Concepto de Alícuota</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider font-mono">Número Referencia</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Moneda</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider font-bold">Monto Abonado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $payment->tax_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550 font-mono">{{ $payment->reference_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">{{ $payment->currency }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-500">
                                {{ $payment->currency === 'USD' ? '$' : 'Bs.' }} {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-percent text-4xl mb-3 block"></i> No se han registrado pagos fiscales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</div>
@endsection
