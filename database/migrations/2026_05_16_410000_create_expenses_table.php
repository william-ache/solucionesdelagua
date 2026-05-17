<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->onDelete('restrict');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('currency', ['USD', 'VES']);
            $table->date('expense_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};
