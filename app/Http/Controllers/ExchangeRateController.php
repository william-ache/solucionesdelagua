<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    /**
     * Display a listing of exchange rates.
     */
    public function index()
    {
        $rates = ExchangeRate::orderBy('date', 'desc')->get();
        return view('exchange_rates.index', compact('rates'));
    }

    /**
     * Store a newly created exchange rate.
     */
    public function store(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0.0001',
            'date' => 'required|date|unique:exchange_rates,date',
        ]);

        ExchangeRate::create($request->all());

        return redirect()->route('exchange-rates.index')->with('success', 'Tasa de cambio guardada con éxito.');
    }
}
