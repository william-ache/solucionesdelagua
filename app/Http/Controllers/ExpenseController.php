<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use App\Services\SystemLogService;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses.
     */
    public function index()
    {
        $expenses = Expense::with('category')->orderBy('expense_date', 'desc')->get();
        $categories = ExpenseCategory::orderBy('name', 'asc')->get();
        return view('expenses.index', compact('expenses', 'categories'));
    }

    /**
     * Store a newly created operational expense.
     */
    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'expense_date' => 'required|date',
        ]);

        $expense = Expense::create($request->all());

        SystemLogService::log('Crear', 'Gastos Operativos', "Se ha registrado un gasto por {$expense->amount} {$expense->currency} ({$expense->description})");

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado con éxito.');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->all());

        SystemLogService::log('Editar', 'Gastos Operativos', "Se ha modificado el gasto: {$expense->description}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto actualizado con éxito.',
                'expense' => $expense->load('category')
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Gasto actualizado con éxito.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $expenseDesc = $expense->description;
        $expense->delete();

        SystemLogService::log('Eliminar', 'Gastos Operativos', "Se ha eliminado un registro de gasto: {$expenseDesc}");

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gasto eliminado con éxito.'
            ]);
        }

        return redirect()->route('expenses.index')->with('success', 'Gasto eliminado con éxito.');
    }
}
