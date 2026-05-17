<?php

namespace App\Http\Controllers;

use App\Models\TaxPayment;
use Illuminate\Http\Request;
use App\Services\SystemLogService;

class TaxPaymentController extends Controller
{
    /**
     * Display a listing of tax payments.
     */
    public function index()
    {
        $payments = TaxPayment::orderBy('payment_date', 'desc')->get();
        return view('tax_payments.index', compact('payments'));
    }

    /**
     * Store a newly created tax payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tax_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'payment_date' => 'required|date',
            'reference_number' => 'required|string|max:100',
        ]);

        $payment = TaxPayment::create($request->all());

        SystemLogService::log('Crear', 'Impuestos', "Se registró un nuevo pago del impuesto: {$payment->tax_name} por {$payment->amount} {$payment->currency}");

        return redirect()->route('tax-payments.index')->with('success', 'Pago de impuesto registrado con éxito.');
    }

    /**
     * Update the specified tax payment in storage.
     */
    public function update(Request $request, TaxPayment $taxPayment)
    {
        $request->validate([
            'tax_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'payment_date' => 'required|date',
            'reference_number' => 'required|string|max:100',
        ]);

        $taxPayment->update($request->all());

        SystemLogService::log('Editar', 'Impuestos', "Se ajustó el registro de pago del impuesto: {$taxPayment->tax_name}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Impuesto actualizado con éxito.',
                'payment' => $taxPayment
            ]);
        }

        return redirect()->route('tax-payments.index')->with('success', 'Impuesto actualizado con éxito.');
    }

    /**
     * Remove the specified tax payment from storage.
     */
    public function destroy(TaxPayment $taxPayment)
    {
        $taxName = $taxPayment->tax_name;
        $taxPayment->delete();

        SystemLogService::log('Eliminar', 'Impuestos', "Se eliminó de los registros el pago del impuesto: {$taxName}");

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Impuesto eliminado con éxito.'
            ]);
        }

        return redirect()->route('tax-payments.index')->with('success', 'Impuesto eliminado con éxito.');
    }
}
