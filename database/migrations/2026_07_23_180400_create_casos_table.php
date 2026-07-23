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
        Schema::create('casos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable()->unique();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('abogado_id')->nullable()->constrained('users');
            $table->foreignId('procurador_id')->nullable()->constrained('users');
            $table->string('especialidad');
            $table->string('tipo')->nullable();
            $table->string('juzgado')->nullable();
            $table->enum('estado', ['Abierto', 'En Proceso', 'Suspendido', 'Cerrado'])->default('Abierto');
            $table->enum('prioridad', ['Baja', 'Normal', 'Alta', 'Urgente'])->default('Normal');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            // CU-010 Completar Ficha del Caso
            $table->string('juez')->nullable();
            $table->string('secretario')->nullable();
            $table->string('fiscal')->nullable();
            $table->string('demandante')->nullable();
            $table->string('demandado')->nullable();
            $table->string('numero_expediente')->nullable();
            $table->string('nurej')->nullable();
            $table->string('delito')->nullable();
            $table->string('materia')->nullable();
            $table->string('tribunal')->nullable();
            $table->string('telefonos')->nullable();
            $table->string('correos')->nullable();
            $table->string('direccion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casos');
    }
};
