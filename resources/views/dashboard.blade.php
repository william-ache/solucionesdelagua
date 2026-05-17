@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Administrativo</h1>
    <p class="text-sm text-gray-500">Indicadores financieros en tiempo real - Soluciones del Agua</p>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Tarjeta Ventas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-brand-blue hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Ventas del Mes</p>
                <p class="text-3xl font-bold text-gray-850">${{ number_format($kpis['sales_month'] ?? 0, 2) }}</p>
                <span class="text-xs text-green-500 font-medium">
                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> Facturado en USD
                </span>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-brand-blue rounded-xl flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>
    
    <!-- Tarjeta Gastos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-red-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Gastos del Mes</p>
                <p class="text-3xl font-bold text-gray-850">${{ number_format($kpis['expenses_month'] ?? 0, 2) }}</p>
                <span class="text-xs text-red-500 font-medium">
                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> Gastos + Nómina
                </span>
            </div>
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>
    </div>
    
    <!-- Tarjeta Cuentas por Cobrar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 border-l-4 border-l-amber-500 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Cuentas por Cobrar</p>
                <p class="text-3xl font-bold text-gray-850">${{ number_format($kpis['accounts_receivable'] ?? 0, 2) }}</p>
                <span class="text-xs text-amber-500 font-medium">
                    <i class="fa-solid fa-circle text-[8px] mr-1"></i> Créditos pendientes
                </span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-2xl shadow-inner">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="grid grid-cols-1 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Comparativa Mensual</h2>
                <p class="text-xs text-gray-400">Ingresos vs Egresos del Año {{ date('Y') }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-brand-blue rounded"></span> Ingresos (Ventas)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-500 rounded"></span> Egresos (Gastos + Nómina)</span>
            </div>
        </div>
        <div class="relative h-96 w-full">
            <canvas id="financeChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        
        const chartData = @json($chartData);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Ingresos (Ventas USD)',
                        data: chartData.incomes,
                        backgroundColor: '#005293',
                        borderColor: '#005293',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: '#003a6a'
                    },
                    {
                        label: 'Egresos (Gastos USD)',
                        data: chartData.expenses,
                        backgroundColor: '#ef4444',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: '#dc2626'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': $' + context.parsed.y.toLocaleString('es-ES', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'sans-serif',
                                size: 11
                            },
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            font: {
                                family: 'sans-serif',
                                size: 11
                            },
                            color: '#9ca3af',
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
