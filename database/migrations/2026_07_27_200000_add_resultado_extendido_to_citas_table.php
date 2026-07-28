<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            // Determinación tomada en la reunión: 'Ejecutivo', 'Cerrar Caso', ambas o ninguna.
            $table->json('determinacion')->nullable();
            $table->boolean('pendiente')->default(false);
            $table->date('pendiente_llamar_fecha')->nullable();
            $table->time('pendiente_llamar_hora')->nullable();
            $table->time('hora_inicio_reunion')->nullable();
            $table->time('hora_fin_reunion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn([
                'determinacion', 'pendiente', 'pendiente_llamar_fecha', 'pendiente_llamar_hora',
                'hora_inicio_reunion', 'hora_fin_reunion',
            ]);
        });
    }
};
