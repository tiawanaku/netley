<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hito extends Model
{
    protected $fillable = [
        'caso_id',
        'responsable_id',
        'nombre',
        'fecha_prevista',
        'fecha_real',
        'resultado',
        'comentario',
        // CU-017 Registrar Retraso Procesal
        'causa_retraso',
        'explicacion_retraso',
        'evidencia_retraso',
    ];

    protected function casts(): array
    {
        return [
            'fecha_prevista' => 'date',
            'fecha_real' => 'date',
        ];
    }

    /**
     * CU-017: el KPI del abogado no disminuye cuando existe una justificación válida de retraso.
     */
    public function getEstaRetrasadoJustificadoAttribute(): bool
    {
        return filled($this->causa_retraso);
    }

    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
