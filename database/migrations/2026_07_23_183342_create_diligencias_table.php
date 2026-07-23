<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diligencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->enum('tipo', ['Notificación', 'Citación', 'Embargo', 'Inspección', 'Allanamiento', 'Entrega', 'Otro']);
            $table->date('fecha');
            $table->string('resultado')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diligencias');
    }
};
