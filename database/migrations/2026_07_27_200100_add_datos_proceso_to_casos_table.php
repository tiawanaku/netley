<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->unsignedInteger('tiempo_proceso_meses')->nullable();
            $table->boolean('requiere_poder')->default(false);
            $table->decimal('monto_iguala_profesional', 10, 2)->nullable();
            $table->decimal('monto_comision', 10, 2)->nullable();
            $table->decimal('comision_porcentaje', 5, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('casos', function (Blueprint $table) {
            $table->dropColumn([
                'tiempo_proceso_meses', 'requiere_poder', 'monto_iguala_profesional',
                'monto_comision', 'comision_porcentaje',
            ]);
        });
    }
};
