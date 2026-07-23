<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'consulta_id',
        'asignado_a',
        'estado',
        'prioridad',
        'fecha_asignacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function contactos(): HasMany
    {
        return $this->hasMany(Contacto::class);
    }

    /**
     * CU-002: asigna el ticket al siguiente profesional disponible (FIFO manual desde el panel).
     * RN-012: toda reasignación queda registrada vía el audit trail de Filament Shield/activitylog.
     */
    public function asignarA(User $user): void
    {
        $this->update([
            'asignado_a' => $user->id,
            'estado' => 'Asignado',
            'fecha_asignacion' => now(),
        ]);
    }
}
