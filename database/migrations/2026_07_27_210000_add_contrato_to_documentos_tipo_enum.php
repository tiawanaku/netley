<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TIPOS_ORIGINALES = [
        'Memorial', 'Poder', 'Demanda', 'Contestación', 'Sentencia',
        'Audio', 'Video', 'Imagen', 'PDF', 'Oficio', 'Resolución',
    ];

    private const TIPOS_CON_CONTRATO = [...self::TIPOS_ORIGINALES, 'Contrato'];

    public function up(): void
    {
        $this->redefinirEnum(self::TIPOS_CON_CONTRATO);
    }

    public function down(): void
    {
        $this->redefinirEnum(self::TIPOS_ORIGINALES);
    }

    /**
     * @param  array<int, string>  $tipos
     */
    private function redefinirEnum(array $tipos): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite no soporta ALTER ... MODIFY; se recrea la columna (sin doctrine/dbal).
            Schema::table('documentos', function (Blueprint $table) {
                $table->dropColumn('tipo');
            });
            Schema::table('documentos', function (Blueprint $table) use ($tipos) {
                $table->enum('tipo', $tipos)->after('actuacion_id');
            });

            return;
        }

        $lista = collect($tipos)->map(fn (string $tipo) => "'{$tipo}'")->implode(', ');
        DB::statement("ALTER TABLE documentos MODIFY tipo ENUM({$lista})");
    }
};
