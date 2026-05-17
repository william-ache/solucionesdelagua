@extends('layouts.app')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 font-sans">Créditos y Cuentas por Cobrar</h1>
        <p class="text-sm text-gray-500">Listado de saldos pendientes de ventas a crédito</p>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Venta ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Límite / Fecha Vence</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Monto Deuda</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Balance Pendiente</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estatus</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($credits as $credit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">#{{ $credit->sale_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ $credit->sale->client_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $credit->due_date ? $credit->due_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                ${{ number_format($credit->total_debt, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-500">
                                ${{ number_format($credit->balance_due, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($credit->status === 'paid')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">
                                        Total Solventado
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <a href="{{ route('credits.show', $credit->id) }}" class="inline-flex items-center gap-1.5 bg-brand-light hover:bg-brand-blue text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all">
                                    <i class="fa-solid fa-receipt"></i> Ver Detalle / Abonar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-hand-holding-dollar text-4xl mb-3 block"></i> No se encontraron créditos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
