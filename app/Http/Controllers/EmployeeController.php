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

        $employee = Employee::create($request->all());
        $employee->load('payrollPayments');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'employee' => $employee,
                'message' => 'Empleado registrado con éxito'
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Empleado registrado con éxito.');
    }

    /**
     * Show/return employee details.
     */
    public function edit(Employee $employee)
    {
        return response()->json($employee);
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:50|unique:employees,identification_number,' . $employee->id,
            'base_salary_usd' => 'required|numeric|min:0.01',
            'status' => 'required|in:active,inactive',
        ]);

        $employee->update($request->all());
        $employee->load('payrollPayments');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'employee' => $employee,
                'message' => 'Empleado actualizado con éxito'
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado con éxito.');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        try {
            if ($employee->payrollPayments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el colaborador porque tiene pagos de nómina registrados.'
                ], 422);
            }

            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Colaborador eliminado con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el colaborador: ' . $e->getMessage()
            ], 500);
        }
    }
}
