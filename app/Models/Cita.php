<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $fillable = [
        'cliente_id',
        'consulta_id',
        'fecha_hora',
        'modalidad',
        'estado',
        'notas',
        // CU-005 Registrar Resultado de la Cita
        'diagnostico_preliminar',
        'tipo_servicio',
        'observaciones',
        'costo',
        'riesgo',
        'recomendaciones',
        'deriva_a',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }
}
