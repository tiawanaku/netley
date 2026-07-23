<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caso extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'cliente_id',
        'abogado_id',
        'procurador_id',
        'especialidad',
        'tipo',
        'juzgado',
        'estado',
        'prioridad',
        'fecha_inicio',
        'fecha_fin',
        // CU-010 Completar Ficha del Caso
        'juez',
        'secretario',
        'fiscal',
        'demandante',
        'demandado',
        'numero_expediente',
        'nurej',
        'delito',
        'materia',
        'tribunal',
        'telefonos',
        'correos',
        'direccion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    /**
     * CU-009 Crear Caso Jurídico: código autogenerado si no se especifica uno.
     */
    protected static function booted(): void
    {
        static::created(function (Caso $caso) {
            if (! $caso->codigo) {
                $caso->updateQuietly([
                    'codigo' => 'CASO-'.$caso->fecha_inicio->format('Y').'-'.str_pad($caso->id, 4, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }

    public function procurador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procurador_id');
    }
}
