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
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Nombre de la máquina (ej: "bh227", "c454e")
            $table->decimal('costo_bn_carilla', 8, 2); // Costo por carilla en B&N
            $table->decimal('costo_color_carilla', 8, 2)->nullable(); // Costo por carilla a color (NULL si no aplica)
            $table->boolean('activa')->default(true); // Si la máquina está disponible
            $table->text('descripcion')->nullable(); // Descripción opcional
            $table->timestamps();
        });

        // Insertar máquinas por defecto
        DB::table('maquinas')->insert([
            [
                'nombre' => 'bh227',
                'costo_bn_carilla' => 0.84,
                'costo_color_carilla' => null,
                'activa' => true,
                'descripcion' => 'Impresora B&N Konica Minolta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'c454e',
                'costo_bn_carilla' => 0.95,
                'costo_color_carilla' => 1.20,
                'activa' => true,
                'descripcion' => 'Impresora Color Konica Minolta',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};