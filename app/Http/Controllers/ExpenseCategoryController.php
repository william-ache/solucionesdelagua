<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Store a newly created expense category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:250|unique:expense_categories,name',
        ]);

        ExpenseCategory::create($request->all());

        return redirect()->route('expenses.index')->with('success', 'Categoría de gasto creada con éxito.');
    }
}
