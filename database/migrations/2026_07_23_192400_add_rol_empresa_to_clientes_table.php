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
        Schema::table('clientes', function (Blueprint $table) {
            // Rol que esta persona cumple dentro de la empresa (si es null, es solo un cliente).
            // Al asignarle un rol se le genera automáticamente un usuario/contraseña para entrar al panel.
            $table->enum('rol_empresa', ['Abogado', 'Psicólogo', 'Procurador', 'Trabajo Social', 'Pasante', 'Otros'])
                ->nullable()
                ->after('es_preferente');
            $table->foreignId('user_id')->nullable()->after('rol_empresa')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('rol_empresa');
        });
    }
};
