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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('categoria', ['servicio', 'impresion', 'fotocopia']);
            $table->foreignId('maquina_id')->nullable()->constrained('maquinas')->onDelete('set null');
            $table->enum('tipo_impresion', ['b&n', 'color'])->nullable();
            $table->foreignId('insumo_id')->nullable()->constrained('insumos')->onDelete('set null');
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('costo_produccion', 10, 2); // Calculado automáticamente
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
