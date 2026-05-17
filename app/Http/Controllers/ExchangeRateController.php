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

        $exchangeRate = ExchangeRate::create($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'rate' => $exchangeRate,
                'message' => 'Tasa de cambio guardada con éxito.'
            ]);
        }

        return redirect()->route('exchange-rates.index')->with('success', 'Tasa de cambio guardada con éxito.');
    }

    /**
     * Show edit details.
     */
    public function edit(ExchangeRate $exchangeRate)
    {
        return response()->json($exchangeRate);
    }

    /**
     * Update exchange rate.
     */
    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0.0001',
            'date' => 'required|date|unique:exchange_rates,date,' . $exchangeRate->id,
        ]);

        $exchangeRate->update($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'rate' => $exchangeRate,
                'message' => 'Tasa de cambio actualizada con éxito.'
            ]);
        }

        return redirect()->route('exchange-rates.index')->with('success', 'Tasa de cambio actualizada con éxito.');
    }

    /**
     * Remove exchange rate.
     */
    public function destroy(ExchangeRate $exchangeRate)
    {
        try {
            $exchangeRate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tasa de cambio eliminada con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la tasa: ' . $e->getMessage()
            ], 500);
        }
    }
}
