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
        Schema::table('citas', function (Blueprint $table) {
            $table->text('diagnostico_preliminar')->nullable();
            $table->string('tipo_servicio')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('costo', 10, 2)->nullable();
            $table->enum('riesgo', ['Bajo', 'Medio', 'Alto'])->nullable();
            $table->text('recomendaciones')->nullable();
            $table->enum('deriva_a', ['Legal', 'Psicología', 'Conciliación', 'Otro'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn([
                'diagnostico_preliminar', 'tipo_servicio', 'observaciones',
                'costo', 'riesgo', 'recomendaciones', 'deriva_a',
            ]);
        });
    }
};
