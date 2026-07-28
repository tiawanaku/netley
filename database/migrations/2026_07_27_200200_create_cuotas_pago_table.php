<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Plan de pagos de un Caso: cada cuota representa un mes. La cantidad de cuotas no debe
     * exceder Caso::tiempo_proceso_meses (validado en el formulario, no a nivel de BD).
     */
    public function up(): void
    {
        Schema::create('cuotas_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas_pago');
    }
};
