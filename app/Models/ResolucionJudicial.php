<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResolucionJudicial extends Model
{
    protected $table = 'resoluciones_judiciales';

    protected $fillable = [
        'caso_id',
        'tipo',
        'numero',
        'fecha',
        'tribunal',
        'resultado',
        'adjunto',
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
