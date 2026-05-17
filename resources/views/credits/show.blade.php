@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ openPaymentModal: false }">
    <!-- Credit Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 lg:col-span-1 h-fit">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Expediente de Crédito</h2>
        
        <div class="space-y-4">
            <div>
                <p class="text-xs uppercase text-gray-400 font-semibold mb-0.5">Cliente</p>
                <p class="text-base font-bold text-gray-800">{{ $credit->sale->client_name ?? '-' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs uppercase text-gray-400 font-semibold mb-0.5">Venta Origen</p>
                    <p class="text-sm font-semibold text-gray-800">#{{ $credit->sale_id }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase text-gray-400 font-semibold mb-0.5">Fecha Vencimiento</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $credit->due_date ? $credit->due_date->format('d/m/Y') : '-' }}</p>
                </div>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-400 font-semibold mb-0.5">Estatus Cuenta</p>
                @if($credit->status === 'paid')
                    <span class="inline-block px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">
                        Totalmente Solventado
                    </span>
                @else
                    <span class="inline-block px-2.5 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full">
                        Pendiente de Cobro
                    </span>
                @endif
            </div>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-500 font-medium">Deuda Original:</span>
                    <span class="text-sm font-bold text-gray-700">${{ number_format($credit->total_debt, 2) }}</span>
                </div>
                <div class="flex items-center justify-between border-t border-gray-200/60 pt-2">
                    <span class="text-sm text-gray-800 font-bold">Saldo Pendiente:</span>
                    <span class="text-lg font-black text-red-500">${{ number_format($credit->balance_due, 2) }}</span>
                </div>
            </div>

            @if($credit->balance_due > 0)
                <button @click="openPaymentModal = true" class="w-full mt-4 bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 shadow transition-all">
                    <i class="fa-solid fa-cash-register"></i> Registrar Abono
                </button>
            @endif
        </div>
    </div>

    <!-- Payments Ledger Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 p-6 lg:col-span-2">
        <h2 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Libro de Abonos Recibidos</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <span class="text-sm"><i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-150">
                <thead class="bg-brand-blue text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha del Abono</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Método de Pago</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider">Monto Abonado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($credit->payments ?? [] as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $payment->payment_method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">
                                ${{ number_format($payment->amount_paid, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-receipt text-3xl mb-3 block"></i> No se han registrado abonos a esta deuda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alpine.js Payment Registration Modal -->
    <div x-show="openPaymentModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openPaymentModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-receipt mr-1"></i> Registrar Abono a Crédito</h3>
                <button @click="openPaymentModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('credits.payments.store', $credit->id) }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto a Abonar (USD)</label>
                        <input type="number" step="0.01" max="{{ $credit->balance_due }}" name="amount_paid" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-bold text-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        <span class="text-xs text-gray-400 mt-1 block">Abono máximo autorizado: ${{ number_format($credit->balance_due, 2) }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Método de Pago</label>
                        <input type="text" name="payment_method" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Zelle, Efectivo, Transferencia Banco Venezuela">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha del Movimiento</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openPaymentModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Continuar y Persistir</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
