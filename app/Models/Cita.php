<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    protected $fillable = [
        'cliente_id',
        'consulta_id',
        'asignado_a_user_id',
        'fecha_hora',
        'modalidad',
        'estado',
        'notas',
        // CU-005 Registrar Resultado de la Cita
        'diagnostico_preliminar',
        'tipo_servicio',
        'observaciones',
        'costo',
        'riesgo',
        'recomendaciones',
        'deriva_a',
        // Resultado extendido (registrado desde "Reuniones Agendadas")
        'determinacion',
        'pendiente',
        'pendiente_llamar_fecha',
        'pendiente_llamar_hora',
        'hora_inicio_reunion',
        'hora_fin_reunion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
            'determinacion' => 'array',
            'pendiente' => 'boolean',
            'pendiente_llamar_fecha' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    /**
     * Miembro del personal (Abogado, Psicólogo, Pasante, etc.) al que el administrador
     * asignó esta cita. Es quien la verá en su bandeja "Reuniones Agendadas".
     */
    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a_user_id');
    }
}
