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
    Schema::create('clientes', function (Blueprint $table) {
        $table->id(); // ID único para cada cliente (1, 2, 3...)
        $table->string('nombre'); // Nombre del cliente o empresa
        $table->string('email')->unique(); // Email (único para que no se repitan)
        $table->string('telefono')->nullable(); // Teléfono (opcional)
        $table->string('rut')->nullable(); // RUT (opcional)
        $table->text('direccion')->nullable(); // Dirección (opcional)
        $table->timestamps(); // Campos 'created_at' y 'updated_at' automáticamente
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
