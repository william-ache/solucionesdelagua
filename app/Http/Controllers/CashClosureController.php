<?php

namespace App\Http\Controllers;

use App\Models\CashClosure;
use App\Models\CashClosureDetail;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CashClosureController extends Controller
{
    /**
     * API: Get the current automated BCV rate
     */
    public function getBcvRate()
    {
        // For this proposal, we grab the latest from the database
        // Real implementation can query an external API if needed
        $latest = ExchangeRate::orderBy('date', 'desc')->first();
        
        return response()->json([
            'success' => true,
            'source' => 'DB_LATEST',
            'rate' => $latest ? $latest->rate : 0.00
        ]);
    }

    /**
     * History Dashboard logic (filtered by Month/Year)
     */
    public function history(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        if ($request->wantsJson() || $request->is('api/*')) {
            $closures = CashClosure::with('details')
                ->where('month', $month)
                ->where('year', $year)
                ->orderBy('date', 'desc')
                ->get();
            return response()->json(['success' => true, 'closures' => $closures]);
        }

        // Return Blade view for history (which will be converted to UI components)
        return view('cash_closures.history', compact('month', 'year'));
    }

    /**
     * Open a new Cash Closure for the day
     */
    public function open(Request $request)
    {
        $request->validate([
            'rate_bcv' => 'required|numeric|min:0.01',
            'rate_usdt' => 'required|numeric|min:0.01',
            'initial_amount_usd' => 'required|numeric|min:0',
            'initial_amount_ves' => 'required|numeric|min:0',
        ]);

        // Check if there is already an open closure
        if (CashClosure::where('status', 'open')->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya existe una caja abierta.'], 422);
        }

        $closure = CashClosure::create([
            'date' => Carbon::today(),
            'month' => Carbon::today()->month,
            'year' => Carbon::today()->year,
            'rate_bcv' => $request->rate_bcv,
            'rate_usdt' => $request->rate_usdt,
            'initial_amount_usd' => $request->initial_amount_usd,
            'initial_amount_ves' => $request->initial_amount_ves,
            'status' => 'open',
            'opened_by_user_id' => auth()->id() ?? 1 // Fallback for testing
        ]);

        return response()->json(['success' => true, 'message' => 'Caja abierta exitosamente.', 'closure' => $closure]);
    }

    /**
     * Close the current active Cash Closure
     */
    public function close(Request $request, CashClosure $closure)
    {
        if ($closure->status === 'closed') {
            return response()->json(['success' => false, 'message' => 'Esta caja ya está cerrada.'], 422);
        }

        $request->validate([
            'audited_usd' => 'required|numeric|min:0',
            'audited_ves' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Re-calculate expected sales from the related details table
            // Usually, details are appended instantly during the day from SalesController, ExpenseController, etc.
            $expected_usd = $closure->initial_amount_usd + $closure->details()->where('type', 'income')->sum('amount_usd');
            $expected_ves = $closure->initial_amount_ves + $closure->details()->where('type', 'income')->sum('amount_ves');
            // Less expenses...
            $expected_usd -= $closure->details()->whereIn('type', ['expense', 'credit_given'])->sum('amount_usd');
            $expected_ves -= $closure->details()->whereIn('type', ['expense', 'credit_given'])->sum('amount_ves');

            $closure->update([
                'audited_usd' => $request->audited_usd,
                'audited_ves' => $request->audited_ves,
                'difference_usd' => $request->audited_usd - $expected_usd,
                'difference_ves' => $request->audited_ves - $expected_ves,
                'total_sales_usd' => clone $closure->details()->where('type', 'income')->sum('amount_usd'),
                'total_sales_bs' => clone $closure->details()->where('type', 'income')->sum('amount_ves'),
                'status' => 'closed',
                'closed_at' => Carbon::now(),
                'closed_by_user_id' => auth()->id() ?? 1
            ]);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Caja cerrada y cuadrada.', 'closure' => $closure]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al cerrar caja: ' . $e->getMessage()], 500);
        }
    }
}
