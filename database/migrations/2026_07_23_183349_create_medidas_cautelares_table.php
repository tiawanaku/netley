<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medidas_cautelares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->foreignId('responsable_id')->nullable()->constrained('users');
            $table->string('tipo');
            $table->date('fecha');
            $table->enum('estado', ['Solicitada', 'Vigente', 'Levantada', 'Vencida'])->default('Solicitada');
            $table->date('vigencia_hasta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medidas_cautelares');
    }
};
