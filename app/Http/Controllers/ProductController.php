<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\SystemLogService;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name', 'asc')->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:100|unique:products,code',
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit_price_usd' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string'
        ]);

        $product = Product::create($request->all());

        SystemLogService::log('Crear', 'Inventario', "Se agregó el producto: {$product->name} al inventario con {$product->stock_quantity} unidades predeterminadas.");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Producto registrado exitosamente en el inventario.'
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Producto registrado exitosamente.');
    }

    public function edit(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'nullable|string|max:100|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit_price_usd' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string'
        ]);

        $oldStock = $product->stock_quantity;
        $product->update($request->all());
        
        $stockMessage = ($oldStock !== $product->stock_quantity) ? " (Stock ajustado de {$oldStock} a {$product->stock_quantity})" : "";

        SystemLogService::log('Editar', 'Inventario', "Se actualizó el producto: {$product->name}{$stockMessage}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'message' => 'Producto actualizado exitosamente.'
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        try {
            $name = $product->name;
            $product->delete();

            SystemLogService::log('Eliminar', 'Inventario', "Se eliminó el producto del sistema: {$name}");

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}
