<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

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

        Expense::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Gasto registrado con éxito.');
    }
}
