<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actuacion extends Model
{
    protected $table = 'actuaciones';

    protected $fillable = [
        'caso_id',
        'responsable_id',
        'tipo',
        'descripcion',
        'fecha',
        'hora',
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

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }
}
