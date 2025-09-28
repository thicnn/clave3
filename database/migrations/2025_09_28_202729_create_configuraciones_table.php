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
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        // Insertar configuraciones por defecto
        DB::table('configuraciones')->insert([
            [
                'clave' => 'margen_ganancia_default',
                'valor' => '1.5',
                'descripcion' => 'Margen de ganancia por defecto (1.5 = 50% de ganancia)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'factor_costo_operativo',
                'valor' => '0.2',
                'descripcion' => 'Factor de costo operativo (0.2 = 20% adicional)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'clave' => 'precio_minimo_por_hoja',
                'valor' => '0.1',
                'descripcion' => 'Precio mínimo por hoja impresa',
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
        Schema::dropIfExists('configuraciones');
    }
};