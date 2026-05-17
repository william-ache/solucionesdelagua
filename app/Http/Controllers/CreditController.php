<?php

namespace App\Http\Controllers;

use App\Models\Credit;
use App\Models\CreditPayment;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    /**
     * Display a listing of outstanding and paid credits.
     */
    public function index()
    {
        $credits = Credit::with('sale')->orderBy('due_date', 'asc')->get();
        return view('credits.index', compact('credits'));
    }

    /**
     * Show details of a credit, including its payment history.
     */
    public function show($id)
    {
        $credit = Credit::with(['sale', 'payments'])->findOrFail($id);
        return view('credits.show', compact('credit'));
    }

    /**
     * Store a newly created credit payment in storage.
     * Triggers automatic balance deduction in the Credit model hook.
     */
    public function storePayment(Request $request, $creditId)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:100',
        ]);

        $credit = Credit::findOrFail($creditId);

        if ($credit->status === 'paid' || $credit->balance_due <= 0) {
            return redirect()->back()->with('error', 'Este crédito ya se encuentra totalmente cancelado.');
        }

        // Enforce payment cap to prevent overpaying the balance
        $amountToPay = min($request->amount_paid, $credit->balance_due);

        CreditPayment::create([
            'credit_id' => $credit->id,
            'amount_paid' => $amountToPay,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('credits.show', $credit->id)
            ->with('success', 'El abono de pago se registró con éxito y se recalculó el saldo restante.');
    }

    /**
     * Update the specifies credit details in storage.
     */
    public function update(Request $request, Credit $credit)
    {
        $request->validate([
            'due_date' => 'required|date',
            'total_debt' => 'required|numeric|min:0',
            'balance_due' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid',
        ]);

        $credit->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Crédito actualizado con éxito.',
                'credit' => $credit->load('sale')
            ]);
        }

        return redirect()->route('credits.index')->with('success', 'Crédito actualizado con éxito.');
    }

    /**
     * Remove the specified credit from storage.
     */
    public function destroy(Credit $credit)
    {
        $credit->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Crédito eliminado con éxito.'
            ]);
        }

        return redirect()->route('credits.index')->with('success', 'Crédito con éxito.');
    }
}
