<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consulta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'nombre',
        'email',
        'telefono',
        'descripcion',
        'origen',
    ];

    protected static function booted(): void
    {
        // RN-001: toda consulta genera exactamente un Ticket.
        // RN-004/RN-005: prioridad "Normal" y estado "Pendiente" ya son los defaults de la tabla tickets.
        static::created(function (Consulta $consulta) {
            $consulta->ticket()->create([]);
        });
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }
}
