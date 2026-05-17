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

        Client::create($request->all());

        return redirect()->route('clients.index')->with('success', 'Cliente creado con éxito.');
    }
}
