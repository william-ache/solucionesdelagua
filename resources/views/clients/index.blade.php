@extends('layouts.app')

@section('content')
<div x-data="{ openCreateModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 font-sans">Gestión de Clientes</h1>
            <p class="text-sm text-gray-500">Expedientes de clientes y saldo de cuenta corriente</p>
        </div>
        <button @click="openCreateModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all">
            <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente / Empresa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">RIF / Cédula</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teléfono</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Correo Electrónico</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dirección</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Saldo Acreedor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $client->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550">{{ $client->document_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550">{{ $client->phone ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-550">{{ $client->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $client->address ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ $client->balance > 0 ? 'text-red-500' : 'text-gray-700' }}">
                                ${{ number_format($client->balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                                <i class="fa-solid fa-users text-4xl mb-3 block"></i> No se encontraron clientes registrados.
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
                <h3 class="text-lg font-bold"><i class="fa-solid fa-user-plus mr-1"></i> Registrar Nuevo Cliente</h3>
                <button @click="openCreateModal = false" class="text-white hover:text-brand-light text-xl"><i class="fa-solid fa-times"></i></button>
            </div>
            
            <form action="{{ route('clients.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre o Razón Social</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Identificación (RIF / Cédula)</label>
                        <input type="text" name="document_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: J-12345678-9">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Teléfono de Contacto</label>
                            <input type="text" name="phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                            <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dirección de Entrega / Fiscal</label>
                        <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="openCreateModal = false" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">Cancelar</button>
                    <button type="submit" class="bg-brand-blue hover:bg-brand-blue/90 text-white px-5 py-2 rounded-lg font-bold shadow transition-all">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
