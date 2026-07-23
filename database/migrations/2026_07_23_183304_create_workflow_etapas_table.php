<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_etapas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->string('nombre');
            $table->unsignedInteger('orden');
            $table->date('fecha_estimada')->nullable();
            $table->date('fecha_real')->nullable();
            $table->boolean('completada')->default(false);
            $table->boolean('es_original')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_etapas');
    }
};
