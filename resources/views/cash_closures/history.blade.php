@extends('layouts.app')

@section('content')
<div x-data="cashClosureManager" class="max-w-7xl mx-auto pb-10">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">
                    <i class="fa-solid fa-cash-register text-brand-blue mr-2"></i> Cierre de Caja
                </h1>
                <p class="text-sm text-gray-500 mt-1">Gestión de flujo de caja, tasas de cambio y cierres de turno.</p>
            </div>
            <button @click="openModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all">
                <i class="fa-solid fa-door-open"></i> Aperturar Turno/Día
            </button>
        </div>

        <!-- Filter Controls -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center flex-col sm:flex-row gap-2 text-sm text-gray-600">
                    <label class="font-bold">Mes Histórico:</label>
                    <select x-model="selectedMonth" class="border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-brand-light bg-white font-medium text-gray-700 outline-none w-32">
                        <option value="1">Enero</option>
                        <option value="2">Febrero</option>
                        <option value="3">Marzo</option>
                        <option value="4">Abril</option>
                        <option value="5">Mayo</option>
                        <option value="6">Junio</option>
                        <option value="7">Julio</option>
                        <option value="8">Agosto</option>
                        <option value="9">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                    </select>
                </div>
                <div class="flex items-center flex-col sm:flex-row gap-2 text-sm text-gray-600">
                    <label class="font-bold">Año:</label>
                    <select x-model="selectedYear" class="border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-brand-light bg-white font-medium text-gray-700 outline-none w-24">
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                    </select>
                </div>
            </div>
            
            <div>
                <button @click="fetchHistory()" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-1.5 rounded-lg text-sm font-bold shadow-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Filtrar Rejilla
                </button>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-left divide-y divide-gray-150">
                <thead class="bg-brand-blue text-white">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider border-b border-blue-800">Fecha</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider border-b border-blue-800">Estatus</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800">T. BCV</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800">T. USDT</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800">Ventas $</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800">Diferencia</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-sm text-gray-700 bg-white">
                    <template x-for="closure in closures" :key="closure.id">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800" x-text="new Date(closure.date).toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric', timeZone: 'UTC' })"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                      :class="closure.status === 'open' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'"
                                      x-text="closure.status === 'open' ? 'ABIERTA' : 'CERRADA'"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-mono" x-text="'Bs. ' + parseFloat(closure.rate_bcv).toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-mono" x-text="'Bs. ' + parseFloat(closure.rate_usdt).toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-mono text-emerald-600 font-bold" x-text="'$' + parseFloat(closure.total_sales_usd).toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-right">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border inline-block min-w-[70px] text-center"
                                      :class="parseFloat(closure.difference_usd) < 0 ? 'bg-red-50 text-red-700 border-red-200/60' : (parseFloat(closure.difference_usd) > 0 ? 'bg-sky-50 text-sky-700 border-sky-200/60' : 'bg-green-50 text-green-700 border-green-200/60')"
                                      x-text="parseFloat(closure.difference_usd) === 0 ? 'Exacto (Cuadre)' : (parseFloat(closure.difference_usd) < 0 ? 'Faltan $' + Math.abs(closure.difference_usd).toFixed(2) : 'Sobran $' + parseFloat(closure.difference_usd).toFixed(2))">
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg transition-colors" title="Imprimir Ticket">
                                    <i class="fa-solid fa-print"></i>
                                </button>
                                <button x-show="closure.status === 'open'" @click="startClosing(closure)" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition-colors ml-1" title="Cerrar Caja Mágica">
                                    <i class="fa-solid fa-lock"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="closures.length === 0 && !loading">
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                            <i class="fa-solid fa-cash-register text-4xl mb-3 text-gray-300 block"></i>
                            <p>No se encontraron registros de caja en el período seleccionado.</p>
                        </td>
                    </tr>
                    <tr x-show="loading">
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                            <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-3 text-brand-light block"></i>
                            <p>Tirando de los libros contables...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cashClosureManager', () => ({
        closures: [],
        loading: false,
        selectedMonth: new Date().getMonth() + 1,
        selectedYear: new Date().getFullYear(),
        openModal: false,
        
        init() {
            this.fetchHistory();
        },

        async fetchHistory() {
            this.loading = true;
            this.closures = [];
            try {
                const res = await fetch(`/cash-closures/history?month=${this.selectedMonth}&year=${this.selectedYear}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.closures = data.closures;
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },
        
        startClosing(closure) {
            alert('¡En breve implementaremos el Formulario Aritmético Completo de Cierre según el Excel! Esta es la maqueta visual funcional.');
        }
    }));
});
</script>
@endsection
