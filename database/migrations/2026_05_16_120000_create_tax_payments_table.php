<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_payments', function (Blueprint $table) {
            $table->id();
            $table->string('tax_name')->comment('Nombre del impuesto (IVA, ISLR, etc.)');
            $table->decimal('amount', 15, 2)->comment('Monto pagado');
            $table->enum('currency', ['USD', 'VES'])->comment('Moneda de pago original');
            $table->date('payment_date')->comment('Fecha del pago del impuesto');
            $table->string('reference_number')->nullable()->comment('Número de referencia de la transferencia/pago');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tax_payments');
    }
};
