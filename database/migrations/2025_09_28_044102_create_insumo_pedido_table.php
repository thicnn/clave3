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
    Schema::create('insumo_pedido', function (Blueprint $table) {
        // Conexión con la tabla de insumos
        $table->foreignId('insumo_id')->constrained('insumos');

        // Conexión con la tabla de pedidos
        $table->foreignId('pedido_id')->constrained('pedidos');

        // Columna extra para guardar datos sobre la relación
        $table->integer('cantidad');

        // Definimos una clave primaria compuesta para evitar duplicados
        $table->primary(['insumo_id', 'pedido_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumo_pedido');
    }
};
