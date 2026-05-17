<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\PayrollPayment;
use App\Models\Credit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Compute financial metrics and monthly metrics for display.
     */
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Total Ventas del mes actual (USD)
        $totalSalesUsd = Sale::where('currency', 'USD')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');

        // 2. Total Gastos del mes actual (USD) (expenses USD + payroll_payments)
        $expensesUsd = Expense::where('currency', 'USD')
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $payrollPaidUsd = PayrollPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount_paid');

        $totalExpensesUsd = $expensesUsd + $payrollPaidUsd;

        // 3. Cuentas por cobrar totales (Suma de balance_due de la tabla credits)
        $totalAccountsReceivable = Credit::where('status', 'pending')
            ->sum('balance_due');

        // KPI bundle
        $kpis = [
            'sales_month' => $totalSalesUsd,
            'expenses_month' => $totalExpensesUsd,
            'accounts_receivable' => $totalAccountsReceivable,
        ];

        // Annual comparison data for Chart.js
        $currentYear = Carbon::now()->year;
        $incomesYear = [];
        $expensesYear = [];
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        for ($i = 1; $i <= 12; $i++) {
            $monthStart = Carbon::create($currentYear, $i, 1)->startOfMonth();
            $monthEnd = Carbon::create($currentYear, $i, 1)->endOfMonth();

            // Sales in USD
            $salesM = Sale::where('currency', 'USD')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('total_amount');

            // Expenses in USD
            $expM = Expense::where('currency', 'USD')
                ->whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');

            // Payroll payments
            $payM = PayrollPayment::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount_paid');

            $incomesYear[] = round($salesM, 2);
            $expensesYear[] = round($expM + $payM, 2);
        }

        $chartData = [
            'labels' => $months,
            'incomes' => $incomesYear,
            'expenses' => $expensesYear,
        ];

        return view('dashboard', compact('kpis', 'chartData'));
    }
}
