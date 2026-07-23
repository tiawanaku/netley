<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedidaCautelar extends Model
{
    protected $table = 'medidas_cautelares';

    protected $fillable = [
        'caso_id',
        'responsable_id',
        'tipo',
        'fecha',
        'estado',
        'vigencia_hasta',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'vigencia_hasta' => 'date',
        ];
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
