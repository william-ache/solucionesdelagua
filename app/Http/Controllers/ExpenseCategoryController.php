<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use App\Services\SystemLogService;

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

        $cat = ExpenseCategory::create($request->all());

        SystemLogService::log('Crear', 'Gastos Operativos', "Se agregó una nueva categoría de gasto: {$cat->name}");

        return redirect()->route('expenses.index')->with('success', 'Categoría de gasto creada con éxito.');
    }
}
