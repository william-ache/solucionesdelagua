<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Clientes
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_id')->unique()->comment('RIF/Cédula');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('balance', 15, 2)->default(0)->comment('Saldo deudor/acreedor');
            $table->timestamps();
        });

        // Transacciones de Clientes
        Schema::create('client_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['invoice', 'credit_note', 'payment']);
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Cuentas Bancarias
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_type');
            $table->string('account_number')->unique();
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // Movimientos Bancarios
        Schema::create('bank_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']);
            $table->string('category')->comment('sales, supplier_payment, payroll, taxes, operational');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable();
            $table->timestamps();
        });

        // Proveedores
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document_id')->unique()->comment('RIF/Cédula');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('balance', 15, 2)->default(0)->comment('Cuentas por pagar');
            $table->timestamps();
        });

        // Facturas de Proveedores
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('bank_movements');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('client_transactions');
        Schema::dropIfExists('clients');
    }
};
