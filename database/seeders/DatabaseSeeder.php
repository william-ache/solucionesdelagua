<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Client;
use App\Models\BankMovement;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use App\Models\TaxPayment;
use App\Models\Sale;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Employee;
use App\Models\PayrollPayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear usuario de prueba (Admin)
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@solucionesdelagua.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Crear Cuenta Bancaria
        $bank = BankAccount::create([
            'bank_name' => 'Banco de Venezuela',
            'account_type' => 'Corriente',
            'account_number' => '0102-XXXX-XXXX-XXXX-1234',
            'initial_balance' => 1000.00,
            'current_balance' => 8500.00,
        ]);

        // 3. Crear Historial de Tasas de Cambio
        $rate = ExchangeRate::create([
            'rate' => 36.5000,
            'date' => Carbon::now(),
        ]);

        // 4. Crear Empleados y Pagos de Nómina
        $employee1 = Employee::create([
            'name' => 'José Rodríguez',
            'identification_number' => 'V-15632541',
            'base_salary_usd' => 450.00,
            'status' => 'active',
        ]);

        $employee2 = Employee::create([
            'name' => 'María Corina Gómez',
            'identification_number' => 'V-20152436',
            'base_salary_usd' => 600.00,
            'status' => 'active',
        ]);

        // Pagos de nómina (mes actual)
        PayrollPayment::create([
            'employee_id' => $employee1->id,
            'concept' => 'Primera Quincena de Mayo',
            'amount_paid' => 225.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(14),
        ]);

        PayrollPayment::create([
            'employee_id' => $employee2->id,
            'concept' => 'Primera Quincena de Mayo',
            'amount_paid' => 300.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(14),
        ]);

        // 5. Crear Categorías de Gastos y Gastos USD
        $catLogistics = ExpenseCategory::create(['name' => 'Logística y Transporte']);
        $catRent = ExpenseCategory::create(['name' => 'Alquiler Altamira']);
        $catServices = ExpenseCategory::create(['name' => 'Servicios de Internet']);

        Expense::create([
            'expense_category_id' => $catRent->id,
            'description' => 'Pago Alquiler Local Comercial',
            'amount' => 800.00,
            'currency' => 'USD',
            'expense_date' => Carbon::now()->startOfMonth(),
        ]);

        Expense::create([
            'expense_category_id' => $catLogistics->id,
            'description' => 'Reparación de Camión de Despacho',
            'amount' => 350.00,
            'currency' => 'USD',
            'expense_date' => Carbon::now()->startOfMonth()->addDays(5),
        ]);

        Expense::create([
            'expense_category_id' => $catServices->id,
            'description' => 'Fibra Óptica Comercial',
            'amount' => 85.00,
            'currency' => 'USD',
            'expense_date' => Carbon::now()->startOfMonth()->addDays(10),
        ]);

        // 6. Registrar Ventas (USD) para alimentar Dashboard
        // Venta Pagada
        Sale::create([
            'client_name' => 'Piscinas del Este C.A.',
            'total_amount' => 4500.00,
            'currency' => 'USD',
            'status' => 'paid',
            'date' => Carbon::now()->startOfMonth()->addDays(2),
        ]);

        // Ventas a Crédito (alimentará Credits en cascada automáticamente por el boot Hook!)
        $saleCredit = Sale::create([
            'client_name' => 'Inversiones AquaClean C.A.',
            'total_amount' => 3200.00,
            'currency' => 'USD',
            'status' => 'credit',
            'date' => Carbon::now()->startOfMonth()->addDays(4),
        ]);

        // Abono parcial a esta venta a crédito para probar que los abonos restan el crédito automáticamente
        $credit = Credit::where('sale_id', $saleCredit->id)->first();
        if ($credit) {
            CreditPayment::create([
                'credit_id' => $credit->id,
                'amount_paid' => 1200.00,
                'payment_date' => Carbon::now()->startOfMonth()->addDays(10),
                'payment_method' => 'Transferencia Zelle',
            ]);
        }

        // Otra Venta a Crédito totalmente activa (deuda completa)
        Sale::create([
            'client_name' => 'Hotel Hesperia Guaparo',
            'total_amount' => 9500.00,
            'currency' => 'USD',
            'status' => 'credit',
            'date' => Carbon::now()->startOfMonth()->addDays(6),
        ]);

        // 7. Pagos de Impuestos de Prueba
        TaxPayment::create([
            'tax_name' => 'Declaración IVA Abril',
            'amount' => 1450.00,
            'currency' => 'USD',
            'payment_date' => Carbon::now()->startOfMonth()->addDays(12),
            'reference_number' => 'TX-982315',
        ]);
    }
}
