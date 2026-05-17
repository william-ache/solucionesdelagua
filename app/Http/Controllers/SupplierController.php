<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use App\Services\SystemLogService;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_id' => 'required|string|max:100|unique:suppliers,document_id',
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'balance' => 'numeric|min:0'
        ]);

        $supplier = Supplier::create($request->all());

        SystemLogService::log('Crear', 'Proveedor', "Se registró un nuevo proveedor: {$supplier->name} ({$supplier->document_id})");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Proveedor registrado exitosamente.'
            ]);
        }

        return redirect()->route('suppliers.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_id' => 'required|string|max:100|unique:suppliers,document_id,' . $supplier->id,
            'phone' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255'
        ]);

        $supplier->update($request->all());

        SystemLogService::log('Editar', 'Proveedor', "Se modificó el proveedor: {$supplier->name}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => 'Proveedor actualizado exitosamente.'
            ]);
        }

        return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $name = $supplier->name;
            $supplier->delete();
            SystemLogService::log('Eliminar', 'Proveedor', "Se eliminó el proveedor: {$name}");

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'El proveedor tiene facturas o movimientos asociados y no puede ser eliminado.'
            ], 422);
        }
    }

    // Modal view for transactions (Cuenta Corriente / Cuenta por Pagar)
    public function getTransactions(Supplier $supplier)
    {
        $transactions = $supplier->transactions()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'transactions' => $transactions,
            'balance' => $supplier->balance
        ]);
    }

    public function storeTransaction(Request $request, Supplier $supplier)
    {
        $request->validate([
            'type' => 'required|in:invoice,credit_note,payment',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $transaction = $supplier->transactions()->create([
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
            ]);

            // Factura incrementa la deuda (cuentas por pagar balance sube)
            // Pago o nota de crédito la reduce
            if ($request->type === 'invoice') {
                $supplier->balance += $request->amount;
            } else {
                $supplier->balance -= $request->amount;
            }
            $supplier->save();

            $typetext = $request->type === 'invoice' ? 'Factura/Cargo' : ($request->type === 'payment' ? 'Pago' : 'Nota de Crédito');
            SystemLogService::log('Movimiento', 'Proveedor', "{$typetext} de $" . number_format($request->amount, 2) . " a proveedor: {$supplier->name}");

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'new_balance' => $supplier->balance,
                'message' => 'Transacción registrada correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar transacción: ' . $e->getMessage()
            ], 500);
        }
    }
}
