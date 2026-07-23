<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audiencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->string('tipo');
            $table->string('tribunal')->nullable();
            $table->string('juez')->nullable();
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->string('sala')->nullable();
            $table->string('participantes')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('resultado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audiencias');
    }
};
