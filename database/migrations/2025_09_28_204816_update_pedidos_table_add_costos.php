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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('costo_total_venta', 10, 2)->after('total')->comment('Precio final que paga el cliente');
            $table->decimal('costo_produccion_total', 10, 2)->after('costo_total_venta')->comment('Suma de costos de producción de todos los ítems');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['costo_total_venta', 'costo_produccion_total']);
        });
    }
};