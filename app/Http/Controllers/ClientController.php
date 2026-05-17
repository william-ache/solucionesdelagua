<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index()
    {
        $clients = Client::orderBy('name', 'asc')->get();
        return view('clients.index', compact('clients'));
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_id' => 'required|string|max:50|unique:clients,document_id',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        $client = Client::create($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client,
                'message' => 'Cliente creado con éxito.'
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Cliente creado con éxito.');
    }

    /**
     * Show client details for edit.
     */
    public function edit(Client $client)
    {
        return response()->json($client);
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_id' => 'required|string|max:50|unique:clients,document_id,' . $client->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        $client->update($request->all());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'client' => $client,
                'message' => 'Cliente actualizado con éxito.'
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado con éxito.');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        try {
            // Check if there are associated client transactions
            if (method_exists($client, 'transactions') && $client->transactions()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene transacciones registradas.'
                ], 422);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado con éxito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transactions for a given client (current account).
     */
    public function getTransactions(Client $client)
    {
        $transactions = $client->transactions()->orderBy('id', 'desc')->get();
        return response()->json($transactions);
    }

    /**
     * Store a new transaction for a client and update the balance.
     */
    public function storeTransaction(Request $request, Client $client)
    {
        $request->validate([
            'type' => 'required|in:invoice,credit_note,payment',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
        ]);

        $transaction = \App\Models\ClientTransaction::create([
            'client_id' => $client->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Process balance
        if ($request->type === 'invoice') {
            $client->increment('balance', $request->amount);
        } else {
            $client->decrement('balance', $request->amount);
        }

        $client->refresh();

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'client' => $client,
            'message' => 'Movimiento registrado con éxito.'
        ]);
    }
}
