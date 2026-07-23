<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conciliacion extends Model
{
    protected $table = 'conciliaciones';

    protected $fillable = [
        'caso_id',
        'lugar',
        'fecha',
        'participantes',
        'resultado',
        'acuerdos',
        'documentacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class);
    }
}
