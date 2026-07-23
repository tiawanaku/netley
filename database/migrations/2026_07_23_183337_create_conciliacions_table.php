<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conciliaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->string('lugar')->nullable();
            $table->date('fecha');
            $table->string('participantes')->nullable();
            $table->text('resultado')->nullable();
            $table->text('acuerdos')->nullable();
            $table->text('documentacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conciliaciones');
    }
};
