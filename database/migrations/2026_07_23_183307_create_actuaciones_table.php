<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actuaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caso_id')->constrained('casos');
            $table->foreignId('responsable_id')->nullable()->constrained('users');
            $table->enum('tipo', [
                'Presentación de memorial', 'Audiencia', 'Reunión', 'Llamada',
                'Conciliación', 'Revisión documental', 'Notificación', 'Recurso',
                'Citación', 'Apelación',
            ]);
            $table->text('descripcion');
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actuaciones');
    }
};
