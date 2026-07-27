<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Cambia 'after' de 'nombre' a 'nombres'
            $table->string('apellido_paterno')->nullable()->after('nombres');
            $table->string('apellido_materno')->nullable()->after('apellido_paterno');
            $table->string('ci')->nullable()->after('apellido_materno');
            $table->string('genero')->nullable()->after('ci');
            $table->date('fecha_nacimiento')->nullable()->after('genero');
            $table->string('nacionalidad')->nullable()->after('fecha_nacimiento');
            $table->string('estado_civil')->nullable()->after('nacionalidad');
            $table->string('profesion')->nullable()->after('estado_civil');
            $table->string('direccion')->nullable()->after('profesion');
            $table->string('ciudad')->nullable()->after('direccion');
            $table->string('whatsapp')->nullable()->after('telefono');
            $table->string('numero_contrato')->nullable()->after('whatsapp');
            $table->string('estado')->nullable()->after('numero_contrato');
            $table->string('foto')->nullable()->after('estado');
            $table->text('nota_netley')->nullable()->after('foto');
            $table->date('fecha_de_inicio')->nullable()->after('nota_netley');
            $table->string('rol_empresa')->nullable()->after('es_preferente');
            $table->foreignId('user_id')->nullable()->after('rol_empresa')->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'apellido_paterno',
                'apellido_materno',
                'ci',
                'genero',
                'fecha_nacimiento',
                'nacionalidad',
                'estado_civil',
                'profesion',
                'direccion',
                'ciudad',
                'whatsapp',
                'numero_contrato',
                'estado',
                'foto',
                'nota_netley',
                'fecha_de_inicio',
                'rol_empresa',
                'user_id',
            ]);
        });
    }
};