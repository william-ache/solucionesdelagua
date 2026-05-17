<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_closures', function (Blueprint $table) {
            $table->id();
            
            // Período
            $table->date('date');
            $table->integer('month');
            $table->integer('year');
            
            // Tasas de Cambio
            $table->decimal('rate_bcv', 15, 4)->comment('Tasa Oficial BCV');
            $table->decimal('rate_usdt', 15, 4)->comment('Tasa USDT/Paralelo');
            
            // Fondos
            $table->decimal('initial_amount_usd', 15, 2)->default(0)->comment('Efectivo USD al iniciar');
            $table->decimal('initial_amount_ves', 15, 2)->default(0)->comment('Efectivo VES al iniciar');
            
            // Ventas
            $table->decimal('total_sales_usd', 15, 2)->default(0);
            $table->decimal('total_sales_bs', 15, 2)->default(0);
            
            // Auditoria (Efectivo y cuadre)
            $table->decimal('audited_usd', 15, 2)->default(0)->comment('Efectivo USD entregado');
            $table->decimal('audited_ves', 15, 2)->default(0)->comment('Efectivo VES entregado');
            
            // Diferencias (Sobrantes/Faltantes)
            $table->decimal('difference_usd', 15, 2)->default(0);
            $table->decimal('difference_ves', 15, 2)->default(0);
            
            // Estatus
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('closed_at')->nullable();
            
            // Logística
            $table->unsignedBigInteger('opened_by_user_id')->nullable();
            $table->unsignedBigInteger('closed_by_user_id')->nullable();

            $table->timestamps();

            // Índices para rapidez en históricos
            $table->index(['month', 'year']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_closures');
    }
};
