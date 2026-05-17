<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index()
    {
        $sales = Sale::orderBy('date', 'desc')->get();
        return view('sales.index', compact('sales'));
    }

    /**
     * Store a newly registered sale.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,VES',
            'status' => 'required|in:paid,credit',
            'date' => 'required|date',
        ]);

        Sale::create($request->all());

        return redirect()->route('sales.index')->with('success', 'Venta registrada con éxito.');
    }
}
