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
        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->enum('categoria', ['servicio', 'impresion', 'fotocopia']);
            $table->foreignId('maquina_id')->nullable()->constrained('maquinas')->onDelete('set null');
            $table->foreignId('insumo_id')->nullable()->constrained('insumos')->onDelete('set null');
            $table->enum('tipo_impresion', ['b&n', 'color'])->nullable();
            $table->string('descripcion_servicio')->nullable();
            $table->integer('cantidad');
            $table->boolean('es_doble_faz')->default(false);
            $table->decimal('descuento_item', 5, 2)->default(0)->comment('Descuento en % (0-100)');
            $table->decimal('precio_venta_item', 10, 2);
            $table->decimal('costo_produccion_item', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_items');
    }
};