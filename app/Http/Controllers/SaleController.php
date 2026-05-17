<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use App\Services\SystemLogService;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index()
    {
        $sales = Sale::orderBy('date', 'desc')->get();
        return view('sales.index', compact('sales'));
    }

    /**
     * Store a newly registered sale.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'status' => 'required|in:paid,credit',
            'date' => 'required|date',
        ]);

        $sale = Sale::create($request->all());

        SystemLogService::log('Crear', 'Ventas', "Se ha registrado una nueva venta para {$sale->client_name} por {$sale->total_amount} {$sale->currency}");

        return redirect()->route('sales.index')->with('success', 'Venta registrada con éxito.');
    }

    /**
     * Update the specified sale in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'status' => 'required|in:paid,credit',
            'date' => 'required|date',
        ]);

        $sale->update($request->all());

        SystemLogService::log('Editar', 'Ventas', "Se ha actualizado la información de la venta a cliente {$sale->client_name} (ID: {$sale->id})");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Venta actualizada con éxito.',
                'sale' => $sale
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Venta actualizada con éxito.');
    }

    /**
     * Remove the specified sale from storage.
     */
    public function destroy(Sale $sale)
    {
        $clientName = $sale->client_name;
        $saleId = $sale->id;
        $sale->delete();

        SystemLogService::log('Eliminar', 'Ventas', "Se ha eliminado una venta del cliente {$clientName} (ID Venta: {$saleId})");

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada con éxito.'
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Venta eliminada con éxito.');
    }
}
