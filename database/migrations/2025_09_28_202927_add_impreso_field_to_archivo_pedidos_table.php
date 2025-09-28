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
        Schema::table('archivo_pedidos', function (Blueprint $table) {
            $table->boolean('impreso')->default(false)->after('ruta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archivo_pedidos', function (Blueprint $table) {
            $table->dropColumn('impreso');
        });
    }
};
