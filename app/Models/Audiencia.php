<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audiencia extends Model
{
    protected $fillable = [
        'caso_id',
        'tipo',
        'tribunal',
        'juez',
        'fecha',
        'hora',
        'sala',
        'participantes',
        'observaciones',
        'resultado',
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
