<?php

namespace App\Http\Controllers;

use App\Models\PayrollPayment;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
    /**
     * Store a newly created payroll payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'concept' => 'required|string|max:255',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        $payment = PayrollPayment::create($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'payment' => $payment,
                'message' => 'Abono de nómina registrado con éxito.'
            ]);
        }

        return redirect()->back()->with('success', 'Abono de nómina registrado con éxito.');
    }
}
