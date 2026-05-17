<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credit_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_id')->constrained('credits')->onDelete('cascade');
            $table->decimal('amount_paid', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->comment('Efectivo, Transferencia, Pago Móvil, Zelle');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_payments');
    }
};
