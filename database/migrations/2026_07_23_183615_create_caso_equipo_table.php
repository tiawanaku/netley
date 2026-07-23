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
        Schema::create('caso_equipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->foreignId('user_id')->constrained('users');
            // El abogado patrocinador y el procurador principal ya son columnas directas en `casos`
            // (CU-008 Asignar Caso). Esta tabla cubre a los demas miembros del equipo (RN-021: un
            // caso puede tener varios colaboradores).
            $table->enum('rol', ['Pasante', 'Psicólogo', 'Conciliador', 'Colaborador']);
            $table->timestamps();
            $table->unique(['caso_id', 'user_id', 'rol']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caso_equipo');
    }
};
