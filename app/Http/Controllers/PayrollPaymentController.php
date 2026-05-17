<?php

namespace App\Http\Controllers;

use App\Models\PayrollPayment;
use Illuminate\Http\Request;
use App\Services\SystemLogService;

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
        $payment->load('employee');

        SystemLogService::log('Abonar Pago', 'Nómina', "Se registró un abono de nómina por {$payment->amount_paid} USD a {$payment->employee->name} ({$payment->concept})");

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
