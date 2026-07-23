<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resoluciones_judiciales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->string('tipo');
            $table->string('numero')->nullable();
            $table->date('fecha');
            $table->string('tribunal')->nullable();
            $table->text('resultado')->nullable();
            $table->string('adjunto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resoluciones_judiciales');
    }
};
