<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = Employee::with('payrollPayments')->orderBy('name', 'asc')->get();
        return view('employees.index', compact('employees'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:50|unique:employees,identification_number',
            'base_salary_usd' => 'required|numeric|min:0.01',
            'status' => 'required|in:active,inactive',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Empleado registrado con éxito.');
    }
}
