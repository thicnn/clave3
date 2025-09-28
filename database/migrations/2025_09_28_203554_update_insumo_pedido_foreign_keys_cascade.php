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
        Schema::table('insumo_pedido', function (Blueprint $table) {
            // Eliminar las restricciones existentes
            $table->dropForeign(['insumo_id']);
            $table->dropForeign(['pedido_id']);
            
            // Recrear con eliminación en cascada
            $table->foreign('insumo_id')->references('id')->on('insumos')->onDelete('cascade');
            $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumo_pedido', function (Blueprint $table) {
            // Eliminar las restricciones con cascada
            $table->dropForeign(['insumo_id']);
            $table->dropForeign(['pedido_id']);
            
            // Recrear sin cascada (comportamiento original)
            $table->foreign('insumo_id')->references('id')->on('insumos');
            $table->foreign('pedido_id')->references('id')->on('pedidos');
        });
    }
};