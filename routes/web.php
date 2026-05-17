<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\TaxPaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollPaymentController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\CashClosureController;
use App\Http\Controllers\ProductController;

// Public and Auth Landing
Route::get('/', function () {
    if (Illuminate\Support\Facades\Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    if (Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    return back()->withErrors([
        'email' => 'Las credenciales proporcionadas no corresponden a nuestros registros.',
    ]);
});

Route::post('/logout', function (Illuminate\Http\Request $request) {
    Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Grouped ERP operations protected under 'auth' middleware
Route::middleware(['auth'])->group(function () {
    
    // Main Panel View
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/global-search', [DashboardController::class, 'globalSearch'])->name('global-search');
    Route::get('/global-detail', [DashboardController::class, 'globalDetail'])->name('global-detail');

    // 1. Módulo de Ventas
    Route::resource('sales', SaleController::class);
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/transactions', [ClientController::class, 'getTransactions'])->name('clients.transactions.index');
    Route::post('/clients/{client}/transactions', [ClientController::class, 'storeTransaction'])->name('clients.transactions.store');

    // 5.5 Proveedores
    Route::resource('suppliers', SupplierController::class);
    Route::get('/suppliers/{supplier}/transactions', [SupplierController::class, 'getTransactions'])->name('suppliers.transactions.index');
    Route::post('/suppliers/{supplier}/transactions', [SupplierController::class, 'storeTransaction'])->name('suppliers.transactions.store');

    // 2. Módulo de Créditos y Cobranzas
    Route::resource('credits', CreditController::class);
    Route::post('/credits/{id}/payment', [CreditController::class, 'storePayment'])->name('credits.payments.store');

    // 3. Módulo de Divisas y Tesorería
    Route::resource('exchange-rates', ExchangeRateController::class);

    // 4. Módulo de Impuestos
    Route::resource('tax-payments', TaxPaymentController::class);

    // 5. Módulo de Nómina (Empleados y Pagos de Nómina)
    Route::resource('employees', EmployeeController::class);
    Route::resource('payroll-payments', PayrollPaymentController::class)->except(['edit', 'update']);

    // 6. Módulo de Gastos Operativos (Categorías y Gastos)
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::resource('expenses', ExpenseController::class);

    // 7. System Logs (Read Only)
    Route::get('/system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');

    // 8. Cierre de Caja
    Route::get('/cash-closures/history', [CashClosureController::class, 'history'])->name('cash-closures.history');
    Route::post('/cash-closures/open', [CashClosureController::class, 'open'])->name('cash-closures.open');
    Route::post('/cash-closures/{closure}/close', [CashClosureController::class, 'close'])->name('cash-closures.close');
    Route::get('/api/v1/rates/bcv', [CashClosureController::class, 'getBcvRate'])->name('api.rates.bcv');

    // 9. Inventario (Productos de Limpieza, etc.)
    Route::resource('products', ProductController::class);

});
