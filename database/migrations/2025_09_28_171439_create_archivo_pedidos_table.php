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
    Schema::create('archivo_pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
        $table->string('nombre_original'); // El nombre que tenía el archivo en la PC del usuario
        $table->string('ruta'); // La ruta donde guardaremos el archivo en nuestro servidor
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivo_pedidos');
    }
};
