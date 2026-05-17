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

    /**
     * Omni-search system records globally across multiple models.
     */
    public function globalSearch(Request $request)
    {
        $q = $request->query('q', '');
        if (strlen(trim($q)) < 1) {
            return response()->json([]);
        }

        $results = [];

        // 1. Clientes
        $clients = \App\Models\Client::where('name', 'like', "%{$q}%")
            ->orWhere('document_id', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        foreach ($clients as $c) {
            $results[] = [
                'id' => $c->id,
                'model' => 'client',
                'type' => 'Cliente',
                'icon' => 'fa-solid fa-users',
                'title' => $c->name,
                'subtitle' => "RIF/Cédula: {$c->document_id} • Saldo Acreedor: $" . number_format($c->balance, 2),
                'url' => route('clients.index') . '?search=' . urlencode($c->name),
            ];
        }

        // 2. Créditos (Show view)
        $credits = \App\Models\Credit::with('sale')
            ->where('id', 'like', "%{$q}%")
            ->orWhereHas('sale', function ($query) use ($q) {
                $query->where('client_name', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get();
        foreach ($credits as $cr) {
            $clientName = $cr->sale->client_name ?? 'Desconocido';
            $results[] = [
                'id' => $cr->id,
                'model' => 'credit',
                'type' => 'Crédito / Cuentas por Cobrar',
                'icon' => 'fa-solid fa-receipt text-red-500',
                'title' => "Crédito #{$cr->id} — {$clientName}",
                'subtitle' => "Pendiente: $" . number_format($cr->balance_due, 2) . " • Vence: " . ($cr->due_date ? $cr->due_date->format('d/m/Y') : '-'),
                'url' => route('credits.show', $cr->id),
            ];
        }

        // 3. Ventas
        $sales = \App\Models\Sale::where('client_name', 'like', "%{$q}%")
            ->orWhere('id', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        foreach ($sales as $s) {
            $results[] = [
                'id' => $s->id,
                'model' => 'sale',
                'type' => 'Venta / Facturación',
                'icon' => 'fa-solid fa-cart-shopping text-emerald-500',
                'title' => "Venta #{$s->id} — {$s->client_name}",
                'subtitle' => "Total: " . ($s->currency === 'USD' ? '$' : 'Bs. ') . number_format($s->total_amount, 2) . " • Condición: " . ($s->status === 'paid' ? 'Contado' : 'Crédito'),
                'url' => route('sales.index') . '?search=' . urlencode($s->client_name),
            ];
        }

        // 4. Colaboradores
        $employees = \App\Models\Employee::where('name', 'like', "%{$q}%")
            ->orWhere('identification_number', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        foreach ($employees as $e) {
            $results[] = [
                'id' => $e->id,
                'model' => 'employee',
                'type' => 'Colaborador / Nómina',
                'icon' => 'fa-solid fa-user-tie text-brand-blue',
                'title' => $e->name,
                'subtitle' => "Cédula: {$e->identification_number} • Saldo USD: $" . number_format($e->base_salary_usd, 2),
                'url' => route('employees.index') . '?search=' . urlencode($e->name),
            ];
        }

        // 5. Gastos Operativos
        $expenses = \App\Models\Expense::with('category')
            ->where('description', 'like', "%{$q}%")
            ->orWhereHas('category', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get();
        foreach ($expenses as $ex) {
            $catName = $ex->category->name ?? 'Gastos generales';
            $results[] = [
                'id' => $ex->id,
                'model' => 'expense',
                'type' => 'Gasto Operativo',
                'icon' => 'fa-solid fa-wallet text-amber-500',
                'title' => $ex->description ?: $catName,
                'subtitle' => "Categoría: {$catName} • Monto: " . ($ex->currency === 'USD' ? '$' : 'Bs. ') . number_format($ex->amount, 2),
                'url' => route('expenses.index') . '?search=' . urlencode($ex->description ?: $catName),
            ];
        }

        // 6. Impuestos y Cargas Fiscales
        $taxes = \App\Models\TaxPayment::where('tax_name', 'like', "%{$q}%")
            ->orWhere('reference_number', 'like', "%{$q}%")
            ->limit(5)
            ->get();
        foreach ($taxes as $t) {
            $results[] = [
                'id' => $t->id,
                'model' => 'tax_payment',
                'type' => 'Impuestos / Fiscos',
                'icon' => 'fa-solid fa-percent text-indigo-500',
                'title' => $t->tax_name,
                'subtitle' => "Ref: {$t->reference_number} • Monto: " . ($t->currency === 'USD' ? '$' : 'Bs. ') . number_format($t->amount, 2),
                'url' => route('tax-payments.index') . '?search=' . urlencode($t->tax_name),
            ];
        }

        // Limit the results to a maximum of 5 suggestions
        $slicedResults = array_slice($results, 0, 5);

        return response()->json($slicedResults);
    }

    /**
     * Fetch formatted transaction details dynamically for global view modal.
     */
    public function globalDetail(Request $request)
    {
        $model = $request->query('model');
        $id = $request->query('id');

        if (!$model || !$id) {
            return response()->json(['error' => 'Parámetros incompletos.'], 400);
        }

        $details = [];
        $title = '';
        $typeLabel = '';

        switch ($model) {
            case 'client':
                $record = \App\Models\Client::find($id);
                if ($record) {
                    $title = $record->name;
                    $typeLabel = 'Cliente';
                    $details = [
                        'Nombre Completo' => $record->name,
                        'Cédula / RIF' => $record->document_id,
                        'Dirección / Ubicación' => $record->address ?: 'No especificada',
                        'Teléfono de Contacto' => $record->phone ?: 'No especificado',
                        'Email Registrado' => $record->email ?: 'No especificado',
                        'Saldo Acreedor (USD)' => '$' . number_format($record->balance, 2),
                        'Fecha Registro' => $record->created_at->format('d/m/Y h:i a'),
                    ];
                }
                break;
            case 'sale':
                $record = \App\Models\Sale::find($id);
                if ($record) {
                    $title = "Venta #{$record->id}";
                    $typeLabel = 'Venta / Facturación';
                    $details = [
                        'Número de Transacción' => "#{$record->id}",
                        'Cliente Asociado' => $record->client_name,
                        'Total de Venta' => ($record->currency === 'USD' ? '$' : 'Bs. ') . number_format($record->total_amount, 2),
                        'Moneda Pactada' => $record->currency,
                        'Condición de Pago' => $record->status === 'paid' ? 'Contado / Solventado' : 'A Crédito / Pendiente',
                        'Fecha de Emisión' => $record->date ? $record->date->format('d/m/Y') : '-',
                        'Creado en Sistema' => $record->created_at->format('d/m/Y h:i a'),
                    ];
                }
                break;
            case 'credit':
                $record = \App\Models\Credit::with('sale')->find($id);
                if ($record) {
                    $clientName = $record->sale->client_name ?? 'Cliente General';
                    $title = "Crédito #{$record->id}";
                    $typeLabel = 'Crédito / Cuentas por Cobrar';
                    $details = [
                        'Expediente Crédito' => "#{$record->id}",
                        'Cliente Beneficiario' => $clientName,
                        'Monto Total Deuda' => '$' . number_format($record->total_debt, 2),
                        'Saldo Pendiente Real' => '$' . number_format($record->balance_due, 2),
                        'Estatus General' => $record->status === 'paid' ? 'Solventado' : 'Pendiente cobro',
                        'Fecha Vencimiento' => $record->due_date ? $record->due_date->format('d/m/Y') : '-',
                        'Última Notificación' => $record->updated_at->format('d/m/Y h:i a'),
                    ];
                }
                break;
            case 'employee':
                $record = \App\Models\Employee::find($id);
                if ($record) {
                    $title = $record->name;
                    $typeLabel = 'Colaborador / Nómina';
                    $details = [
                        'Nombre del Empleado' => $record->name,
                        'Cédula de Identidad' => $record->identification_number,
                        'Cargo Acordado' => $record->position ?: 'No especificado',
                        'Salario Base Acordado (USD)' => '$' . number_format($record->base_salary_usd, 2),
                        'Fecha Ingreso' => $record->hire_date ? $record->hire_date->format('d/m/Y') : 'No registrada',
                        'Estado' => $record->is_active ? 'Activo en Nómina' : 'Inactivo',
                        'Creado en Sistema' => $record->created_at->format('d/m/Y h:i a'),
                    ];
                }
                break;
            case 'expense':
                $record = \App\Models\Expense::with('category')->find($id);
                if ($record) {
                    $catName = $record->category->name ?? 'Gastos Generales';
                    $title = $record->description ?: "Gasto #{$record->id}";
                    $typeLabel = 'Gasto Operativo';
                    $details = [
                        'Identificador Gasto' => "#{$record->id}",
                        'Categoría de Gasto' => $catName,
                        'Descripción Detallada' => $record->description ?: 'Categoría por defecto',
                        'Monto Transacción' => ($record->currency === 'USD' ? '$' : 'Bs. ') . number_format($record->amount, 2),
                        'Moneda Cuenta' => $record->currency,
                        'Fecha Erogación' => $record->expense_date ? $record->expense_date->format('d/m/Y') : '-',
                        'Creado en Sistema' => $record->created_at->format('d/m/Y h:i a'),
                    ];
                }
                break;
            case 'tax_payment':
                $record = \App\Models\TaxPayment::find($id);
                if ($record) {
                    $title = $record->tax_name;
                    $typeLabel = 'Impuestos / Fiscos';
                    $details = [
                        'Impuesto Declarado' => $record->tax_name,
                        'Número de Referencia' => $record->reference_number ?: 'N/D',
                        'Monto Declarado' => ($record->currency === 'USD' ? '$' : 'Bs. ') . number_format($record->amount, 2),
                        'Moneda Impuesto' => $record->currency,
                        'Fecha del Impuesto' => $record->payment_date ? $record->payment_date->format('d/m/Y') : '-',
                        'Detallado en Sistema' => $record->created_at->format('d/m/Y h:i a'),
                    ];
                }
                break;
        }

        if (empty($details)) {
            return response()->json(['error' => 'Registro no encontrado.'], 404);
        }

        return response()->json([
            'title' => $title,
            'type' => $typeLabel,
            'details' => $details
        ]);
    }
}
