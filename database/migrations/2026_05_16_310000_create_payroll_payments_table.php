<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('concept')->comment('Ejemplo: primera quincena, bono de desempeño');
            $table->decimal('amount_paid', 15, 2);
            $table->date('payment_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_payments');
    }
};
