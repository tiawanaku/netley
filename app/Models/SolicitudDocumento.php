<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudDocumento extends Model
{
    protected $fillable = [
        'caso_id',
        'documento_id',
        'descripcion',
        'fecha_limite',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_limite' => 'date',
        ];
    }

    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class);
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class);
    }
}
