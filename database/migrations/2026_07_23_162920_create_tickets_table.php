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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->unique()->constrained('consultas');
            $table->foreignId('asignado_a')->nullable()->constrained('users');
            $table->enum('estado', ['Pendiente', 'Asignado', 'En Atención', 'Cerrado'])->default('Pendiente');
            $table->enum('prioridad', ['Baja', 'Normal', 'Alta', 'Urgente'])->default('Normal');
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
