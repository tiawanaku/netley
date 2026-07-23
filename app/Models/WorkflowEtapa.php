<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowEtapa extends Model
{
    protected $fillable = [
        'caso_id',
        'nombre',
        'orden',
        'fecha_estimada',
        'fecha_real',
        'completada',
        'es_original',
    ];

    protected function casts(): array
    {
        return [
            'fecha_estimada' => 'date',
            'fecha_real' => 'date',
            'completada' => 'boolean',
            'es_original' => 'boolean',
        ];
    }

    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class);
    }
}
