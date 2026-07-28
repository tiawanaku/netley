<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    protected $fillable = [
        'caso_id',
        'actuacion_id',
        'propietario_id',
        'nombre',
        'tipo',
        'formato',
        'archivo',
        'version',
        'tamano',
        'hash',
        'permisos',
    ];

    /**
     * CU-018: todo documento posee tamaño y hash, calculados automáticamente a partir del archivo.
     */
    protected static function booted(): void
    {
        static::saving(function (Documento $documento) {
            if ($documento->isDirty('archivo') && Storage::disk('public')->exists($documento->archivo)) {
                $documento->tamano = Storage::disk('public')->size($documento->archivo);
                $documento->hash = hash_file('sha256', Storage::disk('public')->path($documento->archivo));
            }
        });
    }

    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class);
    }

    public function actuacion(): BelongsTo
    {
        return $this->belongsTo(Actuacion::class);
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }
}
