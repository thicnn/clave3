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
    Schema::create('insumos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); // Ej: "Papel Couché 150g", "Tinta Negra Konica"
        $table->text('descripcion')->nullable(); // Descripción opcional
        $table->decimal('costo', 8, 2); // Costo por unidad (ej. por hoja, por ml)
        $table->integer('stock_actual'); // Cantidad que tienes ahora
        $table->integer('stock_minimo')->default(0); // Umbral para alertas
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
