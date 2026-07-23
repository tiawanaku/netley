<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hitos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->foreignId('responsable_id')->nullable()->constrained('users');
            $table->string('nombre');
            $table->date('fecha_prevista');
            $table->date('fecha_real')->nullable();
            $table->string('resultado')->nullable();
            $table->text('comentario')->nullable();
            // CU-017 Registrar Retraso Procesal: justificación de incumplimiento del hito.
            $table->enum('causa_retraso', [
                'Retraso judicial', 'Retraso del cliente', 'Falta de documentos', 'Huelga',
                'Vacación judicial', 'Falta de pago', 'Investigación', 'Fuerza mayor', 'Otro',
            ])->nullable();
            $table->text('explicacion_retraso')->nullable();
            $table->string('evidencia_retraso')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hitos');
    }
};
