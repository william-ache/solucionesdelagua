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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->default('Admin'); // By default Admin as auth may not be fully enforced yet
            $table->string('action'); // Crear, Editar, Eliminar
            $table->string('module'); // Clientes, Ventas, etc
            $table->text('description'); // Detalles de la acción
            $table->timestamps(); // Provides created_at (Fecha y Hora)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
