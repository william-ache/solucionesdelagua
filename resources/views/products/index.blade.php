@extends('layouts.app')

@section('content')
<div x-data="productManager" class="max-w-7xl mx-auto pb-10">
    <div class="bg-white rounded-xl shadow-sm border border-gray-150 overflow-hidden mb-6">
        <!-- Header -->
        <div class="p-6 border-b border-gray-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 font-sans">
                    <i class="fa-solid fa-boxes-stacked text-brand-blue mr-2"></i> Inventario de Productos
                </h1>
                <p class="text-sm text-gray-500 mt-1">Gestión de stock, químicos de piscina e insumos.</p>
            </div>
            <button @click="resetForm(); openModal = true" class="bg-brand-blue hover:bg-brand-blue/90 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 shadow transition-all self-start sm:self-auto flex-shrink-0">
                <i class="fa-solid fa-plus"></i> Nuevo Producto
            </button>
        </div>

        <!-- Controls -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Mostrar</span>
                <select x-model.number="perPage" @change="currentPage = 1" class="border border-gray-300 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-brand-light bg-white font-medium text-gray-700">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>registros</span>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="relative w-full sm:w-64 md:w-72">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Buscar por nombre o código..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-brand-light bg-white text-gray-750">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-left divide-y divide-gray-150">
                <thead class="bg-brand-blue text-white">
                    <tr>
                        <th @click="sort('code')" class="px-6 py-4 text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer">Código <i class="fa-solid fa-sort ml-1"></i></th>
                        <th @click="sort('name')" class="px-6 py-4 text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer">Producto <i class="fa-solid fa-sort ml-1"></i></th>
                        <th @click="sort('category')" class="px-6 py-4 text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer">Categoría <i class="fa-solid fa-sort ml-1"></i></th>
                        <th @click="sort('stock_quantity')" class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer">Stock <i class="fa-solid fa-sort ml-1"></i></th>
                        <th @click="sort('unit_price_usd')" class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider border-b border-blue-800 cursor-pointer">Precio (USD) <i class="fa-solid fa-sort ml-1"></i></th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider border-b border-blue-800 text-center">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider border-b border-blue-800">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-sm text-gray-700 bg-white">
                    <template x-for="product in pagedProducts" :key="product.id">
                        <tr class="hover:bg-blue-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-gray-500 text-xs" x-text="product.code || '-'"></td>
                            <td class="px-6 py-4 font-bold text-gray-800" x-text="product.name"></td>
                            <td class="px-6 py-4 text-gray-550" x-text="product.category || '-'"></td>
                            <td class="px-6 py-4 text-center font-mono font-bold" :class="product.stock_quantity <= 5 ? 'text-red-500' : 'text-gray-700'" x-text="product.stock_quantity"></td>
                            <td class="px-6 py-4 text-right font-mono">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold border inline-block min-w-[70px] text-center bg-gray-50 text-gray-700 border-gray-200"
                                      x-text="'$' + parseFloat(product.unit_price_usd).toFixed(2)">
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                      :class="product.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                      x-text="product.status === 'active' ? 'Reabastecible' : 'Descontinuado'">
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="editProduct(product)" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-1.5 rounded-lg transition-colors ml-1" title="Editar Stock/Precio">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="deleteProduct(product)" class="text-red-650 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition-colors ml-1" title="Eliminar del Catálogo">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredProducts.length === 0">
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                            <i class="fa-solid fa-box-open text-4xl mb-3 block text-gray-300"></i> No se encontraron productos registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-gray-50/30 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-bold text-gray-700" x-text="filteredProducts.length === 0 ? 0 : (currentPage - 1) * perPage + 1"></span> 
                a <span class="font-bold text-gray-700" x-text="Math.min(currentPage * perPage, filteredProducts.length)"></span> 
                de <span class="font-bold text-gray-700" x-text="filteredProducts.length"></span> registros
            </div>
            <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Anterior</button>
                <template x-for="p in totalPages" :key="p">
                    <button @click="currentPage = p" class="w-8 h-8 rounded-lg text-sm font-bold transition-all" :class="currentPage === p ? 'bg-brand-blue text-white' : 'border border-gray-300 hover:bg-gray-50 text-gray-700'" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none transition-colors">Siguiente</button>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="openModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[80] flex items-center justify-center p-4 overflow-y-auto" x-cloak>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all" @click.away="openModal = false">
            <div class="bg-brand-blue p-5 rounded-t-xl text-white flex items-center justify-between">
                <h3 class="text-xl font-bold font-sans flex items-center gap-2">
                    <i class="fa-solid fa-box"></i> <span x-text="isEditing ? 'Editar Producto' : 'Nuevo Producto'"></span>
                </h3>
                <button @click="openModal = false" class="text-blue-200 hover:text-white transition-colors focus:outline-none">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form @submit.prevent="saveProduct" class="p-6">
                <!-- Validation Errors -->
                <div x-show="Object.keys(errors).length > 0" class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200 flex items-start gap-2">
                    <i class="fa-solid fa-circle-exclamation mt-1"></i>
                    <div>
                        <p class="font-bold mb-1">Hay errores en el formulario:</p>
                        <ul class="list-disc pl-5">
                            <template x-for="error in Object.values(errors)" :key="error">
                                <li x-text="error[0]"></li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
                        <input type="text" x-model="formData.name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="Ej: Cloro Granulado 70% Tambor">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Código (SKU)</label>
                        <input type="text" x-model="formData.code" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-brand-light" placeholder="CLO-01">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                        <select x-model="formData.category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light">
                            <option value="">Ninguna</option>
                            <option value="Químicos">Químicos</option>
                            <option value="Limpieza">Limpieza de Piscinas</option>
                            <option value="Filtros y Bombas">Filtros y Bombas</option>
                            <option value="Accesorios">Accesorios y Otros</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Precio de Venta (USD) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-bold">$</span>
                            <input type="number" step="0.01" min="0" x-model.number="formData.unit_price_usd" required class="w-full border border-gray-300 rounded-lg pl-8 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light font-mono" placeholder="0.00">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Stock Actual <span class="text-red-500">*</span></label>
                        <input type="number" step="1" min="0" x-model.number="formData.stock_quantity" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light font-mono" placeholder="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Estado de Inventario</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" x-model="formData.status" value="active" name="status" class="text-brand-blue focus:ring-brand-light w-4 h-4"> Reabastecible / Activo
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" x-model="formData.status" value="inactive" name="status" class="text-red-500 focus:ring-red-400 w-4 h-4"> Descontinuado
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-150">
                    <button type="button" @click="openModal = false" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 font-bold transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-brand-blue hover:bg-brand-blue/90 text-white rounded-lg font-bold shadow-sm flex items-center gap-2 transition-all">
                        <i class="fa-solid fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('productManager', () => ({
        products: @json($products),
        searchQuery: '',
        currentPage: 1,
        perPage: 10,
        sortColumn: 'name',
        sortDirection: 'asc',
        
        openModal: false,
        isEditing: false,
        errors: {},
        
        formData: {
            id: '', code: '', name: '', category: '', unit_price_usd: '', stock_quantity: 0, status: 'active'
        },

        get filteredProducts() {
            let filtered = this.products;
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(p => 
                    p.name.toLowerCase().includes(q) || 
                    (p.code && p.code.toLowerCase().includes(q))
                );
            }
            filtered = filtered.sort((a, b) => {
                let valA = a[this.sortColumn] || '';
                let valB = b[this.sortColumn] || '';
                if(typeof valA === 'string') valA = valA.toLowerCase();
                if(typeof valB === 'string') valB = valB.toLowerCase();
                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
            return filtered;
        },

        get pagedProducts() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredProducts.slice(start, end);
        },

        get totalPages() {
            return Math.ceil(this.filteredProducts.length / this.perPage) || 1;
        },

        sort(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
        },

        resetForm() {
            this.isEditing = false;
            this.errors = {};
            this.formData = { id: '', code: '', name: '', category: '', unit_price_usd: '', stock_quantity: 0, status: 'active' };
        },

        editProduct(product) {
            this.isEditing = true;
            this.errors = {};
            this.formData = JSON.parse(JSON.stringify(product));
            this.openModal = true;
        },

        async saveProduct() {
            this.errors = {};
            try {
                const url = this.isEditing ? `/products/${this.formData.id}` : '/products';
                const method = this.isEditing ? 'PUT' : 'POST';
                const token = document.querySelector('meta[name="csrf-token"]').content;
                
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    if (this.isEditing) {
                        const index = this.products.findIndex(p => p.id === data.product.id);
                        if (index !== -1) this.products[index] = data.product;
                    } else {
                        this.products.unshift(data.product);
                    }
                    this.openModal = false;
                    this.resetForm();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else if (response.status === 422) {
                    this.errors = data.errors;
                } else {
                    Swal.fire('Error', data.message || 'Error desconocido al guardar.', 'error');
                }
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Fallo de red al intentar guardar.', 'error');
            }
        },
        
        deleteProduct(product) {
            Swal.fire({
                title: `¿Eliminar a ${product.name}?`,
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#adb5bd',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const response = await fetch(`/products/${product.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.products = this.products.filter(p => p.id !== product.id);
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Error de red.', 'error');
                    }
                }
            });
        }
    }));
});
</script>
@endsection
