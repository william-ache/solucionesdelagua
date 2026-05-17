<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense'])->comment('Tipo de transacción: Ingreso o Egreso');
            $table->decimal('amount_usd', 15, 2)->comment('Monto en Dólares');
            $table->decimal('amount_ves', 15, 2)->comment('Monto en Bolívares');
            $table->foreignId('exchange_rate_id')->constrained('exchange_rates')->onDelete('restrict')->comment('Relación con tasa de cambio');
            $table->text('description')->nullable();
            $table->date('date')->comment('Fecha de la transacción');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
