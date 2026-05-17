<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 15, 4)->comment('Tasa de cambio VES/USD');
            $table->date('date')->unique()->comment('Fecha de vigencia de la tasa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
};
