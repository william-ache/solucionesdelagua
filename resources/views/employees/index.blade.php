@extends('layouts.app')

@section('content')
<div x-data="{ openCreateModal: false, openPayModal: false, selectedEmployeeId: null, selectedEmployeeName: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 font-sans">Nómina y Gestión de Personal</h1>
            <p class="text-sm text-gray-500">Expedientes de nómina de colaboradores y registro de salarios</p>
        </div>
        <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all">
            <i class="fa-solid fa-user-plus"></i> Registrar Colaborador
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre del Colaborador</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cédula / Documento</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Salario Base (USD)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Abonos Históricos</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $employee->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $employee->identification_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                ${{ number_format($employee->base_salary_usd, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($employee->status === 'active')
                                    <span class="px-2.5 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-full">
                                        Activo
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold bg-red-100 text-red-700 rounded-full">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="bg-gray-100 text-gray-650 px-2 py-0.5 rounded text-xs font-bold font-mono">
                                    {{ $employee->payrollPayments->count() }} pagos
                                </span>
                                <span class="text-xs text-gray-400 font-semibold block mt-0.5">
                                    Total: ${{ number_format($employee->payrollPayments->sum('amount_paid'), 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                @if($employee->status === 'active')
                                    <button @click="selectedEmployeeId = '{{ $employee->id }}'; selectedEmployeeName = '{{ $employee->name }}'; openPayModal = true;" class="inline-flex items-center gap-1.5 bg-brand-light hover:bg-brand-blue text-white text-xs font-bold py-1.5 px-3 rounded shadow transition-all">
                                        <i class="fa-solid fa-wallet"></i> Pagar Nómina
                                    </button>
                                @else
                                    <span class="text-xs text-gray-405 font-bold italic">Desincorporado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-user-tie text-4xl mb-3 block"></i> No se encontraron colaboradores registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Employee Modal -->
    <div x-show="openCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.away="openCreateModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-user-plus mr-1"></i> Registrar Colaborador</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('employees.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cédula de Identidad</label>
                        <input type="text" name="identification_number" required class="w-full border border-gray-300 rounded-lg px-3 py-2 font-mono focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: V-12345678">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Salario Base (USD)</label>
                            <input type="number" step="0.01" name="base_salary_usd" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: 500.00">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Estado</label>
                            <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
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

    <!-- Reg Pago de Nómina Modal -->
    <div x-show="openPayModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all" @click.away="openPayModal = false">
            <div class="bg-brand-blue p-5 text-white flex items-center justify-between">
                <h3 class="text-md font-bold"><i class="fa-solid fa-cash-register mr-1"></i> Desembolsar Nómina</h3>
                <button @click="openPayModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('payroll-payments.store') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="employee_id" :value="selectedEmployeeId">
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Colaborador Destinatario</p>
                        <p class="text-sm font-bold text-gray-800" x-text="selectedEmployeeName"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Concepto del Pago</label>
                        <input type="text" name="concept" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Primera Quincena Mayo">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Monto Pagado (USD)</label>
                        <input type="number" step="0.01" name="amount_paid" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-lg font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha Emisión</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
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
