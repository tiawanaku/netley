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
        Schema::table('consultas', function (Blueprint $table) {
            $table->enum('forma_ingreso', ['Netley', 'Social', 'Psicología', 'Taller', 'Otros'])
                ->default('Netley')
                ->after('origen');
            $table->string('colegio_u_otro')->nullable()->after('forma_ingreso');
            $table->string('ciudad')->nullable()->after('colegio_u_otro');
            $table->string('tipo_proceso')->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn(['forma_ingreso', 'colegio_u_otro', 'ciudad', 'tipo_proceso']);
        });
    }
};
