<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->foreignId('actuacion_id')->nullable()->constrained('actuaciones');
            $table->foreignId('propietario_id')->constrained('users');
            $table->string('nombre');
            $table->enum('tipo', [
                'Memorial', 'Poder', 'Demanda', 'Contestación', 'Sentencia',
                'Audio', 'Video', 'Imagen', 'PDF', 'Oficio', 'Resolución',
            ]);
            $table->string('archivo');
            $table->unsignedInteger('version')->default(1);
            $table->unsignedBigInteger('tamano')->nullable();
            $table->string('hash')->nullable();
            $table->enum('permisos', ['Solo abogado', 'Abogado y cliente', 'Público'])->default('Solo abogado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
