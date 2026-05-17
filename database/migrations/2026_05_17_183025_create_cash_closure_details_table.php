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
        Schema::create('cash_closure_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_closure_id')->constrained()->onDelete('cascade');
            
            // e.g. "Cash USD", "Cash VES", "Zelle", "POS Mercantil", "Pago Movil"
            $table->string('payment_method');
            
            // Category: Income, Expense, Credit_Given, Credit_Paid
            $table->enum('type', ['income', 'expense', 'credit_given', 'credit_paid'])->default('income');
            
            $table->decimal('amount_usd', 15, 2)->default(0);
            $table->decimal('amount_ves', 15, 2)->default(0);
            
            $table->string('reference_number')->nullable();
            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_closure_details');
    }
};
